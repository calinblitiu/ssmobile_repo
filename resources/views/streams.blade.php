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
    @if(Session::get('NightMode') || Session::get('NightMode') == '1' )
      <link rel="stylesheet" href="{{ cdn('css/streams_night.min.css') }}">
    @else
      <link rel="stylesheet" href="{{ cdn('css/streams_comment.min.css') }}">
    @endif
  <link rel="stylesheet" href="{{ cdn('css/editor.min.css') }}">
  {{--<link rel="stylesheet" href="{{ cdn('css/comment.css') }}">--}}
  <script>
    var emojis = {};
    var GitHubEmojiAPICallback = function(resp) {
      window.emojis = resp.data;
    };
  </script>
  <script src="https://api.github.com/emojis?callback=GitHubEmojiAPICallback"></script>
  <script>
    var elements = '';
    var getAlluserUrl = "{{ secure_url('getAlluserUrl') }}";
    $.get(getAlluserUrl, function (data) {
      elements = data;
    });
  </script>

  {{-- <script src="{{ cdn('js/tinymce/tinymce.min.js')}}"></script> --}}

    <script src="{{ secure_asset('js/axios.min.js') }}"></script>
  <script src="{{ cdn('js/autocomplete/dist/jquery.textcomplete.min.js') }}"></script>
  <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
  <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
  {{--<script src="{{ cdn('js/tags.js') }}"></script>--}}
  <script src="{{ cdn('js/comment.min.js') }}"></script>
  {{--<script src="{{ cdn('plugins/clipboard.min.js') }}"></script>--}}
    <script src="{{ secure_asset('plugins/clipboard_tags_min.js') }}"></script>
    <link href="{{secure_asset('css/bootstrap-toggle.min.css')}}" rel="stylesheet">
    <script src="{{secure_asset('js/bootstrap-toggle.min.js')}}"></script>
  {{--<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">--}}
  {{--<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>--}}

@endsection

@section('content')
  <div class="new-result-container">

    <div class="container">
      <div class="row">
        @if( isset($event->home_team_id) && isset($event->away_team_id) && !empty($event->away_team_id) && !empty($event->home_team_id))
        <div class="col-xs-6 col-sm-6 col-md-3 col-md-offset-3 col-lg-2 col-lg-offset-2 visible-sm visible-xs">
          <div class="logo-holder col-md-12">
            @if( file_exists( 'images/teams/small/'.$event->home_team_logo ) )
              <img src="{{ cdn('images/teams/'.$event->home_team_logo)}}" alt="{{ $event->home_team }}" class="img-responsive">
            @else
              <img src="{{ cdn('images/generic.png')}}" alt="{{ $event->home_team }}" class="img-responsive">
            @endif
            <!--span></span-->
          </div>
          <h2>
            <a href="#" class="show-one-line">{{ $event->home_team }}</a>
          </h2>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-2 visible-sm visible-xs">
          <div class="logo-holder col-md-12">
            @if( file_exists( 'images/teams/'.$event->away_team_logo ) )
              <img src="{{ cdn('images/teams/'.$event->away_team_logo)}}" alt="{{ $event->away_team }}" class="img-responsive">
            @else
              <img src="{{ cdn('images/generic.png')}}" alt="{{ $event->away_team }}" class="img-responsive">
            @endif
            <!--span></span-->
          </div>
          <h2>
            <a href="#" class="show-one-line">{{ $event->away_team }}</a>
          </h2>
        </div>
        @else
          <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4 hidden-sm hidden-xs"></div>
        @endif
        <div class="col-xs-12 col-sm-12 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2 nopadding">
          @if( isset($event->home_team_id) && isset($event->away_team_id) && !empty($event->away_team_id) && !empty($event->home_team_id))
          <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 hidden-sm hidden-xs">
            <div class="logo-holder col-md-12">
              @if( file_exists( 'images/teams/small/'.$event->home_team_logo ) )
                <img src="{{ cdn('images/teams/'.$event->home_team_logo)}}" alt="{{ $event->home_team }}" class="img-responsive">
              @else
                <img src="{{ cdn('images/generic.png')}}" alt="{{ $event->home_team }}" class="img-responsive">
              @endif
              <!--span></span-->
            </div>
            <h2>
              <a href="#" class="show-one-line">{{ $event->home_team }}</a>
            </h2>
          </div>
          @else
            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 hidden-sm hidden-xs"></div>
          @endif
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="match-info">
              @if( $event->competition_logo && file_exists( 'images/competitions/'.$event->competition_logo ) )
                <img style="max-width: 40px" src="{{ cdn('images/competitions/'.$event->competition_logo) }}" alt="{{ $event->competition_name }}"  title="{{ $event->competition_name }}">
              @else
                <img style="max-width: 40px" src="{{ cdn('images/generic.png')}}" alt="{{ $event->competition_name }}" title="{{ $event->competition_name }}">
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
                <span><i class="fa fa-calendar" style="margin-right: 2px"></i>  </span> {{ \Carbon\Carbon::parse($event->start_date)->addHours(Session::get('visitorTZ'))->format('jS F Y') }}
                &nbsp;
                <span><i class="fa fa-clock-o" style="margin-right: 2px"></i>  </span>
                <span id="eventTime" style="color:#000" data-eventtime="{{ $event->start_date }}">{{ \Carbon\Carbon::parse($event->start_date)->addHours(Session::get('visitorTZ'))->format('H:i') }}</span>
              </p>
              @if(!is_null($event->venue))
                <p><span><i class="fa fa-map-pin" style="margin-right: 2px"></i>  {{ $event->venue }}</span></p>
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
          <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 hidden-sm hidden-xs">
            <div class="logo-holder col-md-12">
              @if( file_exists( 'images/teams/'.$event->away_team_logo ) )
                <img src="{{ cdn('images/teams/'.$event->away_team_logo)}}" alt="{{ $event->away_team }}" class="img-responsive">
              @else
                <img src="{{ cdn('images/generic.png')}}" alt="{{ $event->away_team }}" class="img-responsive">
              @endif
              <!--span></span-->
            </div>
            <h2>
              <a href="#" class="show-one-line">{{ $event->away_team }}</a>
            </h2>
          </div>
          @else
            <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3 hidden-sm hidden-xs"></div>
          @endif
        </div>
      </div>
    </div>

  </div>
{{-- {{ dd($allStreams->count()) }} --}}
  <div id="http_events" class="panel panel-default panel-collapse">
    <div class="panel-heading" id="panel-comment" style="padding: 5px 15px">
      <span class='color-gold'>Live Streams (@if($allStreams->count()){{$allStreams->count()}}@else 0 @endif)</span>
      <div class="form-inline pull-right">
        <div class="filter">
          <label for="type-filter" class="control-label">Type </label>
          <select class="form-control" id="type-filter" style="height:34px;">
            <option value="">All</option>
            @foreach($streamTypes as $type)
              <option value="{{ strtoupper($type) }}">{{ $type }}</option>
            @endforeach
          </select>
        </div>
        <div class="filter">
          <label for="quality-filter" class="control-label">Quality </label>
          <select class="form-control" id="quality-filter" style="height:34px;">
            <option value="">All</option>
            @foreach($streamQuality as $type)
              <option value="{{ strtoupper($type) }}">{{ $type }}</option>
            @endforeach
          </select>
        </div>
        <div class="filter">
          <label for="language-filter" class="control-label">Language </label>
          <select class="form-control" id="language-filter" style="height:34px;">
            <option value="">All</option>
            @foreach($streamLanguage as $type)
              <option value="{{ strtoupper($type) }}">{{ $type }}</option>
            @endforeach
          </select>
        </div>
        <div class="filter pull-right" style="margin: 2px 0 0px 5px">
          <label for="language-filter" class="control-label mobile-view-toggle" style="display:none;">Toggle </label>
          <input type="checkbox" checked data-toggle="toggle" id="mobile-filter"
                 data-on="All"
                 data-off="<img class='small_icon' src='{{ cdn('icons/streaminfo/mobilecompat.png') }}' >" data-size="mini">
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
    <div class="panel-body collapse in" id="streams">
      <div class="table-responsive">
        <table class="table table-striped table-hover" id="streams-table" width="100%">
          <tbody>
          @php
            // $showStreams = \Carbon\Carbon::parse($event->start_date)->timestamp - \Carbon\Carbon::now()->timestamp > 3600 && ( Auth::check() && !Auth::user()->role);
            $showStreams = \Carbon\Carbon::parse($event->start_date)->timestamp - \Carbon\Carbon::now()->timestamp > 3600;
            if(Auth::check() && Auth::user()->role)
              $showStreams = false;
            if(Auth::check()){
              //
              $streamersStreams = $allStreams->filter(function($item) {
                  return $item->user_id == Auth::user()->id;
              })->all();
            }

          @endphp
            @include('ace_stream')
          @if($allStreams->count())
            @if( $showStreams )
              <tr>
                <td colspan="11">There are currently {{$allStreams->count()}} submitted streams available for this match. These will become visible 1 hour before kick-off time</td>
              </tr>
              @if(Auth::check())
                @include('eventStreamsTemplate',['streams'=>$streamersStreams])
              @endif
            @else
              @include('eventStreamsTemplate',['streams'=>$allStreams])
            @endif
          @endif
          @if($allStreams->count() < 1)
            <tr>
              <td colspan="11" style="border: none;">There are no streams for this match right now!</td>
            </tr>
          @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>


  @include('eventComments',['comments'=>$comments, 'streams' => $allStreams,'event_id'=>$event->event_id, 'comment_count' => $comment_count, 'comment_sort_options' => $comment_sort_options, 'order_type' => $order_type, 'user_comment_count' => $user_comment_count, 'treeComments' => $hComments])
@endsection

@section('scripts')
  <script src="{{ cdn('js/jquery.countdown.min.js') }}"></script>
  <script src="{{ cdn('js/streams.min.js') }}"></script>
  <script src="//cdn.jsdelivr.net/alertifyjs/1.9.0/alertify.min.js"></script>

  <!-- CSS -->
  <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/alertify.min.css"/>
  <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/themes/default.min.css"/>
  <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/themes/bootstrap.min.css"/>

  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
  <script src="{{ cdn('js/parsley.min.js') }}"></script>

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

    @media screen and (min-width: 1230px) {
      .show-one-line {
        position: absolute;
        white-space: nowrap;
        transform: translateX(-50%);
      }
    }

    @media screen and (min-width: 600px) and (max-width: 1100px) {
      #http_events #panel-comment .color-gold {
        transform: translateY(50%);
        display: inline-block;
      }

      @media screen and (max-width: 600px) {
        .event-comments a+small {
          display: block;
          margin: 10px 0;
        }
        .comment-sort {
          display: none;
        }
      }
    }
  </style>
{{-- @section('modalblock') --}}
<div class="blurry"></div>
<style>
  .color-gold {
    color: #B3994C;
  }
  .modal{ z-index: 2050 !important}
</style>
<div class="modal fade edit_form" id="edit_form" role="dialog">
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
<script type="text/javascript" src="{{secure_asset('js/streams_func1.js')}}"></script>


  @if(\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role>=1)
<script type="text/javascript" src="{{secure_asset('js/streams_func2.js')}}"></script>

  @endif
  <script type="text/javascript" src="{{secure_asset('js/streams_func3.js')}}"></script>

@endsection

