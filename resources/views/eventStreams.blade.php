@extends('master')
@section('title',$event->event_title.' streams - ')
@section('content')
  <link rel="stylesheet" href="{{ secure_asset('css/global.css') }}">
  <link href="{{ secure_asset('css/socicon/socicon_eventStreams.min.css') }}" rel="stylesheet">
  <link href="{{ secure_asset('css/tags.min.css') }}" rel="stylesheet">
  {{--<script src="{{ secure_asset('js/tags.js') }}"></script>--}}
  {{--<script src="{{ secure_asset('plugins/clipboard.min.js') }}"></script>--}}
  <script src="{{ secure_asset('plugins/clipboard_tags_min.js') }}"></script>

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
  <script src="{{ secure_asset('js/axios.min.js') }}"></script>
  <script src="{{ secure_asset('js/jquery.countdown.min.js') }}"></script>

  <script src="{{ secure_asset('js/eventStreams_func.js') }}"></script>
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