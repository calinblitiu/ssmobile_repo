<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Exclusive free HD quality streams for cord cutters at Soccer Streams. We index stream links shared by hundreds of independent streamers.">
  <meta name="author" content="soccerstreams team">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  @yield('metaSection')
  <link rel="icon" href="{{ cdn('favicon.ico') }}">

  <title>@yield('title')Soccer Streams</title>

  <!-- Material Design fonts -->
  <link type="text/css" rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400,400italic,600,600italic,700,800">
  <link type="text/css" rel="stylesheet" href="//fonts.googleapis.com/css?family=Raleway:400,500,700,600,800">
  <link type="text/css" rel="stylesheet" href="//fonts.googleapis.com/css?family=Oswald:400,700">
  <link rel="stylesheet" type="text/css" href="{{ cdn('css/RobotoSlab.min.css') }}">
  <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="stylesheet" type="text/css" href="{{ secure_asset('fonts/font-awesome/css/font-awesome.min.css') }}">
  <script src="{{ secure_asset('js/jquery_1.11.3.min.js') }}"></script>
 {{-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>--}}
  <!-- donation stuff
   <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
   <link href="//siolab.pw/donation/css/udb.css?ver=1.60" rel="stylesheet">
   <script src="//siolab.pw/donation/js/udb-jsonp.js?ver=1.60"></script> -->
  <!-- Bootstrap core CSS -->
  <link href="{{ cdn('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
  @if(Session::get('NightMode') || Session::get('NightMode') == '1' )
    <link rel="stylesheet" type="text/css" href="{{ cdn('css/soccerstreams_night.min.css') }}">
  @else
    <link rel="stylesheet" type="text/css" href="{{ cdn('css/soccerstreams.min.css') }}">
  @endif
  {{-- <link rel="stylesheet" type="text/css" href="{{ cdn('css/soccerstreams_normal.css') }}"> --}}
  <!-- Bootstrap Country and File upload CSS -->
  {{--<link rel="stylesheet" type="text/css" href="{{ cdn('css/bootstrap-formhelpers.css') }}">--}}
  <link rel="stylesheet" type="text/css" href="{{ cdn('css/bootstrap-formhelpers.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ cdn('css/fileinput.min.css') }}">

  <!-- sceditor css -->
  <link rel="stylesheet" type="text/css" href="{{ secure_asset('css/sceditor.default.min.css') }}">

  <!-- Bootstrap Country and File Upload JS -->
  {{--<script src="{{ cdn('js/bootstrap-formhelpers.js') }}"></script>--}}
  <script src="{{ cdn('js/bootstrap-formhelpers.min.js') }}"></script>
  <script src="{{ cdn('js/fileinput.min.js') }}"></script>

  <script src="{{ cdn('js/hammer.min.js') }}"></script>
  <script src="{{ cdn('js/custom.min.js') }}"></script>
  <script src="{{ cdn('bootstrap/js/bootstrap.min.js') }}"></script>
  <script src="{{ cdn('js/moment.min.js') }}"></script>
  <script src="{{ cdn('js/moment-timezone.min.js') }}"></script>

  <!-- sceditor javascript -->
  <script src="{{ secure_asset('js/jquery.sceditor.min.js') }}"></script>

  <script>
    setInterval(function(){
      checkNotificationurl = "{{secure_url('checkNotification')}}"
      $.get(checkNotificationurl, function (data) {
        if(data != '0'){
          $('span.notification-unread-navbar').css('display','block');
          $('span.notification-unread-navbar').text(data);
          $('div.notification-unread-navbar-dropdown').css('display','block');
          $('div.notification-unread-sidemenuicon').css('display','block');
          $('div.notification-unread-sidemenuicon').text(data);
          $('.notification-unread-sidemenubar-dropdown').css('display','block');
          $('.profile-page-notification-count').css('display','block');
          $('.profile-page-notification-count').text(data);
        }else if(data == '0'){
          $('span.notification-unread-navbar').css('display','none');
          $('div.notification-unread-navbar-dropdown').css('display','none');
          $('div.notification-unread-sidemenuicon').css('display','none');
          $('.notification-unread-sidemenubar-dropdown').css('display','none');
          $('.profile-page-notification-count').css('display','none');
        }
      });
    },200000)

    var tmx;
    function UpdateTimezone() {
      var tzOptions = document.getElementById('offset');
      var tz = tzOptions.options[tzOptions.selectedIndex].value;
      var timezoneURL = "{{ secure_url('setTimezone') }}";
      $.get(timezoneURL + "/" + tz);
      tmx=tz;
      $('.event-time').each(function (i, e) {
        var oldTime = $(e).attr('data-eventtime');
        var utcStart = moment.utc(oldTime).utcOffset('UTC');
        var startDate = utcStart.utcOffset(tz * 60).format('HH:mm');
        $(e).html(startDate);
      });
    }
    function SelectElement(valueToSelect) {
      var element = document.getElementById('offset');
      element.value = valueToSelect;
    }
  </script>
  @if(!Session::has('visitorTZ'))
    <script>
      momentZone = moment.tz.guess();
      currentZoneOffset = moment.tz(momentZone).utcOffset() / 60;
      $(function ($) {
        var timezoneURL = "{{ secure_url('setTimezone') }}";
        $.get(timezoneURL + "/" + currentZoneOffset, function (data) {
          SelectElement(currentZoneOffset);
          UpdateTimezone();
        });
      });
    </script>
  @endif
  <link rel="stylesheet" href="{{ cdn('plugins/sweetalert/sweetalert.min.css') }}">
  <script src="{{ cdn('plugins/sweetalert/sweetalert.min.js') }}"></script>
  @yield('headScripts')
  @yield('style')

    <style>
      .nightmode-button-div-desktop{
        height: 40px;
        width: 30px;
        background-color: #00222E;
        position: fixed;
        z-index: 2100;
        right: 0px;
        top: 105px;
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
        box-shadow: 0px 5px 5px 2px rgba(0,0,0,0.7);
        cursor: pointer;
        transition: width 0.4s;
      }
      .nightmode-button-div-desktop:hover{
        width: 100px;
      }
      .nightmode-button-div-desktop:active{
        box-shadow: 0px 1px 3px 2px rgba(0,0,0,0.7);
        top: 108px;
      }
      .nightmode-button-p{
        color: #B3994C;
        font-size: 15px;
        font-weight: bold;
        position: absolute;
        margin-bottom: 0;
        white-space: nowrap;
        top: 7px;
        left: 10px;
        opacity: 0;
        transition: opacity 0.3s;
      }
      .nightmode-button-div-desktop:hover .nightmode-button-p{
        opacity: 1;
      }
      .nightmode-button-label-mobile{
        display: none;
        position: fixed;
        z-index: 3005;
        transition: all .2s ease-in-out 0s;
        font-size: 2.6em;
        color: #b3994c;
        text-align: center;
        left: 10px;
        bottom: 18px;
        width: 36px;
        height: 36px;
        background-color: #0b1a27;
        border-radius: 100%;
        box-shadow: 0 0px 5px 2px rgba(179,153,76,0.7);
        margin-bottom: 0px;
      }
      @media (max-width: 768px) {
        .nightmode-button-label-mobile{
          display: block;
        }
        .nightmode-button-div-desktop{
          display: none;
        }
        .navbar-brand {
          width: 100%;
          display: block;
          margin-bottom: 10px;
        }

        .navbar-brand > img {
          margin: auto;
          width: auto;
        }

        .live-menu .navbar-nav {
          float: none;
          width: 195px;
          margin: auto;
        }
      }

      #logo-ball {
        display: block;
        position: relative;
        top: 11px;
        left: 63px;
        transition: transform 0.8s ease-in-out;
      }

      .navbar-brand:hover #logo-ball {
        transform: rotate(360deg);
      }

      .navbar-brand {
        display: block;
        width: 242px;
        height: 97px !important;
        background: url("{{ cdn('images/logo.png') }}");
        padding: initial !important;
      }

      .navbar-brand > img {
        height: initial !important;
        margin-top: initial !important;
      }

      @media (max-width: 768px) {
        .navbar-brand > img {
          margin: initial !important;
        }
      }

      .full-logo {
        width: 100% !important;
        height: 100% !important;
        background: none !important;
      }

      .full-logo img {
        margin-top: 33px !important;
        width: 100% !important;
      }
      .live-menu .dropdown-menu{
        background: #001a28;
        min-width:30px;
      }
      .notification-unread-navbar{
        position: absolute;
        width: 18px;
        background-color: #fb1056;
        text-align: center;
        font-size: 10px;
        border-radius: 100%;
        font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
        font-weight: bold;
        color: #000;
        height: 18px;
        top: 10px;
        right: -11px;
      }
      .notification-unread-navbar-dropdown{
        position: absolute;
        background-color: #fb1056;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
        top: 35px;
        left: 29px;
      }
      .notification-unread-sidemenuicon{
        position: absolute;
        font-size: 10px;
        width: 15px;
        height: 15px;
        line-height: 15px;
        background-color: #fb1056;
        color: #000;
        border-radius: 50%;
        font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
        top: 0;
        right: 0;
      }
      .notification-unread-sidemenubar-dropdown{
        position: absolute;
        background-color: #fb1056;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        top: 12px;
        left: 24px;
      }
      #footer-links{ line-height:34px; }
      #footer_timer{ line-height:36px;text-align:right; color:#B3994C; }
      .footer #footer-links li a, .footer #footer-links li{ color:#B3994C; }
      @media (max-width: 767px) {
        #footer-links{ text-align:center; }
      }
      footer{
        position:absolute;
        bottom:0;
        width:100%;
        height:60px;   /* Height of the footer */
        background:#6cf;
      }
    </style>

  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-91839096-1', 'auto');
    ga('send', 'pageview');
