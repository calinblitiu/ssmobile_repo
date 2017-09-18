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
  <link rel="stylesheet" href="{{ secure_asset('css/streams.css') }}">
  <!-- <link rel="stylesheet" href="{{ secure_asset('css/streams.css') }}"> -->
  <link rel="stylesheet" href="{{ secure_asset('css/comment.css') }}">
  <!-- <link rel="stylesheet" href="{{ secure_asset('css/comment.css') }}"> -->

  {{-- <script type="text/javascript" src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=gq4mpo5r0xobgamm8pni3pqatgqnal9yolqelhzcvnzmkv7i"></script> --}}

  <script src="{{ secure_asset('js/axios.min.js') }}"></script>
  <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
  <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
  <script src="{{ secure_asset('js/tags.js') }}"></script>
  <script src="{{ secure_asset('js/comment.js'.'?'.time()) }}"></script>
  <!-- <script src="{{ secure_asset('js/comment.js'.'?'.time()) }}"></script> -->
  <script src="{{ secure_asset('plugins/clipboard.min.js') }}"></script>
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
  <script src="{{ secure_asset('js/streams.js') }}"></script>
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
  <script type="text/javascript">
    function myFunction(){
      var matcher = $("#country_selector").val();
      $(".tv_row_channel").each(function(i, val){
        var dump = $(val).find("#country_name").text();
        if(matcher==dump || matcher=="All")
        {
          $(val).css({"display":"inline"});
        }
        else
        {
          $(val).css("display","none");
        }
      });

    }
  </script>>

 <script type='text/javascript'>

					$('textarea').sceditor({
						toolbar: 'bold,italic,underline,color,emoticon',
						style: '/css/jquery.sceditor.default.css',
						height: 150,
						width: '95%'
					});

					setInterval(function(){
						$('.sceditor-container iframe').each(function(){
							$(this).width('95%');
						});
					}, 1000);

</script>

{{--
  <script type="text/javascript">
    tinymce.init({
    selector: 'textarea',
    height: 200,
    theme: 'modern',
    plugins: [
      'advlist autolink lists link image charmap print preview hr anchor pagebreak',
      'searchreplace wordcount visualblocks visualchars code fullscreen',
      'insertdatetime media nonbreaking save table contextmenu directionality',
      'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc help'
    ],
    menubar: false,
    toolbar: 'bold italic forecolor Blockquote emoticons',
    image_advtab: true,
    templates: [
      { title: 'Test template 1', content: 'Test 1' },
      { title: 'Test template 2', content: 'Test 2' }
    ],
    content_css: [
      '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
      '//www.tinymce.com/css/codepen.min.css'
    ],
    setup: function (editor) {
        editor.on('change', function () {
            editor.save();
        });
      }
   });
  </script>
--}}

  <script>
    function CommentVoteUp(el, comment_id) {
      el = $(el);
      var url = "{{ secure_url('comment_vote') }}";
      $.ajax({
        url: url,
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "comment_id": comment_id},
        cache: false,
        success: function (data) {
          if (data.msg) {
            swal({title: data.msg, type: "error"});
            return;
          }
          el.parent().parent().find('.done').show();
          var votes = parseInt(el.parent().parent().find('.votes_count').html())+1;
          if(el.parent().parent().hasClass('stream_comment')){
            el.closest('.stream_comments').attr('data-votes', votes);
          }else{
            el.parent().parent().attr('data-votes', votes);
          }
          el.parent().parent().find('.votes_count').text(votes);
          el.parent().parent().attr('data-votes', votes);
          el.hide();
          swal({title: "Thank you!", type: "success"});
          refresh_comment_votes();
        },
        error: function (data) {
          sweetAlert('Oops...', 'Only registered user have the ability to vote!', 'error');
        }
      });
    }

    function CommentVoteDown(el, comment_id) {
      el = $(el);
      var url = "{{ secure_url('comment_vote_down') }}";
      $.ajax({
        url: url,
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "comment_id": comment_id},
        cache: false,
        success: function (data) {
          if (data.msg) {
            swal({title: data.msg, type: "error"});
            return;
          }
          el.parent().parent().find('.vot').show();
          var votes = parseInt(el.parent().parent().find('.votes_count').html())-1;
          if(el.parent().parent().hasClass('stream_comment')){
            el.closest('.stream_comments').attr('data-votes', votes);
          }else{
            el.parent().parent().attr('data-votes', votes);
          }
          el.parent().parent().find('.votes_count').text(votes);
          el.hide();
          swal({title: "Thank you!", type: "success"});
          refresh_comment_votes();
        },
        error: function (data) {
          sweetAlert('Oops...', 'Only registered user have the ability to vote!', 'error');
        }
      });
    }

    function refresh_comment_votes() {
      var sortedDivs = $(".parent_comment:not(.stream_comment)").toArray().sort(sorter);
      $.each(sortedDivs, function (index, value) {
        $("#comments-div").append(value);
      });
    }

    function sorter(a, b) {
      var contentA =parseInt( $(a).attr('data-votes'));
      var contentB =parseInt( $(b).attr('data-votes'));
      contentB = (contentB)?contentB:0;
      contentA = (contentA)?contentA:0;
      return (contentA > contentB) ? -1 : (contentA < contentB) ? 1 : 0
    }

    (function () {
      refresh_comment_votes();
      // $('.parent_comment .replies').first().css('margin-left', '10px');
      $('[data-toggle="tooltip"]').tooltip();
      var filters = {
        type: null,
        quality: null,
        language: null,
        mobile: null
      };

      function updateFilters() {
        $('.clickable-row').hide().filter(function () {
          var self = $(this), result = true;

          Object.keys(filters).forEach(function (filter) {
            if (filters[filter] && (filters[filter] != 'None') && (filters[filter] != 'Any')) {
              result = result && filters[filter] === self.data(filter);
            }
          });

          return result;
        }).show();
      }

      function bindDropdownFilters() {
        Object.keys(filters).forEach(function (filterName) {
          $('#' + filterName + '-filter').on('change', function () {
            if (filterName == 'mobile') {
              filters[filterName] = $(this).prop('checked') ? '' : 'Yes';
              console.log(filters[filterName]);
            } else {
              filters[filterName] = this.value;
              console.log(filters[filterName]);
            }

            updateFilters();
          });
        });
      }

      bindDropdownFilters();
    })();

    $(function ($) {
      $("#getting-started")
        .countdown( new Date().getTime() + {{ $offset_start }}, {elapse: true})
        .on('update.countdown', function (event) {
          var $this = $(this);
          if (event.elapsed) {
            $('#fake_count').hide();
            $('#real_count').show();
            $this.html('');
            $this.countdown('stop');
          } else {
            $('#fake_count').show();
            $('#real_count').hide();
            $this.html(
              event.strftime('<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><span>%D</span>Days</div>' +
                '<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><span>%H</span>Hours</div>' +
                '<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><span>%M</span>Minutes</div>' +
                '<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"><span>%S</span>Seconds</div><div class="clearfix"></div>')
            );
          }
        });
      $("#getting-finished")
        .countdown( new Date().getTime() + {{ $offset_end }}, {elapse: true})
        .on('update.countdown', function (event) {
          var $this = $(this);
          if (event.elapsed) {
            $('#fake_count').remove();
            $('#real_count').remove();
            $('#match_end').show();
            $this.html('');
            $this.countdown('stop');
            $('#streams-table tbody').html('<tr><td colspan="11" align=center>This match is ended!</td></tr>');
          }
        });

      $('#offset').change(function(){
        console.log($(this).val());
        var oldTime = $('#eventTime').attr('data-eventtime');
        console.log(oldTime);
        var utcStart = moment.utc(oldTime).utcOffset('UTC');
        console.log(utcStart);
        var startDate = utcStart.utcOffset($(this).val() * 60).format('HH:mm');
        console.log(startDate);
        $('#eventTime').html(startDate);
      });
    });

    function voteUp(el, stream,eventId) {
      if( $(el.parentElement).hasClass('disabled') )
        return;
      var url = "{{ secure_url('vote') }}";
      $.ajax({
        url: url,
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "stream": stream,"eventId":eventId},
        cache: false,
        success: function (data) {
          var rate = el.parentElement.nextElementSibling.innerText;
          if( data == 1 )
            $(el.parentElement).toggleClass('disabled');

          $(el.parentElement.nextElementSibling.nextElementSibling).removeClass('disabled');
          el.parentElement.nextElementSibling.innerHTML = parseInt(rate) + 1;
          swal({title: "Thank you!", type: "success"});
        }
      });
    }

    function voteDown(el, stream, eventId) {
      if( $(el.parentElement).hasClass('disabled') )
        return;
      var url = "{{ secure_url('voteDown') }}";
      $.ajax({
        url: url,
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "stream": stream,"eventId":eventId},
        cache: false,
        success: function (data) {
          var rate = el.parentElement.previousElementSibling.innerText;
          if( data == 1 )
            $(el.parentElement).toggleClass('disabled');

          $(el.parentElement.previousElementSibling.previousElementSibling).removeClass('disabled');
          el.parentElement.previousElementSibling.innerHTML = parseInt(rate) - 1;
          swal({title: "Thank you!", type: "success"});
        }
      });
    }

    function getEventComments() {
      var eventId = "{{ $event->event_id }}";
      var url = "{{ secure_url('getEventComments') }}";
      var orderType = $('#commentSort').val();
      $.ajax({
        url: url,
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "eventId":eventId, "orderType": orderType},
        cache: false,
        success: function (data) {
          $('.panel-group.panel-comments').html(data);

		  $('textarea').sceditor({
						toolbar: 'bold,italic,underline,color,emoticon',
						style: '/css/jquery.sceditor.default.css',
						height: 150,
						width: '95%'
					});
					
		  /*
          tinymce.init({
            selector: 'textarea',
            height: 100,
            theme: 'modern',
            plugins: [
              'advlist autolink lists link image charmap print preview hr anchor pagebreak',
              'searchreplace wordcount visualblocks visualchars code fullscreen',
              'insertdatetime media nonbreaking save table contextmenu directionality',
              'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc help'
            ],
            menubar: false,
            toolbar: 'bold italic forecolor backcolor emoticons',
            image_advtab: true,
            templates: [
              { title: 'Test template 1', content: 'Test 1' },
              { title: 'Test template 2', content: 'Test 2' }
            ],
            content_css: [
              '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
              '//www.tinymce.com/css/codepen.min.css'
            ],
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                });
              }
           });
		   */
        }
      });
    }

    function report(el, stream,eventId) {
      var url = "{{ secure_url('report') }}";
      swal({
          title: "Report a stream!",
          text: "Please add a comment before submit your report:",
          type: "input",
          showCancelButton: true,
          closeOnConfirm: false,
          animation: "slide-from-top",
          inputPlaceholder: "Write something"
        },
        function (inputValue) {
          if (inputValue === false) return false;

          if (inputValue === "") {
            swal.showInputError("You need to write something!");
            return false
          } else {
            $.ajax({
              url: url,
              type: "post",
              data: {"_token": "{{ csrf_token() }}", "stream": stream,"eventId":eventId, "comment": inputValue},
              cache: false,
              success: function (data) {
                $(el).hide();
                $(el.parentElement.lastElementChild).show();
              }
            });
          }

          swal("Thank you!");
        });

    }

    function comments(stream) {
      var url = "{{ secure_url('getComments') }}";
      axios.get(url + '/' + stream)
        .then(function (response) {
          $('#commentsModal').modal();
          $('#modal-body').html(response);
          $('#commentsModal').on('shown.bs.modal', function () {
            $('#commentsModal .modal-body').html(response);
            $('#modal-body').html(response);
          });
          $('#commentsModal').on('hidden.bs.modal', function () {
            $('#commentsModal .modal-body').data('');
          });
          console.log(response);
        })
        .catch(function (error) {
          console.log(error);
        });
    }

    function addComment(el, stream_id) {
      var comment = el.find('textarea').val();
      if (comment == '' || stream_id == '') {
        sweetAlert("Oops...", "Can't leave an empty comment!", "error");
        return false;
      }
      axios.post("{{ secure_url('saveComment') }}", {
        stream_id: stream_id,
        comment: comment,
        event_id: "{{ $event->event_id }}"
      })
        .then(function (response) {
          $('.comment').val('');
          $('#commentsModal').modal('hide');
          $('#collapseComments').addClass('in');
          if(stream_id){
            $('.stream_'+stream_id).parent().find('.comments').prepend(response.data);
            $('.stream_'+stream_id).parent().removeClass('hidden');
            $('.stream_'+stream_id+":not(.media-body .stream_"+ stream_id +")").addClass('hidden');
            var comments_count_div = $('tr[data-stream-id="'+ stream_id +'"]').find('.comments_count');
            var comments_count = parseInt(comments_count_div.text())+1;
            comments_count_div.text(comments_count);
          }
          $('.parent_comment .replies').first().css('margin-left', '10px');
        })
        .catch(function (error) {
          console.log(error);
        });
    }

    function stream_comment_init(stream_id) {
      if($('.stream_'+stream_id).parent().hasClass('media-body')){
        $('.stream_'+stream_id).parent().find('.reply_button').first().click();
        $('hmtl, body').scrollTop($('.stream_'+stream_id).parent().find('.reply_button').offset().top);
      }else{
        $('.stream_'+stream_id).parent().removeClass('hidden');
        $('.stream_'+stream_id).removeClass('hidden');
        $('.stream_comment_row.stream_'+stream_id).parent().find('.streamCommentAdd').show();
        $('hmtl, body').animate({
          scrollTop: $('.stream_'+stream_id).parent().find('.streamCommentAdd').offset().top
        }, 1000);
      }
    }

    function streamAction(el, streamId) {
      swal({
          title: "Are you sure?",
          text: "You will not be able to recover this stream!",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Yes, delete it!",
          closeOnConfirm: false
        },
        function () {
          $.post(
            $(el).attr('data-href'),
            {"_token": "{{ csrf_token() }}"},
            function (data, status) {
              console.log(data)
            });
          if( $(el).closest('.stream_block').length > 0 )
            $(el).closest('.stream_block').hide();
          else
            $(el).closest('tr').slideUp('slow');
          swal("Deleted!", "Stream has been deleted.", "success");
        });
    }
  </script>

  @if(\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role>=1)

    <script>
      function recommend(el, stream, eventID, action ) {
        var url = "{{ secure_url('recommend') }}";
        $.ajax({
          url: url,
          type: "post",
          data: {"_token": "{{ csrf_token() }}", "stream": stream, "event": eventID, "action": action },
          cache: false,
          success: function (data) {
            var tr = el.closest('.stream_block');
            if( action == 1 ){
              tr.className += " highlight";

              $html = '<a href="javascript:void(0);" onclick="recommend(this,\''+stream+'\', \''+eventID+'\', 0 )" title="Undo Recommend"><i class="fa fa-hand-o-down" style="color: red" aria-hidden="true"></i></a>';

              $(el).parent().html( $html );
              $(tr).attr("data-toggle", "tooltip");
              $(tr).attr("data-placement", "left");
              $(tr).attr("data-original-title", "Recommended");
              $(tr).tooltip('show');
              swal({title: "Successfully recommended.", type: "success"});
            }
            else{
              tr.classList.remove('highlight');

              $html = '<a href="javascript:void(0);" onclick="recommend(this,\''+stream+'\', \''+eventID+'\', 1 )" title="Recommend"><i class="fa fa-hand-o-up" style="color: red" aria-hidden="true"></i></a>';

              $(el).parent().html( $html );
              $(tr).removeAttr('data-toggle');
              $(tr).removeAttr('data-original-title');

              $(tr).tooltip('hide');
              swal({title: "Unrecommended.", type: "success"});
            }
          }
        });
      }

      function banUserAction(el, streamId,e) {
        e.stopPropagation();
        swal({
            title: "Are you sure?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, Ban this user!",
            closeOnConfirm: false
          },
          function () {
            $.get(
              $(el).attr('data-href'),
              {"_token": "{{ csrf_token() }}"},
              function (data, status) {
                console.log(data)
              });
            $(el).closest('tr').slideUp('slow');
            swal("Done!", "Successfully.", "success");
          });
      }

      function deleteComment(commentId) {
        var $this = $('#comment_'+commentId);
        swal({
            title: "Are you sure?",
            text: "You will not be able to recover this comment!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false
          },
          function () {
            var deleteURL = '{{ secure_url('deleteComment') }}';
            axios.post(deleteURL, {
              id: commentId,
            }).then(function (response) {
              if ($this.parent().parent().hasClass('stream_comments') && $this.parent().parent().find('.media').length == 1) {
                $this.parent().parent().addClass('hidden');
              }
              if(!$this.parent().hasClass('replies') && $this.parent().parent().parent().find('.stream_comment_row').length){
                var comments_count_div = $('tr[data-stream-id='+ $this.parent().parent().find('.stream_comment_row').data('stream-id') +'] .comments_count');
                var comments_count = parseInt(comments_count_div.text())-1;
                comments_count_div.text(comments_count);
              }
              $('#comment_' + commentId).remove();
              swal("Deleted!", "Comment has been deleted.", "success");
            });
          });
      }

      function banDomainAction(el, streamId,e) {
        e.stopPropagation();
        swal({
            title: "Are you sure?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, Ban it!",
            closeOnConfirm: false
          },
          function () {
            console.log( $(el).attr('data-href') );
            $.get(
              $(el).attr('data-href'),
              {"_token": "{{ csrf_token() }}"},
              function (data, status) {
                console.log(data);
                location.reload();
              });
            swal("Banned!", "Successfully", "success");
          });
      }

      function sendMessage( el, userId, e, url )
      {
        e.stopPropagation();
        alertify.prompt('Send Message', 'You can send message to this user.', ''
          , function (evt, value) {
            if( !value ){
              alertify.error('Empty message.');
              return;
            }

            console.log( value );
            $.post(
              '{{ secure_url('/profile/messages/send') }}',
              {"_token": "{{ csrf_token() }}", "userId": userId, "body": value, "permalink": url},
              function (data, status) {
                console.log(data)
                alertify.success('Message sent')
              });
          }
          , function () {
            alertify.error('Cancel')
          });
      }
    </script>
  @endif

