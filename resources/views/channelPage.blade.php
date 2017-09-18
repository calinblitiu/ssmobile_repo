@extends('master')
@section('title', $allSchedule['channel']['name'].' channel-')
@section('headScripts')
  <link rel="stylesheet" href="{{ secure_asset('css/news.min.css') }}">
  {{-- <script type="text/javascript" src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=gq4mpo5r0xobgamm8pni3pqatgqnal9yolqelhzcvnzmkv7i"></script> --}}
  <script src="{{ secure_asset('js/axios.min.js') }}"></script>
  <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
  <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
  {{--<script src="{{ cdn('js/tags.js') }}"></script>--}}
  <script src="{{ secure_asset('js/custom.min.js') }}"></script>
  {{--<script src="{{ cdn('plugins/clipboard.min.js') }}"></script>--}}
  <script src="{{ cdn('plugins/clipboard_tags_min.js') }}"></script>
  {{--<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">--}}
  {{--<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>--}}
  <link href="{{secure_asset('css/bootstrap-toggle.min.css')}}" rel="stylesheet">
  <script src="{{secure_asset('js/bootstrap-toggle.min.js')}}"></script>
@endsection

@section('content')
  <div class="new-result-container">
    <div class="breadcrumbs">
      <div class="container">
        <div><a class="no_underline">Channels</a></div>
        <div><a class="no_underline">{{$country}}</a></div>
        <div><a class="no_underline"><?php echo $allSchedule['channel']['name']; ?></a></div>
      </div>
    </div>
  </div>

  <div class="row">
      <div class="col-sm-12" align="center">
        <h2><?php echo $allSchedule['channel']['name']."\t"; echo $country; ?>
           Football Schedule
        </h2>
      </div>
      <div class="col-sm-12">
        <div class="col-xs-6">
            <h4 class="tvSchedule_header header_text" >About</h4>
            <p align="center">{{$channel_description}}</p>

        </div>
        <div class="col-xs-6 acquire_rights">
            <h4 class="tvSchedule_header header_text">Acquire rights</h4>
            <p align="center">{{$channel_acquire}}</p>
        </div>
      </div>

  </div>


  <div class="tv_channel_table">
    <div class="panel-body collapse in" id="streams">
    <?php
      $scheduleArray = $allSchedule['fixtures'];
      $dt = date('Y-m-d H:i:s');
      $day = date('Y-m-d');
      $dt_d =new datetime($day);
      for($j=0; $j < count($scheduleArray); $j++){
        $competition_date = date('Y-m-d H:i:s', strtotime($scheduleArray[$j]['datetime']));
        $competition_day = date('Y-m-d', strtotime($scheduleArray[$j]['datetime']));
        $competition_d = new datetime($competition_date);
        if($competition_date > $dt){
          $diff = $dt_d->diff($competition_d);
          if (($diff->days)>0  ){
        ?>
      <div class="row matches_divider" id="date_bar">
        <i class="fa fa-calendar fa-2x"></i>
        <span><?php echo date( "d M Y", strtotime($scheduleArray[$j]['datetime']) ); ?></span>
      </div>
      <?php
        $dt_d = $competition_d;
      }
      ?>
      <div class="row tv_row_channel_match" id="tv-hover">
        <div class="col-xs-2 " style="width:20%;">
          <p class="channel_match">
            <div class="row text-center mobile-view-competition-time">
              <?php echo date( "G:i", strtotime($scheduleArray[$j]['datetime']) ); ?>
            </div>
          </p>
        </div>
        <div class="col-xs-2 mobile-view-text-center" style="width:20%;">
          <p class="channel_match " style="color:#444">
            <div class="row text-center">
            @foreach ($allCompetitions as $competition)
              @if (($competition->competition_name)==($scheduleArray[$j]['competition']))
                @if( file_exists( 'images/competitions/small/'.$competition->competition_logo))
                  <img class="mobile-view-competition-logo" src="{{ cdn('images/competitions/small/'.$competition->competition_logo)}}"  width="30" height="30">
                @else
                  <img class="mobile-view-competition-logo" src="{{ cdn('images/generic.png') }}"  width="30 " height="30">
                @endif
              @endif
            @endforeach
            </div>
          </p>
        </div>
        <div class="col-xs-8" style="width:60%;">
          <p class="channel_match">
            <div class="row">
              <div class="col-md-12 mobile-view-main-table-hide">
                <div class="col-md-5 text-right">

                   @if( $scheduleArray[$j]['team1_logo'])
                    {{ $scheduleArray[$j]['team1_name']}} &nbsp<img src="{{$scheduleArray[$j]['team1_logo']}}" width="30" height="30">
                    @else
                      {{ $scheduleArray[$j]['team1_name'] }} &nbsp;<img src="{{ cdn('images/generic.png')}}" width="30" height="30">
                    @endif

                </div>
                <div class="col-md-2 text-center">
                    vs
                </div>
                <div class="col-md-5 text-left">

                    @if( $scheduleArray[$j]['team2_logo'])
                    <img src="{{$scheduleArray[$j]['team2_logo']}}" width="30" height="30">&nbsp;{{ $scheduleArray[$j]['team2_name']}}
                    @else
                      <img src="{{ cdn('images/generic.png')}}" width="30" height="30"> &nbsp;{{ $scheduleArray[$j]['team2_name'] }}
                    @endif

                </div>
              </div>
              <div class="col-md-12 mobile-view-main-table-show" style="display:none;">
                <div class="mobile-view-team-first">
                  @if($scheduleArray[$j]['team1_logo'])
                    {{$scheduleArray[$j]['team1_name']}}
                  @else
                    {{ $scheduleArray[$j]['team1_name'] }}
                  @endif
                </div>
                <div style="position:relative;">
                  @if( $scheduleArray[$j]['team1_logo'])
                    <img style="position:absolute;top:-6px;left:0;" src="{{ $scheduleArray[$j]['team1_logo']}}" width="30" height="30">
                  @else
                    <img style="position:absolute;top:-6px;left:0;" src="{{ cdn('images/generic.png')}}" width="30" height="30">
                  @endif
                  <div class="mobile-view-vs-set">
                      <span>vs</span>
                  </div>
                  @if( $scheduleArray[$j]['team2_logo'])
                    <img style="position:absolute;top:-6px;right:0;" src="{{ $scheduleArray[$j]['team2_logo']}}" width="30" height="30">
                  @else
                    <img style="position:absolute;top:-6px;right:0;" src="{{ cdn('images/generic.png')}}" width="30" height="30">
                  @endif
                </div>
                <div class="mobile-view-team-second">
                  @if(  $scheduleArray[$j]['team2_logo'] )
                    <span style="overflow:hidden;width:130px;display:inline-block;text-overflow: ellipsis;">{{  $scheduleArray[$j]['team2_name']}}</span>
                  @else
                    <span style="overflow:hidden;width:130px;display:inline-block;text-overflow: ellipsis;">{{  $scheduleArray[$j]['team2_name']}}</span>
                  @endif
                </div>
              </div>
            </div>
          </p>
        </div>
      </div>
     <?php }} ?>
    </div>
  </div>
@endsection