</script>
  <script>
    window.paceOptions = {
      target: '#pace-loader',
	  restartOnPushState: false,
    }
  </script>
  <script src="{{ cdn('plugins/pace/pace.min.js') }}"></script>
  <link rel="stylesheet" href="{{ cdn('plugins/pace/themes/yellow/pace-theme-minimal.css') }}">

</head>
@php
if(!Auth::guest()){
  $unread = \App\Notification::where(['target_id' => Auth::id(), 'action' => 0, 'type' => 1])->orderBy('created_at', 'desc')->get();
}

@endphp
<body>
  <div class="background-overlay" style="display:none;"></div>
  <div class="nightmode-button-div-desktop nightmode-toggle-button">
    @if(Session::get('NightMode') || Session::get('NightMode') == '1' )
      <p class="nightmode-button-p" style="font-size:13px;left:13px;top:8px;font-family: 'HelveticaNeue-Light', 'Helvetica Neue Light', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;">Normal Mode</p>
    @else
      <p class="nightmode-button-p">Night Mode</p>
    @endif
  </div>
  <label class="nightmode-button-label-mobile nightmode-toggle-button">
    @if(Session::get('NightMode') || Session::get('NightMode') == '1' )
      <i class="fa fa-sun-o" aria-hidden="true"></i>
    @else
      <i class="fa fa-moon-o" aria-hidden="true"></i>
    @endif
  </label>
  <input id="floating-button-toggle" type="checkbox" class="plus">
  <label for="floating-button-toggle" class="floating-button-toggle" style="margin:0!important;"><i style="border-radius:100%;background-color:#b3994c;" class="fa fa-soccer-ball-o"></i></label>
  <div class="floating-button" id="master-floating-button">
    @if (Auth::guest())
      <a href="{{ secure_url('rules') }}" class="beforelogin" data-title="Rules"><i class="fa fa-soccer-ball-o"></i></a>
      <a href="{{ secure_url('donate') }}" class="beforelogin" data-title="Donate"><i class="fa fa-credit-card"></i></a>

      <a href="{{ secure_url('submit') }}" class="beforelogin" data-title="Submit streams"><i class="fa fa-sticky-note"></i></a>
      @include('social-icon', ['isBefore' => true])

      <a href="https://discordapp.com/channels/341369416656617493/341369416656617493" class="beforelogin" target="_blank" data-title="Discord" style="color: #B3994C;">
        <img alt="discord" src="/images/29274582-4be0c7e2-8100-11e7-83c8-0435aee88626-min.png"/>
      </a>

      <a href="{{ secure_url('redditLogin') }}" class="beforelogin" data-title="Reddit Login"><i class="fa fa-reddit-alien" aria-hidden="true"></i></a>

