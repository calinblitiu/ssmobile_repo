@extends('master')
@section('title',$event->event_title.' streams - ')
@section('content')
  <link rel="stylesheet" href="{{ secure_asset('css/global.css') }}">
  <link href="{{ secure_asset('css/socicon/socicon.css') }}" rel="stylesheet">
  <link href="{{ secure_asset('css/tags.css') }}" rel="stylesheet">
  <script src="{{ secure_asset('js/tags.js') }}"></script>
  <script src="{{ secure_asset('plugins/clipboard.min.js') }}"></script>
  <style>
    a {
      color: #FFF;
    }
    
    h2 a {
      color: #45556c !important;
    }
    
    .breadcrumbs {
      line-height: 17px !important;
      background: #B3994C !important;
    }
    
    .new-result-container .breadcrumbs div:not(.container):last-of-type {
      color: #F7F7F7 !important;
    }
    
    .share-block {
      /*background: #B3994C;*/
      color: #FFF !important;
    }
    
    .share-block a.fb:hover {
      background: #3e5b98 !important;
    }
    
    .share-block a.tw:hover {
      background: #4da7de !important;
    }
    
    .share-block a.gp:hover {
      background: #dd4b39 !important;
    }
    
    .share-block a.vk:hover {
      background: #5a7fa6 !important;
    }
    
    .share-block a.tl:hover {
      background: #45556c !important;
    }
    
    #getting-started div span {
      line-height: 35px;
    }
    
    .logo-holder span:before {
      background: none !important;
    }
    
    #http_events, #p2p_events {
      margin-top: 20px;
    }
    
    #http_events h4, #p2p_events h4 {
      background-color: #00222E;
      border-bottom: 2px solid #B3994C !important;
      padding: 10px;
      color: #FFF;
    }
    
    .no_underline, .no_underline:hover {
      text-decoration: none !important;
    }
    
    .clickable-row {
      cursor: pointer;
    }
    
    .watermark {
      display: block;
      position: relative;
    }
    
    .watermark::after {
      content: "";
      background: url('{{ secure_asset('images/competitions/'.$event->competition_logo) }}');
      background-repeat: no-repeat;
      background-position: center;
      opacity: 0.09;
      top: 0;
      left: 0;
      bottom: 0;
      right: 0;
      position: absolute;
      z-index: -1;
    }
    
    .small_icon {
      width: 15px !important;
    }
    
    .medium_icon {
      width: 44px !important;
    }
    
    .event-social-block {
      display: inline;
    }
    
    @media (max-width: 768px) {
      .event-social-block {
        display: block;
      }
      
      body .new-result-container .breadcrumbs div:not(.container)::after {
        border-width: 15px 0px 15px 10px;
      }
      
      body .new-result-container .breadcrumbs div:not(.container) a {
        padding: 7px 4px 7px 13px;
      }
      
      body .new-result-container .breadcrumbs {
        font-size: 10px;
      }
      
      body .new-result-container .count span {
        font-size: 80px;
      }
      
      body .new-result-container .count {
        font-size: 40px;
      }
      
      body .new-result-container #getting-started > div span {
        font-size: 25px;
      }
    }
    
    /* Important part */
    .modal-dialog {
      overflow-y: initial !important
    }
    
    .modal-body {
      height: 250px;
      overflow-y: auto;
    }
    
    .verified {
      color:             #07212B !important; /*Dark blue*/
      background:        #B59B48 !important; /*Gold*/
      border:            solid;
      border-color:      #07212B !important; /*Dark blue*/
      width:             130px;
      border-radius:     20px;
      display: inline-block;
      position: relative;
      cursor: help;
    }
  
    /*Hover*/
    .verified:before {
      content: attr(verified-hover-text);
      display: none;
      position: absolute;
      color:             #B59B48 !important; /*Dark blue*/
      background:        #07212B !important; /*Gold*/
      border-color:      #B59B48 !important; /*Dark blue*/
      padding: 4px 8px;
      font-size: 12px;
      min-width: 100px;
      text-align: left;
      border-radius: 4px;
      white-space: nowrap;
    }
  
    [verified-hover-position="top"]:before{
      top: 100%;
      margin-top: 6px;
      margin-left: -6px;
      border-width: 6px 6px 0;
      border-color: #000 !important;
    }
  
    [verified-hover-text]:after {
      content: '';
      display: none;
      position: absolute;
      width: 0;
      height: 0;
      border-color: transparent;
      border-style: solid;
    }
  
    [verified-hover-text]:hover:before{
      display: block;
      z-index: 50;
    }
  </style>
  <div class="new-result-container">
    <div class="breadcrumbs">
      <div class="container">
        <div><a class="no_underline">{{ $event->nation_name }}</a></div>
        <div><a class="no_underline">{{ $event->competition_name }}</a></div>
        <div><a class="no_underline" href="{{ url()->current() }}"><span>{{ $event->event_title }}</span></a></div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
        </div>
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4 visible-sm visible-xs">
        </div>
        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 nopadding">
          <div class="match-info">
            <img style="max-width: 40px" src="{{ secure_asset('images/competitions/'.$event->competition_logo) }}" alt="{{ $event->competition_name }}"
                 title="{{ $event->competition_name }}">
            <p>{{ $event->event_title }}</p>
            @if(!is_null($event->round_name) && !empty($event->round_name))
              <p>{{ $event->round_name }}</p>
            @endif
            
            @if(!is_null($event->game_week))
              <p>Week {{ $event->game_week }}</p>
            @endif
            
            <p>
              <span>Date:</span> {{ \Carbon\Carbon::parse($event->start_date)->addHours(Session::get('visitorTZ'))->format('jS F Y') }}
            </p>
            <p>
              <span>Time:</span> {{ \Carbon\Carbon::parse($event->start_date)->addHours(Session::get('visitorTZ'))->format('H:i') }}
            </p>
          </div>
          <div class="count" id="real_count" style="display: none">
            @if(!empty($event->event_status))
              <span>{{ $event->event_status }}</span>
            @endif
          </div>
          
          <div id="getting-started" data-countdown="{{ \Carbon\Carbon::parse($event->start_date)->addHours(Session::get('visitorTZ'))->format('Y/m/d H:i') }}"></div>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4 hidden-sm hidden-xs">
        </div>
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
          <a target="_blank" data-toggle="modal" class="btn gp" href="http://vk.com/share.php?url={{ url()->current() }}">
            <span class="socicon socicon-vkontakte"></span>
          </a>
          <a target="_blank" data-toggle="modal" class="btn tl" href="http://tumblr.com/widgets/share/tool?canonicalUrl={{ url()->current() }}m">
            <span class="socicon socicon-tumblr"></span>
          </a>
        </div>
      </div>
    </div>
  </div>

  <div id="http_events">
    <h4>HTTP & P2P</h4>
    <div class="row">
      <div class="col-md-4 filter">
        <label class="col-md-4" for="stream_selector">Stream Type </label>
        <div class="col-md-8">
          <select class="form-control" id="stream_selector">
            <option value="">All</option>
            @foreach($streamTypes as $type)
              <option value="{{ $type }}">{{ $type }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-striped table-hover" id="streams-table" width="100%">
        <thead>
        <tr>
          <td class="no-sort"></td>
          <td class="no-sort"></td>
          <td class="no-sort"></td>
          <td>Language</td>
          <td class="no-sort"></td>
          <td class="no-sort"></td>
          <td>Quality</td>
          <td class="no-sort"></td>
          <td class="no-sort"></td>
          <td class="no-sort"></td>
          <td class="no-sort"></td>
          <td class="no-sort"></td>
        </tr>
        </thead>
        <tbody>
        @if($vStream->count())
          @include('streamsTemplate',['streams'=>$vStream])
        @endif
        @if($streams->count())
          @include('streamsTemplate',['streams'=>$streams])
        @endif
        </tbody>
      </table>
    </div>
  </div>
  
  <div id="commentsModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Stream comments</h4>
        </div>
        <div class="modal-body">
        
        </div>
        <div class="modal-footer">
          @if (Auth::guest())
            <div class="input-group" id="doneBtns">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          @else
            <div class="input-group" id="actionBtns">
              <input type="text" name="comment" class="form-control comment" placeholder="Place your comment here"/>
              <span class="input-group-btn">
            <button type="button" class="btn btn-primary" onclick="addComment(this,0)">Add comment</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </span>
            </div>
            <div class="input-group" id="doneBtns" style="display: none;">
              <p class="alert alert-success text-center">Your comment added, thank you</p>
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          @endif
        
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
@endsection
@section('scripts')
  <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/alertify.min.css"/>
  <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/themes/default.min.css"/>
  <link rel="stylesheet" href="//cdn.jsdelivr.net/alertifyjs/1.9.0/css/themes/bootstrap.min.css"/>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
  <script src="{{ secure_asset('js/jquery.countdown.min.js') }}"></script>
  <script>
    $(function ($) {
      var streamsTable = $('#streams-table').DataTable({
        responsive: true,
        paging: false,
        info: false,
        ordering: false,
        dom: 'lrtip',
        columnDefs: [ {
          "targets"  : 'no-sort',
          "orderable": false,
        }],
        language: {
          "emptyTable": "There are no streams for this match right now, check again 1 hour before kickoff!"
        },
      });
      $('#stream_selector').on('change', function (e) {
        console.log(this.value);
        streamsTable.search(this.value).draw();
      });
      
      var copyStream = new Clipboard('.btn-copy');
      copyStream.on('success', function(e) {
        swal('Copied', e.text,"success");
      });
      
      $(".clickable").click(function () {
        window.open(
          $(this).parent().data("href"),
          '_blank'
        );
      });
      
      $("#getting-started")
        .countdown("{{ \Carbon\Carbon::parse($event->start_date)->addHours(Session::get('visitorTZ'))->format('Y/m/d H:i:s') }}", {elapse: true})
        .on('update.countdown', function (event) {
          var $this = $(this);
          if (event.elapsed) {
            $('#fake_count').hide();
            $('#real_count').show();
            $this.html('');
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
      
      $("#commentsModal").on("show.bs.modal", function (e) {
        var link = $(e.relatedTarget);
        $(this).find(".modal-body").load(link.attr("href"));
      });
      
    });
    
    function voteUp(el, stream) {
      var url = "{{ secure_url('vote') }}";
      $.ajax({
        url: url,
        type: "post",
        data: {"_token": "{{ csrf_token() }}", "stream": stream},
        cache: false,
        success: function (data) {
          var rate = el.parentElement.previousElementSibling.innerText;
          $(el).hide();
          $(el.parentElement.nextElementSibling).show();
          el.parentElement.previousElementSibling.innerHTML = parseInt(rate) + 1;
          swal({title: "Thank you!", type: "success"});
        }
      });
    }
    
    function report(el, stream) {
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
              url: secure_url,
              type: "post",
              data: {"_token": "{{ csrf_token() }}", "stream": stream, "comment": inputValue},
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
    
    function addComment(el) {
      var comment = $(el.parentElement.previousElementSibling).val();
      var stream_id = document.getElementsByName('streamId')[0].value;
      if (comment == '') {
        sweetAlert("Oops...", "Can't leave an empty comment!", "error");
        return false;
      }
      axios.post("{{ secure_url('saveComment') }}", {
        stream_id: stream_id,
        comment: comment
      })
        .then(function (response) {
          $('.comment').val('');
          $('.modal-body').append(response.data);
          $('.modal-body').animate({scrollTop: $('.modal-body').prop("scrollHeight")}, 500);
        })
        .catch(function (error) {
          console.log(error);
        });
    }
  
  </script>
  
  @if(\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role>=1)
    <script>
      function deleteComment(commentId) {
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
              $('#comment_' + commentId).hide();
            });
            swal("Deleted!", "Comment has been deleted.", "success");
          });
      }
    </script>
  @endif
@endsection