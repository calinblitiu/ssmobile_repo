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
  <link rel="stylesheet" href="{{ secure_asset('css/news.css') }}"/>

  {{-- <script type="text/javascript" src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=gq4mpo5r0xobgamm8pni3pqatgqnal9yolqelhzcvnzmkv7i"></script> --}}

  <script src="{{ secure_asset('js/axios.min.js') }}"></script>
  <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
  <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
  <script src="{{ cdn('js/tags.js') }}"></script>
  <script src="{{ secure_asset('js/custom.js') }}"></script>
  <script src="{{ cdn('plugins/clipboard.min.js') }}"></script>
  {{--<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">--}}
  {{--<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>--}}
  <link href="{{secure_asset('css/bootstrap-toggle.min.css')}}" rel="stylesheet">
  <script src="{{secure_asset('js/bootstrap-toggle.min.js')}}"></script>
@endsection

@section('content')
  <div class="row" style="padding-top:20px">
    <div class="col-sm-3 " >
      <h4  style="color:#5423ff;">left side bar</h4>
      <div></div>
    </div>

    <div class="col-sm-6 "  >
      <h4  style="color:#5423ff;">dagag</h4>
      <img src='https://demo.mythemeshop.com/socialme/files/2015/03/table-530x250.jpg' style="width:100%">
    </div>
    <div class="col-sm-3 " >
      <h4  style="color:#5423ff;">right side bar</h4>
    </div>
  </div>
@endsection