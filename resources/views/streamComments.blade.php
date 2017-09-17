<div class="panel-group panel-stream-comments" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading" data-toggle="collapse" data-parent="#accordion" href="#collapseStreamComments" aria-expanded="true" aria-controls="collapseOne" role="tab" id="panel-stream-comment" style="cursor:pointer">
        <span class="color-gold"><small style="font-size: 17px;">{{ $comment_count }}</small> COMMENTS</span>
        <i class="fa fa-compress pull-right" aria-hidden="true" style="font-size: 1.5em;"></i>
        <div class="clear-both"></div>
    </div>
    <div id="collapseStreamComments" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="panel-stream-comment">
      <div class="panel-body">
        <div class="stream-event-comments">
          @if(\Illuminate\Support\Facades\Auth::check())
            {{-- registered user comment box --}}
            <form id="postStreamComment" action="{{ secure_url('storeStreamComment') }}" method="post" novalidate>
              <input type="hidden" name="event_id" value="{{ $event_id }}">
              <input type="hidden" name="parent" value="0">
              {{ csrf_field() }}
              <div class="form-group">
                <label for="comment">Your Comment</label>
                <textarea name="comment" class="form-control" rows="3" required="required"></textarea>
              </div>
              <button type="submit" class="btn btn-default">Send</button>
            </form>
          @else
            <p class="mobile-view-comment-unregister-hide">Please <a href="{{ url('register') }}">register</a> to add your comment or <a href="{{ url('redditLogin') }}">login with Reddit.</a></p>
            <p class="mobile-view-comment-unregister-show1">Please<br /><a href="{{ url('register') }}">register </a>to add your comment <br />or <a href="{{ url('redditLogin') }}">login with Reddit.</a></p>
            <p class="mobile-view-comment-unregister-show2">Please<br /><a href="{{ url('register') }}">register </a>to add your comment or <a href="{{ url('redditLogin') }}">login with Reddit.</a></p>
          @endif
          {{-- end of registered user comment box --}}

          {{-- nested comments --}}
          <div class="row" id="stream-comments-div">
            @if($comments->count()>0)
                @if(count($treeComments)>0)
                  @foreach($treeComments as $comment)
                    @include('partials.comment', ['comment'=>$comment, 'user_comment_count'=>$user_comment_count])
                  @endforeach
                @endif
            @endif
            @if(false && count($streams))
                @if($allStreams->count())
                  @include('eventStreamsTemplate',['streams'=>$streams, 'comment_show' => true])
                @endif
            @endif
          </div>
          {{-- end of nested comments --}}
        </div>
      </div>
    </div>

  </div>
</div>
<style>
  .editable-pre-wrapped {
    white-space: initial !important;
  }

  .event-comments {
    padding-bottom: 9px;
    margin: 5px 0 5px;
  }

  .event-comments .comment-meta {
    /*border-bottom: 1px solid #eee;*/
    margin-bottom: 5px;
  }

 /* .event-comments .media {
    margin-bottom: 5px;
    padding-left: 10px;
  }*/

  .event-comments .media-heading {
    font-size: 12px;
    color: grey;
  }

  .event-comments .media-heading .left {
    float: left;
  }

  .event-comments .media-heading .right {
    float: right;
  }

  .event-comments .media-heading .right span {
    margin-left: 2px;
  }

  .event-comments .comment-meta a {
    font-size: 12px;
    color: grey;
    font-weight: bolder;
    margin-right: 5px;
  }

  #comments-div {
    padding: 15px;
  }

  .label-gold-rss {
    background-color: #B3994C;
  }

  a.btn-delete {
    color: #FFF !important;
    padding: 1px;
    background-color: red;
    text-decoration: none;
  }
</style>
<br>