{{--       <a href="https://www.facebook.com/rSoccerStreams/" class="beforelogin" target="_blank" data-title="Facebook"><i class="fa fa-facebook"></i></a>
      <a href="https://twitter.com/rsoccerstreams" class="beforelogin" target="_blank" data-title="Twitter"><i class="fa fa-twitter"></i></a>
      <a href="http://www.reddit.com/r/soccerstreams" class="beforelogin" target="_blank" data-title="Reddit"><i class="fa fa-reddit"></i></a> --}}

      <a href="{{ secure_url('login') }}" class="beforelogin" data-title="Login"><i class="fa fa-sign-in"></i></a>
      <a href="{{ secure_url('register') }}" class="beforelogin" data-title="Signup"><i class="fa fa-tv"></i></a>
      <!-- <a href="https://siolab.pw/forums/" class="beforelogin" target="_blank" data-title="Forum"><i class="fa fa-credit-card"></i></a> -->
    @else
      @include('social-icon', ['isBefore' => false])
      <a href="{{ secure_url('submit') }}" class="afterlogin" data-title="Submit streams"><i class="fa fa-sticky-note"></i></a>
      <a href="{{ secure_url('rules') }}" class="afterlogin" data-title="Rules"><i class="fa fa-soccer-ball-o"></i></a>
      <!-- a href="https://siolab.pw/forums/" class="afterlogin" target="_blank" data-title="Forum"><i class="fa fa-credit-card"></i></a> -->
      <a href="{{ secure_url('profile/favourite') }}" class="afterlogin" data-title="Favorites"><i class="fa fa-bookmark"></i></a>
      <a href="{{ secure_url('donate') }}" class="afterlogin" data-title="Donate"><i class="fa fa-credit-card"></i></a>
      @if(!isset(Auth::user()->id))
        <a href="{{ secure_url('redditLogin') }}" class="afterlogin" data-title="Reddit Login"><i class="fa fa-reddit-alien" aria-hidden="true"></i></a>
      @endif

