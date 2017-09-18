@php
  $start_time  = \Carbon\Carbon::parse($event->start_date);
  $end_time    = \Carbon\Carbon::parse($event->end_date) ;
  $cur_time    = \Carbon\Carbon::now();
  $offset_start = ( $cur_time < $start_time ) ? $cur_time->diffInSeconds($start_time) * 1000 : 0;
  $offset_end   = ( $cur_time < $end_time ) ? $cur_time->diffInSeconds($end_time) * 1000 : 0;
  $checked = "checked";
  $updateScore = session('updateScore');
  if(session('updateScore') == false){
      $checked = "";
  }
  $isRunning = 0;
  if($cur_time >= $start_time)
    $isRunning = 1;
@endphp
@extends('master')
@section('title', ( isset($event->home_team_id) && isset($event->away_team_id) && !empty($event->away_team_id) && !empty($event->home_team_id))?$event->home_team.' vs. '.$event->away_team.' streams - ' : @$event->event_title)

@section('headScripts')
  <link rel="stylesheet" href="{{ secure_asset('css/streams_comment.min.css') }}">
  <!-- <link rel="stylesheet" href="{{ secure_asset('css/streams.css') }}"> -->

  <!-- <link rel="stylesheet" href="{{ secure_asset('css/comment.css') }}"> -->

  {{-- <script type="text/javascript" src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=gq4mpo5r0xobgamm8pni3pqatgqnal9yolqelhzcvnzmkv7i"></script> --}}

  <script src="{{ secure_asset('js/axios.min.js') }}"></script>
  <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
  <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
  {{--<script src="{{ secure_asset('js/tags.js') }}"></script>--}}
  <script src="{{ secure_asset('js/comment.min.js'.'?'.time()) }}"></script>
  <!-- <script src="{{ secure_asset('js/comment.js'.'?'.time()) }}"></script> -->
  {{--<script src="{{ secure_asset('plugins/clipboard.min.js') }}"></script>--}}
  <script src="{{ secure_asset('plugins/clipboard_tags_min.js') }}"></script>
  {{--<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">--}}
  {{--<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>--}}
  <link href="{{secure_asset('css/bootstrap-toggle.min.css')}}" rel="stylesheet">
  <script src="{{secure_asset('js/bootstrap-toggle.min.js')}}"></script>
@endsection

@section('content')
  <div class="new-result-container">
    <div class="breadcrumbs">
      <div class="container">
        <div><a class="no_underline">{{ $event->nation_name }}</a></div>
        <div><a class="no_underline">{{ $event->competition_name }}</a></div>
        <div><a class="no_underline" href="{{ url()->current() }}"><span>{{ $event->home_team }}</span> vs <span>{{ $event->away_team }}</span></a></div>
        <div><a class="no_underline">TV Channels</a></div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        @if( isset($event->home_team_id) && isset($event->away_team_id) && !empty($event->away_team_id) && !empty($event->home_team_id))
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
          <div class="logo-holder col-md-7">
            @if( file_exists( 'images/teams/small/'.$event->home_team_logo ) )
              <img src="{{ secure_asset('images/teams/'.$event->home_team_logo)}}" alt="{{ $event->home_team }}" class="img-responsive">
            @else
              <img src="{{ secure_asset('images/generic.png')}}" alt="{{ $event->home_team }}" class="img-responsive">
            @endif
            <span></span>
          </div>
          <h2>
            <a href="#">{{ $event->home_team }}</a>
          </h2>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4 visible-sm visible-xs">
          <div class="logo-holder right col-md-7">
            @if( file_exists( 'images/teams/'.$event->away_team_logo ) )
              <img src="{{ secure_asset('images/teams/'.$event->away_team_logo)}}" alt="{{ $event->away_team }}" class="img-responsive">
            @else
              <img src="{{ secure_asset('images/generic.png')}}" alt="{{ $event->away_team }}" class="img-responsive">
            @endif
            <span></span>
          </div>
          <h2>
            <a href="#">{{ $event->away_team }}</a>
          </h2>
        </div>
        @else
          <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4 hidden-sm hidden-xs"></div>
        @endif
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 nopadding">
          <div class="match-info">
            @if( $event->competition_logo && file_exists( 'images/competitions/'.$event->competition_logo ) )
              <img style="max-width: 40px" src="{{ secure_asset('images/competitions/'.$event->competition_logo) }}" alt="{{ $event->competition_name }}"  title="{{ $event->competition_name }}">
            @else
              <img style="max-width: 40px" src="{{ secure_asset('images/generic.png')}}" alt="{{ $event->competition_name }}" title="{{ $event->competition_name }}">
            @endif

            @if(!is_null($event->round_name) && !empty($event->round_name))
              <p>{{ $event->round_name }}</p>
            @elseif(!is_null($event->event_title) && !empty($event->event_title) && $event->event_title != 'NULL')
              <p>{{ $event->event_title }}</p>
            @else
              <br/>
            @endif

            @if(!is_null($event->game_week))
              <p>Week {{ $event->game_week }}</p>
            @else
              <br/>
            @endif
            <p>
              <span>Date: </span> {{ \Carbon\Carbon::parse($event->start_date)->addHours(Session::get('visitorTZ'))->format('jS F Y') }}
              &nbsp;
              <span>Time: </span>
              <span id="eventTime" style="color:#000" data-eventtime="{{ $event->start_date }}">{{ \Carbon\Carbon::parse($event->start_date)->addHours(Session::get('visitorTZ'))->format('H:i') }}</span>
            </p>
            @if(!is_null($event->venue))
              <p><span>Venue: {{ $event->venue }}</span></p>
            @endif
            <input type="hidden" id="updateScore" value="{{ $checked }}">
            @if($isRunning && $event->event_minute != "")
              <p>
                <span class="@if($event->event_minute != "FT") game_minute @endif" id="event_minute" data-id="{{ $event->event_id }}" data-running="{{ $isRunning }}">'{{ $event->event_minute }}</span>
              </p>
              <p>
                <span style="font-size: 140%; color: #000;" id="event_score">{{ $event->event_status }}</span>
              </p>
            @endif

          </div>
          <div class="count match_live" id="real_count" style="display: none; margin-top: 2px;">
            This match is @if($event->event_minute == "FT") ended! @else live! @endif
          </div>
          <div class="count match_end" id="match_end" style="display: none; margin-top: 2px;">
            This match ended!
          </div>
          <div class="count match_start" id="fake_count" style="display: none; margin-top: 2px;">This match starts in:</div>

          <div id="getting-started"></div>
          <div id="getting-finished"></div>
        </div>
        @if( isset($event->home_team_id) && isset($event->away_team_id) && !empty($event->away_team_id) && !empty($event->home_team_id))
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4 hidden-sm hidden-xs">
          <div class="logo-holder right col-md-7">
            @if( file_exists( 'images/teams/small/'.$event->away_team_logo ) )
              <img src="{{ secure_asset('images/teams/'.$event->away_team_logo)}}" alt="{{ $event->away_team }}" class="img-responsive">
            @else
              <img src="{{ secure_asset('images/generic.png')}}" alt="{{ $event->away_team }}" class="img-responsive">
            @endif
            <span></span>
          </div>
          <h2>
            <a href="#">{{ $event->away_team }}</a>
          </h2>
        </div>
       @endif
      </div>
    </div>
    <div class="share-block">
      <div class="container">
        Share this event with your friends:
        <div class="event-social-block">
          <a target="_blank" data-toggle="modal" class="btn fb" href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}">
            <span class="socicon socicon-facebook"></span>
          </a>
          <a target="_blank" data-toggle="modal" class="btn tw" href="https://twitter.com/home?status={{ url()->current() }}">
            <span class="socicon socicon-twitter"></span>
          </a>
          <a target="_blank" data-toggle="modal" class="btn gp" href="https://plus.google.com/share?url={{ url()->current() }}">
            <span class="socicon socicon-googleplus"></span>
          </a>
          <a target="_blank" data-toggle="modal" class="btn gp" href="https://vk.com/share.php?url={{ url()->current() }}">
            <span class="socicon socicon-vkontakte"></span>
          </a>
          <a target="_blank" data-toggle="modal" class="btn tl" href="https://tumblr.com/widgets/share/tool?canonicalUrl={{ url()->current() }}m">
            <span class="socicon socicon-tumblr"></span>
          </a>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="mobile-ipad-view-for-7 col-md-7 filter">
      <form action="" class="form-inline">
        <div class="form-group">
          <label style="margin-top: 9px" for="country_selector" class="mobile-view-competition-label">Country:&nbsp;&nbsp;</label>
            <select  id="country_selector" style="height:25px;padding-top:0;padding-bottom:0;" onchange="myFunction()">
              <option value="All">All</option>
              <?php
                $var1 = json_decode($event->channels, true);
                for ($j=0; $j < count($var1); $j++) {
                  $channelsArray = array();
                  try
                  {
                    $channelsArray = $var1[$j]["channels"];
                  }
                  catch(Exception $e)
                  {
                    continue;
                  }

              ?>
                <option value="{{ $var1[$j]['country'] }}">{{ $var1[$j]['country'] }}</option>
              <?php }?>
            </select>
        </div>
      </form>
    </div>
  </div>
  <div class="tv_channel_table">
    <div class="col-sm-12" data-toggle="collapse" data-parent="#http_events" href="#streams" aria-expanded="true" aria-controls="collapseOne" role="tab" id="panel-comment" style="cursor:pointer;">
      <span class='color-gold'>All TV Channels</span>
      <i class="fa fa-compress pull-right" aria-hidden="true" style="font-size: 1.5em;"></i>
    </div>
    <div class="panel-body collapse in" id="streams">
      <div class="row tv_row">
        <div class="col-xs-6 tvSchedule_header header_text">
           Country
        </div>
        <div class="col-xs-6 tvSchedule_header header_text" >
           Channel
        </div>
      </div>
      <?php
        $var1 = json_decode($event->channels, true);
        for ($j=0; $j < count($var1); $j++) {
          $channelsArray = array();
          try
          {
              $channelsArray = $var1[$j]["channels"];
          }
          catch(Exception $e)
          {
            continue;
          }
      ?>
      <div class="row tv_row_channel" id="tv-hover" >
        <div class="col-xs-6 " >
            <p class="channel_country mobile-view-country" >
              <img src="{{$var1[$j]['flag']}}"></img>&nbsp&nbsp&nbsp
              <span id="country_name"><?php echo $var1[$j]["country"];?></span>
            </p>
        </div>
        <div class="col-xs-6 ">
          <p class=" channel_name" style="padding-top:13px">
          <?php
            for ($k=0; $k < count($channelsArray); $k++){
              if(strpos($channelsArray[$k]["name"], "."))
              {
                continue;
              }
           ?>
           <a style="color:#444" href="{{secure_url('channels/'.$var1[$j]['country'].'/'.$channelsArray[$k]['slug'])}}">
            <?php echo $channelsArray[$k]["name"]; ?>
           </a>
           <?php
              if ($k<count($channelsArray)-1) {
                echo (",  ");
              }
            }
          ?>
          </p>
        </div>
      </div>
      <?php
        }
      ?>
    </div>
  </div>
@endsection
@section('scripts')
  <script src="{{ secure_asset('js/jquery.countdown.min.js') }}"></script>
  <script src="{{ secure_asset('js/streams.min.js') }}"></script>
  <script src="//cdn.jsdelivr.net/alertifyjs/1.9.0/alertify.min.js"></script>

  <!-- CSS -->
  <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/alertify.min.css"/>
  <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/themes/default.min.css"/>
  <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/themes/bootstrap.min.css"/>

  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
  <script src="{{ secure_asset('js/parsley.min.js') }}"></script>

  <style>
    .vpnLogo{
      padding-right: 0px;
      padding-left: 0px;
      text-align: center;
      background-color: #fff;
      padding: 10px;
    }
    .vpnlogo-text{
      font-style: italic;
      padding:5px;
    }
    .vpnlogo-content .img-responsive{
      padding:5px;
    }
  </style>

<script type="text/javascript" src="{{secure_asset('js/channels/channels_first.js')}}"></script>
  @if(\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role>=1)
    <script type="text/javascript" src="{{secure_asset('js/channels/channels_second.js')}}"></script>

  @endif
<script type="text/javascript" src="{{secure_asset('js/channels/channels_final.js')}}"></script>


@endsection
<div class="blurry"></div>
<style>
  .color-gold {
    color: #B3994C;
  }
  .modal{ z-index: 2050 !important}
</style>
<div class="modal fade edit_form" id="edit_form" role="dialog" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Stream</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="streamForm" role="form" method="POST" action="{{ secure_url('updateStream') }}" data-parsley-validate>
                  {{ csrf_field() }}
                  <input type="hidden" name="stream_id" id="stream_id" value=""/>
                  <input type="hidden" name="stream_url" id="stream_url" value="{{Request::url()}}"/>
                  <div class="form-group required">
                    <label for="" class="col-sm-3 control-label">Stream Type</label>
                    <div class="col-sm-9">
                      <select id='type_selector' onchange="selectType(this)" class="form-control selectpicker" name="streamType" data-live-search="true" required title="Choose one of the following...">
                        <option value="http">HTTP</option>
                        <option value="Acestream">Acestream</option>
                        <option value="sopcast">Sopcast</option>
                        <option value="VLC">VLC</option>
                        <option value="Other">Other</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-3 control-label">URL</label>
                    <div class="col-sm-9">
                      <input id="url" type="text" name="url" class="form-control" required placeholder="Stream URL"
                             data-parsley-pattern="(?=(http:\/\/|https:\/\/|acestream:\/\/|sopcast:\/\/)).*"
                             data-parsley-error-message="Please select stream type first"
                             data-href="{{ url('checkBanDomain') }}">
                    </div>
                  </div>
                  <div class="form-group required">
                    <label for="" class="col-sm-3 control-label">Language</label>
                    <div class="col-sm-9">
                      <select class="form-control selectpicker" name="language" id="language_selector" data-live-search="true" required title="Choose one of the following...">
                        @foreach($languages as $language)
                          <option value="{{ $language->language_name }}" data-tokens="{{ $language->language_name }}">
                            {{ $language->language_name }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label for="" class="col-sm-3 control-label">Stream quality</label>
                    <div class="col-sm-9">
                      <select class="form-control selectpicker" name="quality" id="stream_quality" required data-live-search="true" title="Choose one of the following...">
                        <option value="HD">HD</option>
                        <option value="520p">520p</option>
                        <option value="SD">SD</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                      <div class="checkbox">
                        <label>
                          <input name="compatible" id="mobile_compatible" value="1" type="checkbox"> is mobile compatible ?
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label for="" class="col-sm-3 control-label">Number of Ad-overlays</label>
                    <div class="col-sm-9">
                      <select class="form-control selectpicker" name="adNumber" id="adNumber" data-live-search="true" required title="Choose one of the following...">
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">9+</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                      <div class="checkbox">
                        <label>
                          <input name="nsfw" id="nsfw" value="1" type="checkbox"> NSFW ads
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Other Info.</label>
                    <div class="col-sm-9">
                      <input type="text" name="otherInfo" id="otherInfo" class="form-control" maxlength="40" placeholder="Other Information about the stream no more than 40 characters">
                    </div>
                  </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="submitForm" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-error" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