<script type="text/javascript">

    var el_tooltip = $('.highlight[data-toggle="tooltip"]');

    window.onload = function() {
        var slug = window.location.hash.substr(1);
        if( slug == "" ){
            setTimeout(function () {
                el_tooltip.tooltip({trigger: 'manual'}).tooltip('hide');
            }, 1000);

            return;
        }

        el = $('table [data-slug="'+slug+'"]');

        if (el.length < 1) return;
        if( el.parent().parent().hasClass('stream_block'))
          el = el.parent().parent();

        var elOffset = el.offset().top;
        var elHeight = el.height();
        var windowHeight = $(window).height();
        var offset;

        if (elHeight < windowHeight) {
            offset = elOffset - ((windowHeight / 2) - (elHeight / 2));
        }
        else {
            offset = elOffset;
        }

        target = $('table [data-slug="'+slug+'"]').parent();
        target.addClass('highlighed_cell');
        target.siblings().addClass('highlighed_cell');
        $('.highlighed_cell [data-toggle="tooltip"]').parent().css('z-index', 1700);
        $('.highlighed_cell .verified').parent().css('z-index', 1800);

        if( el.hasClass('stream_block'))
        {
          element = $('.highlighed_cell').parent();
          $('.stream_block').removeClass('active');
          element.addClass('active');

          var siblings = element.siblings().length;
          width = 100 - 6 * ( siblings );
          element.css('width', 'calc('+ width+'%' + ' - ' + (siblings+1) + 'px)');

          $('.stream_block').not('.active').css('width', '6%');

        }

        el_tooltip.tooltip('hide');

        $('.blurry').show();
        $('html, body').animate({
            scrollTop: offset
        }, 1000, function(){
            el_tooltip.tooltip({trigger: 'manual'}).tooltip('hide');
        });
    };

    window.onresize = function() {
        el_tooltip.tooltip('hide');
    }

  $('.collapse').on('shown.bs.collapse', function(){
    $(this).parent().find(".fa-expand").removeClass("fa-expand").addClass("fa-compress");
  }).on('hidden.bs.collapse', function(){
    $(this).parent().find(".fa-compress").removeClass("fa-compress").addClass("fa-expand");
  });

  $(".blurry").on('click', function(){
      $(this).hide();
      // $('.highlighed_cell').removeClass('highlighed_cell');
  });

  function edit_form( streamId )
  {
      if( !streamId )
          return;
      $.post(
        '{{ secure_url('/streamInfo') }}',
        {"_token": "{{ csrf_token() }}", "stream_id": streamId },
        function (data, status) {
          streamInfo = JSON.parse( data );

          $('#stream_id').val( streamId );
          $('#type_selector').val( streamInfo.stream_type );
          $('#url').val( streamInfo.url );
          $('#language_selector').val( streamInfo.language );
          $('#stream_quality').val( streamInfo.quality );
          if( streamInfo.compatibility == "Yes")
              $('#mobile_compatible').attr('checked', true);

          $('#adNumber').val( streamInfo.ad_number );
          if( streamInfo.nsfw == 1 )
              $('#nsfw').attr('checked', true);
          $('#otherInfo').val( streamInfo.other_info );
          $('.selectpicker').selectpicker('refresh');
        });
  }

  $(function ($) {
    $('#streamForm').parsley();
    $('.selectpicker').selectpicker({
      style: 'btn-default',
      size: 6
    });
  });

  function selectType(el) {
    var streamType = el.value;
    if (streamType == 'http') {
      $('#url').prop('disabled', false);
      $('#url').attr("data-parsley-pattern", "(?=(http:\/\/|https:\/\/)).*");
      $('#url').attr('data-parsley-error-message', 'Please only use http://, https://');
    }
    else if (streamType == 'Acestream') {
      $('#url').prop('disabled', false);
      $('#url').attr("data-parsley-pattern", "(?=(acestream:\/\/)).*");
      $('#url').attr('data-parsley-error-message', 'Please only use acestream://');
    }
    else if (streamType == 'sopcast') {
      $('#url').prop('disabled', false);
      $('#url').attr("data-parsley-pattern", "(?=(sop:\/\/)).*");
      $('#url').attr('data-parsley-error-message', 'Please only use sop://');
    }
    else if (streamType == 'VLC') {
      $('#url').prop('disabled', false);
      $('#url').attr("data-parsley-pattern", "(?=(http:\/\/|https:\/\/)).*");
      $('#url').attr('data-parsley-error-message', 'Please only use http://, https://');
    }
    else if (streamType == 'Other') {
      $('#url').disabled = false;
      $('#url').removeAttr("data-parsley-pattern");
      $('#url').removeAttr('data-parsley-error-message');
    }
  }

  $('#submitForm').click(function(e) {
      selectType( document.getElementById('type_selector') );
      e.preventDefault();

      if( $('form#streamForm').parsley().validate() )
      {
        $.post(
          "{{ secure_url('updateStream') }}",
          $('form#streamForm').serialize(),
          function ( res, status1) {
              if( res == 1 )
                swal({title: "Successfully updated!", type: "success"});
              else
                swal({title: res, type: "error"})
        });
      }
  });

    //for Whatch button toolTip
    $(".geoLock, .isExpandable").click(function () {
      var dataId = $(this).attr('data-stream-id');
      if( tr_el.hasClass('open') ) {
        $(".tooltip_" + dataId).show();
        //$('.btnWatch_'+ dataId).removeClass('not-active');
      }else{
        $(".tooltip_" + dataId).hide();
        //$('.btnWatch_'+ dataId).addClass('not-active');
      }
    });
    //End

  $(".clickable").click(function () {
      tr_el = $(this).parent();
      if( tr_el.hasClass('geoLock') || tr_el.hasClass('isExpandable') )
          return;
      else
          window.open(tr_el.data("href"));
  });

  $(".geoLock>td").click(function () {

    tr_el = $(this).parent();
    if( tr_el.hasClass('geoLock') )
    {
      if( tr_el.hasClass('open') )
      {
        tr_el.removeClass('open');
        $('.append_tr td > div').slideUp( 300, function(){ $('.append_tr').remove();} );
      }
      else{
        $('.append_tr').remove();
        $('.append_tr td > div').slideUp( 300, function(){ $('.append_tr').remove();} );

        tr_el.addClass('open');
        countTD = $(this).siblings().length + 1;

        //Sponsor banner
        var sponsorBanner = '';
        if(tr_el.hasClass('sponsor')){
          /*sponsorBanner = '<div class="row">'+
              '<div class="col-xs-12" style="margin-bottom:10px;">' +
              '<img src="/images/banners/v01.jpg" class="img-responsive"/></div>' +
              '<div class="col-xs-12" style="margin-bottom:10px">' +
              '<img src="/images/banners/v02.jpg" class="img-responsive"/></div>'+
              '</div>';*/
          sponsorBanner = '<div class="row">'+
              '<div class="col-xs-12" style="margin-bottom:10px;">' +
              '<a href="https://www.fubo.tv/watch/SoccerStreams?irad=366554&irmp=376982" target="_blank"><img src="/images/banners/v01.jpg" class="img-responsive"/></a></div>' +
              '</div>';
        }

        html = "";


        // html = sponsorBanner +'<p>This stream is geo-locked. Please consider using one of the following VPN services to support us.</p>'+
        //         '<div class="row"><div class="col-xs-3"></div>'+
        //           '<div class="col-xs-2 vpnLogo" style="border: 1px solid #6cd22d;">' +
        //         '<span class="vpnlogo-content"><div class="vpnlogo-text">For Best Speed</div><a href="https://billing.purevpn.com/aff.php?aff=30171" target="_new">'+
        //             '<img src="/images/vpn/purevpn.png" class="img-responsive"/></a></span></div>'+
        //         '<div class="col-xs-2 vpnLogo" style="border: 1px solid #d60017;">' +
        //         '<span class="vpnlogo-content"><div class="vpnlogo-text">For Best Speed</div><a href="https://www.linkev.com/?a_aid=streams17" target="_new">'+
        //         '<img src="/images/vpn/vpn-express.png" class="img-responsive"/>'+
        //         '</a></span></div>'+
        //           '<div class="col-xs-2 vpnLogo" style="border: 1px solid #0063a9;">' +
        //         '<span class="vpnlogo-content"><div class="vpnlogo-text">For Best security</div><a href="https://go.nordvpn.net/aff_c?offer_id=15&aff_id=4565" target="_new">'+
        //             '<img src="/images/vpn/vpn-nord.png" class="img-responsive"/></a></span></div>'+
        //         '</div>';


        if( tr_el.next().hasClass('tooltip'))
            tr_el.next().after('<tr class="append_tr"><td style="background:#faf3b3;padding:0;position:relative;z-index:1500;text-align: center;" colspan='+countTD+'>'+html+'</td></tr>');
        else
            tr_el.after('<tr class="append_tr"><td style="background:#faf3b3;padding:0;position:relative;z-index:1500;text-align: center;" colspan='+countTD+'>'+html+'</td></tr>');

        $('.append_tr').children('td').wrapInner('<div>');
        $('.append_tr td > div').css( { "display":"none", "padding": "20px" });
        $('.append_tr td > div').slideDown( 300 );
      }
    }
    else
      window.open(tr_el.data("href"));
  });

    //$(".isExpandable > td, .clickable").click(function () {
    $(".isExpandable>td").click(function () {
      tr_el = $(this).parent();
      if( tr_el.hasClass('isExpandable') )
      {
        if( tr_el.hasClass('open') )
        {
          tr_el.removeClass('open');
          $('.append_tr td > div').slideUp( 300, function(){ $('.append_tr').remove();} );
        }
        else{
          $('.append_tr').remove();
          $('.append_tr td > div').slideUp( 300, function(){ $('.append_tr').remove();} );

          tr_el.addClass('open');
          countTD = $(this).siblings().length + 1;
          /*html = '<div class="row">'+
                  '<div class="col-xs-12" style="margin-bottom:10px;">' +
                  '<img src="/images/banners/v01.jpg" class="img-responsive"/></div>' +
                  '<div class="col-xs-12">' +
                  '<img src="/images/banners/v02.jpg" class="img-responsive"/></div>'+
                  '</div>';*/
          html = '<div class="row">'+
                  '<div class="col-xs-12" style="margin-bottom:10px;">' +
                  '<a href="https://www.fubo.tv/watch/SoccerStreams?irad=366554&irmp=376982" target="_blank"><img src="/images/banners/v01.jpg" class="img-responsive"/></a></div>' +
                  '</div>';


          if( tr_el.next().hasClass('tooltip'))
            tr_el.next().after('<tr class="append_tr"><td style="background:#faf3b3;padding:0;position:relative;z-index:1500;text-align: center;" colspan='+countTD+'>'+html+'</td></tr>');
          else
            tr_el.after('<tr class="append_tr"><td style="background:#faf3b3;padding:0;position:relative;z-index:1500;text-align: center;" colspan='+countTD+'>'+html+'</td></tr>');

          $('.append_tr').children('td').wrapInner('<div>');
          $('.append_tr td > div').css( { "display":"none", "padding": "20px" });
          $('.append_tr td > div').slideDown( 300 );
        }
      }
      else
        window.open(tr_el.data("href"));
    });


    /*Real time score update*/

    function updateScore(updateCheck){
      var id = $("#event_minute").data("id");
      var data = [id];

      $.ajax({
        url: document.location.origin + '/updatehomepagescores',
        dataType: 'json',
        data: {
          data: data
        },
        success: function (result) {
          if( !(result['result'] == 0) )
            for (var i = result['result'].length - 1; i >= 0; i--) {
              if(result['result'][i]["event_minute"] != null)
                $("#event_minute").text("'"+result['result'][i]["event_minute"]);
              if(result['result'][i]["event_status"] != null)
                $("#event_score").text(result['result'][i]["event_status"]);
            }
        },
        error: function(result) {
        }
      });
    }

    if($('#event_minute').data("running") && $('#updateScore').attr("value") == "checked"){
      setInterval(function(){
        updateScore();
      }, 60000)
    }
    /*End of real time score update*/

    /* Multiple Stream Features */
    $('.stream_heading').hover( function(){
      el = $(this).parent();
      el.siblings().removeClass('active');
      el.addClass('active');

      var siblings = el.siblings(".stream_block:visible").length;
      width = 100 - 6 * ( siblings );

      el.css('width', 'calc('+ width+'%' + ' - ' + (siblings+1) + 'px)');
      $('.stream_block').not('.active').css('width', '6%');

    });

    $('.multiple_streams').each( function(){
      $(this).children().first().addClass('active');
    });

    $('.stream_block.active').each(function(){
      var siblings = $(this).siblings(".stream_block:visible").length;
      width = 100 - 6 * ( siblings );
      $(this).css('width', 'calc('+ width+'%' + ' - ' + (siblings+1) + 'px)');
      $('.stream_block').not('.active').css('width', '6%');

    });

    $('#streams').on('change', function (e) {
      $('.stream_block.active').each(function(){
        var siblings = $(this).siblings(".stream_block:visible").length;
        width = 100 - 6 * ( siblings );
        $(this).css('width', 'calc('+ width+'%' + ' - ' + (siblings+1) + 'px)');
        $('.stream_block').not('.active').css('width', '6%');
      });

      $('#streams-table tr:not(.clickable-row)').each(function(){
        if($(this).children(':visible').length == 0) {
           $(this).hide();
        }
        else
          $(this).show();
      });
    });

    $('#streams-table tr').each(function(){
      if($(this).children().length == 1 && $(this).children('td').children().length == 0 ){
        $(this).hide();
      }
    });

    $('#streams-table tr .dropdown').hover(
      function(){
        $(this).closest('tr.stream_block').children().css('padding-bottom', '120px');
        $(this).closest('div.stream_block').children().css('margin-bottom', '120px');
        $(this).parent().css('overflow', 'visible');
      },

      function () {
        $(this).closest('tr.stream_block').children().css('padding-bottom', '8px');
        $(this).closest('div.stream_block').children().css('margin-bottom', '0');
        $(this).parent().css('overflow', 'hidden');
       }
    );

    $('#streams-table tr .dropdown-content').click( function(){
        $(this).closest('tr.stream_block').children().css('padding-bottom', '8px');
        $(this).closest('div.stream_block').children().css('margin-bottom', '0');
        $(this).parent().parent().css('overflow', 'hidden');
    });

    /* End multiple stream */