{{--       <a href="https://www.facebook.com/rSoccerStreams/" class="afterlogin" target="_blank" data-title="Facebook"><i class="fa fa-facebook"></i></a>
      <a href="https://twitter.com/rsoccerstreams" class="afterlogin" target="_blank" data-title="Twitter"><i class="fa fa-twitter"></i></a>
      <a href="http://www.reddit.com/r/soccerstreams" class="afterlogin" target="_blank" data-title="Reddit"><i class="fa fa-reddit"></i></a> --}}

      <a href="{{ secure_url('profile') }}" class="afterlogin" data-title="Profile"><i class="fa fa-user"></i></a>
      <a href="{{ secure_url('profile/messages') }}" class="afterlogin" data-title="Messages"><i class="fa fa-envelope"></i></a>
      <a href="https://discordapp.com/channels/341369416656617493/341369416656617493" class="afterlogin" target="_blank" data-title="Discord" style="color: #B3994C;">
        <img alt="discord" src="/images/29274582-4be0c7e2-8100-11e7-83c8-0435aee88626-min.png"/>
      </a>
      <a href="{{ secure_url('moderator/dashboard') }}"  class="afterlogin" data-title="Moderator"><i class="fa fa-group"></i></a>
      <a href="{{ secure_url('logout') }}" class="afterlogin" data-title="Logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i></a>
      <form id="logout-form" action="{{ secure_url('logout') }}" method="POST" style="display: none;">
          {{ csrf_field() }}
      </form>
    @endif
  </div>
