@extends('master')
@section('title','Home - ')
@section('headScripts')
  {{--<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/v/bs-3.3.7/dt-1.10.13/fc-3.2.2/fh-3.1.2/r-2.1.0/datatables.min.css"/>--}}
  {{--<script type="text/javascript" src="//cdn.datatables.net/v/bs-3.3.7/dt-1.10.13/fc-3.2.2/fh-3.1.2/r-2.1.0/datatables.min.js"></script>--}}
  <link rel="stylesheet" type="text/css" href="{{ secure_asset('css/datatables.min.css') }}">
  <script type="text/javascript" src="{{secure_asset('js/datatables.min.js')}}"></script>
  <style>
    #manage_events tbody tr {
      cursor: pointer;
    }
    
    .game_minute {
      animation: blinker 1s linear infinite;
    }
    
    @keyframes blinker {
      50% {
        opacity: 0;
      }
    }

    @media screen and (max-width:767px){
      .table-responsive {
        margin-left: -15px;
        width: calc(100% + 30px) !important;
      }
      body .new-result-container .count span {
        font-size: 70px;
        line-height: 70px;
        bottom: 0px;
      }
      body .new-result-container .logo-holder {
        padding: 10px;
      }
      .live-menu .navbar-nav {
        float: right;
      }
    }
  </style>
@endsection
@section('content')
  @if (session('error'))
    <div class="alert alert-danger" style="margin: 10px;">
      {{ session('error') }}
    </div>
  @endif
  <div id="manage_events">
    <div class="table-responsive">
      <table class="table table-striped table-hover" id="event-table" width="100%">
        <tbody></tbody>
      </table>
    </div>
  </div>
@endsection
@section('scripts')
  <script src="{{secure_asset('js/events_function.js')}}"></script>

@endsection