</script>
<script  type="text/javascript">
  $(document).ready(function(){
    // var width_taotal = parseFloat(window.getComputedStyle($('div.array-center-livestream-button').get(0)).width);
    // var width_item = parseFloat(window.getComputedStyle($('div.filter').get(0)).width);
    // var width_value = (width_taotal - (width_item * 4 + 30))/2;
    // if($(window).width() > 358){
    //   $('div.array-center-livestream-button').css('padding-left',width_value);
    // }
    // else{
    //   $('div.array-center-livestream-button').css('padding-left','5');
    // }
  });
  // $( window ).resize(function() {
  //   var width_taotal = parseFloat(window.getComputedStyle($('div.array-center-livestream-button').get(0)).width);
  //   var width_item = parseFloat(window.getComputedStyle($('div.filter').get(0)).width);
  //   var width_value = (width_taotal - (width_item * 4 + 30))/2;
  //   if($(window).width() > 358){
  //     $('div.array-center-livestream-button').css('padding-left',width_value);
  //   }
  //   else{
  //     $('div.array-center-livestream-button').css('padding-left','5');
  //   }
  // });
</script>
@endsection
<div class="blurry"></div>
<style>
  .color-gold {
    color: #B3994C;
  }
  .modal{ z-index: 2050 !important};
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