<nav class="logo-header main-hide" role="navigation" style="position: fixed; top: 0; left: 0; right: 0; z-index: 111; margin-top: -5px;">
  <div class="container" id="headerContainer">
    <div class="row">
      <div class="col-md-3 col-sm-3 col-xs-3">
        <div class="“logo-confiner”">
          <a href="{{ secure_url('/') }}" class="navbar-brand full-logo">
            <img id="logo-ball-full" alt="Soccer Streams logo" src="{{ cdn('images/new_logo.png') }}">
          </a>
        </div>
      </div>
{{--         <div class="live-menu social-icons">
          <ul>
            <li>
              <a href="https://www.facebook.com/rSoccerStreams/" target="_blank"><i class="fa fa-facebook fa-2x" aria-hidden="true"></i></a>
            </li>
            <li>
            <a href="https://twitter.com/rsoccerstreams" target="_blank"><i class="fa fa-twitter fa-2x" aria-hidden="true"></i></a>
            </li>
            <li>
            <a href="http://www.reddit.com/r/soccerstreams" target="_blank"><i class="fa fa-reddit fa-2x" aria-hidden="true"></i></a>
            </li>
          </ul>
        </div> --}}
      <div class="col-md-7 col-sm-7 col-xs-7 no-padding" style="float:right;">
        <div class="live-menu collapse navbar-collapse no-padding-left" style="padding-bottom:15px;padding-top:32px;">
          <ul class="nav navbar-nav">
            <li>
              <!--<a href="https://siolab.pw/forums/" target="_blank"><i class="fa fa-credit-card"></i><span class="hidden-sm hidden-xs"> &nbsp;Forums</span></a>
            </li>
            <li class='spliter'><span>/</span></li>-->
            <li>
              <a href="{{ secure_url('donate') }}"><i class="fa fa-credit-card"></i><span class="hidden-sm hidden-xs"> &nbsp;Donate</span></a>
            </li>
            <li class='spliter'><span>/</span></li>
            <li>
              <a href="{{ secure_url('rules') }}"><i class="fa fa-soccer-ball-o"></i><span class="hidden-sm hidden-xs"> Rules</span></a>
            </li>
            <li class='spliter'><span>/</span></li>
            <li>
              <a href="{{ secure_url('submit') }}"><i class="fa fa-sticky-note"></i><span class="hidden-sm hidden-xs"> &nbsp;Submit streams</span></a>
            </li>
            <!--<li class='spliter'><span>/</span></li>
            <li>
              <a href="{{ secure_url('faq') }}"><i class="fa fa-question-circle-o"></i><span class="hidden-sm hidden-xs"> &nbsp;FAQ</span></a>
            </li>-->
            @if(!isset(Auth::user()->id))
            <li class='spliter'><span>/</span></li>
            <li>
              <a href="{{ secure_url('redditLogin') }}"><i class="fa fa-reddit-alien" aria-hidden="true"></i><span class="hidden-sm hidden-xs"> Reddit Login</span></a>
            </li>
            @endif
            <li class='spliter'><span>/</span></li>
            @if (Auth::guest())
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i><span class="hidden-sm hidden-xs">&nbsp;Account</span></a>
                <ul class="dropdown-menu">
                  <li>
                    <a href="{{ secure_url('login') }}"><i class="fa fa-sign-in"></i><span class="hidden-sm hidden-xs"> &nbsp;Login</span></a>
                  </li>
                  <li>
                    <a href="{{ secure_url('register') }}"><i class="fa fa-tv"></i><span class="hidden-sm hidden-xs"> &nbsp;Signup</span></a>
                  </li>
                </ul>
              </li>
            @else
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i><span class="hidden-sm hidden-xs">
                &nbsp;{{Auth::user()->name}} @if(Auth::user()->role==1) ( Mod ) @elseif( Auth::user()->role==2 ) ( Admin ) @endif</span>
                  <span class="notification-unread-navbar" style="display:none">{{isset($unread)?count($unread):0}}</span>
                </a>
                <ul class="dropdown-menu">
                  <li>
                    <a href="{{ secure_url('profile') }}">
                      <i class="fa fa-user"></i><span class="hidden-sm hidden-xs"> &nbsp;Profile </span>
                    </a>
                  </li>
                  <li>
                    <a href="{{ secure_url('profile/messages') }}">
                      <i class="fa fa-envelope"></i><div class="notification-unread-navbar-dropdown" style="display:none"></div><span class="hidden-sm hidden-xs"> &nbsp;Messages </span>
                    </a>
                  </li>
                  <li>
                    <a href="{{ secure_url('profile/favourite') }}">
                      <i class="fa fa-bookmark"></i><span class="hidden-sm hidden-xs"> &nbsp;Favorites </span>
                    </a>
                  </li>
                  @if( Auth::user()->role )
                  <li>
                    <a href="{{ secure_url('moderator/dashboard') }}">
                      <i class="fa fa-group"></i><span class="hidden-sm hidden-xs"> &nbsp;Moderator </span>
                    </a>
                  </li>
                  @endif
                  <li>
                    <a href="{{ secure_url('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                      <i class="fa fa-sign-out"></i><span class="hidden-sm hidden-xs"> &nbsp;Logout</span>
                    </a>
                    <form id="logout-form" action="{{ secure_url('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                  </li>
                </ul>
              </li>
            @endif
          </ul>
        </div>
      </div>
    </div>
  </div>   <!-- /.container -->
  <div id="pace-loader" style="position: relative; margin-top: -4px">

  </div>
</nav>

<div class="main-content" id="main-content" style="margin-top: 27px;overflow-x:hidden;position:relative;left:0;transition:left 0.5s;">
  <nav class="logo-header mobile-show" role="navigation" style="display:none;position: fixed; top: 0; left: 0; right: 0; z-index: 2001; margin-top: -5px;">
    <div class="background-overlay-sidebar"></div>
    <div class="container" id="headerContainer" >
      <div class="row">
          <a id="side-menubar">
            <span></span>
            <div class="notification-unread-sidemenuicon" style="display:none">{{ isset($unread)?count($unread):0 }}</div>
          </a>
          <!-- <a class="close-side-menubar" id="side-menubar" style="display:none;"><span></span></a> -->
          <div class="“logo-confiner” mobile-view-logo">
            <a href="{{ secure_url('/') }}" class="navbar-brand full-logo">
              <img id="logo-ball-full" alt="Soccer Streams logo" src="{{ cdn('images/new_logo.png') }}">
            </a>
        </div>
      </div>
    </div>   <!-- /.container -->
    <ul class="nav nav-mobile-view">
      <li>
        <a href="{{ secure_url('rules') }}"><i class="fa fa-soccer-ball-o"></i><span class=""> Rules</span></a>
      </li>
      <li>
        <!-- <a href="https://siolab.pw/forums/" target="_blank"><i class="fa fa-credit-card"></i><span class=""> &nbsp;Forums</span></a> -->
      </li>
      <li>
        <a href="{{ secure_url('donate') }}"><i class="fa fa-credit-card"></i><span class=""> &nbsp;Donate</span></a>
      </li>
      <li>
        <a href="{{ secure_url('submit') }}"><i class="fa fa-sticky-note"></i><span class=""> &nbsp;Submit streams</span></a>
      </li>
      <!--<li class='spliter'><span>/</span></li>
      <li>
        <a href="{{ secure_url('faq') }}"><i class="fa fa-question-circle-o"></i><span class="hidden-sm hidden-xs"> &nbsp;FAQ</span></a>
      </li>-->
      @if(!isset(Auth::user()->id))
      <li>
        <a href="{{ secure_url('redditLogin') }}"><i class="fa fa-reddit-alien" aria-hidden="true"></i><span class=""> Reddit Login</span></a>
      </li>
      @endif
      <li class="dropdown2">
        <a href="#" class="mobile-show-acound-dropdown2" ><i class="fa fa-twitter"></i><span class="">&nbsp;Social</span></a>
        <ul class="dropdown-menu-mobile">
          <li class="drop-item-small">
            <a href="https://www.facebook.com/rSoccerStreams/" target="_blank"><i class="fa fa-facebook"></i><span class="">&nbsp;Facebook</span></a>
          </li>
          <li class="drop-item-small">
          <a href="https://twitter.com/rsoccerstreams" target="_blank"><i class="fa fa-twitter"></i><span class="">&nbsp;Twitter</span></a>
          </li>
          <li class="drop-item-small">
          <a href="https://www.reddit.com/r/soccerstreams" target="_blank"><i class="fa fa-reddit"></i><span class="">&nbsp;Reddit</span></a>
          </li>
        </ul>
      </li>
      @if (Auth::guest())
        <li class="dropdown1">
          <a href="#" class="mobile-show-acound-dropdown1" ><i class="fa fa-user"></i><span class="">&nbsp;Account</span></a>
          <ul class="dropdown-menu-mobile">
            <li class="drop-item-small">
              <a href="{{ secure_url('login') }}"><i class="fa fa-sign-in"></i><span class=""> &nbsp;Login</span></a>
            </li>
            <li class="drop-item-small">
              <a href="{{ secure_url('register') }}"><i class="fa fa-tv"></i><span class=""> &nbsp;Signup</span></a>
            </li>
          </ul>
        </li>
      @else
        <li class="dropdown1">
          <a href="#" class="mobile-show-acound-dropdown1">
            <span class="hidden-sm" style="display: block;width: 130px;overflow: hidden;white-space:nowrap;text-overflow: ellipsis;">
              <i class="fa fa-user"></i>
              &nbsp;{{Auth::user()->name}} @if(Auth::user()->role==1) ( Mod ) @elseif( Auth::user()->role==2 ) ( Admin ) @endif
            </span></a>
          <ul class="dropdown-menu-mobile">
            <li class="drop-item-small">
              <a href="{{ secure_url('profile') }}">
                <i class="fa fa-user"></i><span class="hidden-sm"> &nbsp;Profile </span>
              </a>
            </li>
            <li class="drop-item-small">
              <a href="{{ secure_url('profile/messages') }}">
                <i class="fa fa-envelope"></i><div class="notification-unread-sidemenubar-dropdown" style="display:none"></div><span class="hidden-sm"> &nbsp;Messages </span>
              </a>
            </li>
            <li class="drop-item-small">
              <a href="{{ secure_url('profile/favourite') }}">
                <i class="fa fa-bookmark"></i><span class="hidden-sm"> &nbsp;Favorites </span>
              </a>
            </li>
            @if( Auth::user()->role )
            <li class="drop-item-small">
              <a href="{{ secure_url('moderator/dashboard') }}">
                <i class="fa fa-group"></i><span class="hidden-sm"> &nbsp;Moderator </span>
              </a>
            </li>
            @endif
            <li class="drop-item-small">
              <a href="{{ secure_url('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa fa-sign-out"></i><span class="hidden-sm"> &nbsp;Logout</span>
              </a>
              <form id="logout-form" action="{{ secure_url('logout') }}" method="POST" style="display: none;">
                  {{ csrf_field() }}
              </form>
            </li>
          </ul>
        </li>
      @endif
    </ul>

    <div class="col-md-9 move-left-delay mobile-view">
      @include('alert')
    </div>
  </nav>
  <div class="container" id="mobile-view-content" style="position: relative">
    <div class="col-md-9">
      <div class="mobile-view-hide">
        @include('alert')
      </div>
      @yield('content')
    </div>
    <div class="col-md-3" style="margin-top: 20px;">
      <div class="sidebar-right" style="border-left: 1px solid #c0c0c0; margin-left: -15px; padding-left: 15px;">
        <div class="row" style="text-align: center; margin: 20px 0 30px 0; padding: 10px;background-image: url(/images/bg.png); border-radius: 5px;">
          <div class="row">
                <div class="col-md-12" id="footer_timer" style="text-align: center;">
                  {{ \Carbon\Carbon::now()->addHours(Session::get('visitorTZ'))->format('jS F H:i:s') }}
                </div>
            </div>
            <div class="row">
                <div class="mobile-view-select-time-cont col-md-12">
                  <select id="offset" class="form-control select-style" name="offset" onchange="UpdateTimezone();" style="background: #fff !important;">
                    @foreach($timeZoneOffsets as $key => $offset)
                      @if(Session::has('visitorTZ') && Session::get('visitorTZ')==$key)
                        {{ $selected = 'selected' }}
                      @else
                        {{ $selected = '' }}
                      @endif
                      <option value="{{ $key }}" {{ $selected }}> {{$offset}}
                      </option>
                    @endforeach
                  </select>
                </div>
          </div>
        </div>
        <div class="row" id="social" style="text-align: center; font-size: 200%; margin: 7px 0 0 0; padding: 10px; background-image: url('{{cdn('images/bg.png')}}'); border-radius: 5px;">

          <a href="https://discordapp.com/channels/341369416656617493/341369416656617493" target="_blank" data-title="Discord" style="color: #B3994C; padding-right: 20px;">
            <img alt="discord" src="{{cdn('images/soccer_header_icon.png')}}" style="margin-top: -8px;"></a>

          <a href="https://www.facebook.com/rSoccerStreams/" target="_blank" data-title="Facebook" style="color: #B3994C;"><i class="fa fa-facebook"></i></a>

          <a href="https://twitter.com/rsoccerstreams" target="_blank" data-title="Twitter" style="color: #B3994C; padding-left: 20px; padding-right: 20px;"><i class="fa fa-twitter"></i></a>

          <a href="https://www.reddit.com/r/soccerstreams" target="_blank" data-title="Reddit" style="color: #B3994C;"><i class="fa fa-reddit"></i></a>
        </div>
		<br>
        <div id="twitter">
          <a class="twitter-timeline" href="https://twitter.com/rsoccerstreams"></a> <script async src="{{secure_url('js/widgets.js')}}" charset="utf-8"></script>
        </div>
      </div>
    </div><!-- sidebar -->
  </div><!-- /.container -->
</div>

<footer class="footer color-bg">
  <div class="container">
    <div class="row">
{{--       <div class="col-sm-5 col-xs-12 pull-right">
        <div class="col-sm-7 col-xs-6 hidden-xs" id="footer_timer">
          {{ \Carbon\Carbon::now()->addHours(Session::get('visitorTZ'))->format('jS F H:i:s') }}
        </div>
        <div class="mobile-view-select-time-cont col-sm-5 col-xs-6 col-xs-offset-3 col-sm-offset-0">
          <select id="offset" class="form-control select-style" name="offset" onchange="UpdateTimezone();">
            @foreach($timeZoneOffsets as $key => $offset)
              @if(Session::has('visitorTZ') && Session::get('visitorTZ')==$key)
                {{ $selected = 'selected' }}
              @else
                {{ $selected = '' }}
              @endif
              <option value="{{ $key }}" {{ $selected }}> {{$offset}}
              </option>
            @endforeach
          </select>
        </div>
      </div> --}}

      <div class="col-md-6" style="text-align: center;">
          <ul id="footer-links">
            <li><a href="{{ secure_url('contact-us') }}">Contact us</a></li>
            <li>Coded with <span style="color: red;">❤</span>{{--<i class="em em-heart"></i>--}}</li>
            <li><a href="{{ secure_url('faq') }}">FAQ</a></li>
            <li class="last_child"><a href="{{ secure_url('dmca') }}">DMCA</a></li>
          </ul>
      </div>

      <div class="col-md-6" style="text-align: center;">
        <div class="social-icons" style="padding-top: 5px;">
          <ul>
            <li>
              <a href="https://www.facebook.com/rSoccerStreams/" target="_blank"><i class="fa fa-facebook fa-2x" aria-hidden="true"></i></a>
            </li>
            <li>
            <a href="https://twitter.com/rsoccerstreams" target="_blank"><i class="fa fa-twitter fa-2x" aria-hidden="true"></i></a>
            </li>
            <li>
            <a href="https://www.reddit.com/r/soccerstreams" target="_blank"><i class="fa fa-reddit fa-2x" aria-hidden="true"></i></a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</footer>

<script src="{{ cdn('js/date.format.min.js') }}"></script>

<script type="text/javascript">

  $(document).ready(function () {

    $('.nightmode-toggle-button').click(function(){
      var setNitghtModeURL = "{{ secure_url('setNightMode') }}";
      $.get( setNitghtModeURL, function( data ) {
        if(data){window.location.reload(true);}
      });
    });

    tmx="{{Session::get('visitorTZ')}}";
    var now = new Date("{{ \Carbon\Carbon::now()->addHours(Session::get('visitorTZ')) }}");
    var second = 0;

    function getdate(){
      date = new Date( now ).getTime() + parseInt(second) * 1000 ;
      converted = new Date( date );
      var h = converted.getHours();
      var m = converted.getMinutes();
      var s = converted.getSeconds();

      $("#footer_timer").html( moment.utc(new Date()).zone( -60*tmx).format('Do MMMM HH:mm:ss'));
      second++;
      setTimeout(function( ){getdate()}, 1000);
    }
    getdate();
  });

</script>
@yield('scripts')

<!-- including notice modal
@include('partials.notice_modal')-->

@yield('modalblock')

</body>
</html>

<style type="text/css">
select#offset.form-control.select-style {
    padding: 0;
    margin: 0;
    color: #B3994C !important;
    border: 0px solid #ccc;
    border-radius: 3px;
    overflow: hidden;
    background: transparent !important;
    text-align: center!important;
    text-align: -moz-center;
    text-align: -webkit-center;
    -ms-text-align-last: center;
    -moz-text-align-last: center;
    text-align-last: center;
}

.pace .pace-progress {
  position: absolute;
}

.afterlogin i,
.afterlogin img,
.beforelogin img {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: #000000;
}

.afterlogin i:before,
.beforelogin i:before {
  color: #000000;
}

.afterlogin img,
.beforelogin img {
  border-radius: 50%;
  max-width: 50%;
}

.select-style select {
    padding: 5px 8px;
    border: none;
    box-shadow: none;
    width:130%;
    background-color: transparent;
    background-image: none;
    -webkit-appearance: none;
       -moz-appearance: none;
            appearance: none;
}
.select-style:active, .select-style:hover {
  outline: none
}
.select-style:hover {
    color: white;
    background: #7d7d7d;
    opacity: 1;
}
</style>