<div class="media{{ (!$comment->parent)?' parent_comment':'' }}" id="comment_{{ $comment->id }}">
  <div class="media-heading">
    <button class="btn btn-default btn-xs" type="button" data-toggle="collapse" data-target="#collapse_{{ $comment->id }}" aria-expanded="false"
            aria-controls="collapseExample">
      <span class="glyphicon glyphicon-minus" aria-hidden="true"></span>
    </button>
    <span class="label label-gold-rss">{{ $comment->name }}</span>
    @if(\Illuminate\Support\Facades\Auth::check())
      @if($comment->role==1)
        &nbsp;<small>(Moderator)</small>
      @elseif($comment->role==2)
        &nbsp;
        <small>(Admin)</small>
      @endif
    @endif
    &nbsp;{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $comment->created_at)->diffForHumans() }}
  </div>
  <div class="panel-collapse collapse in" id="collapse_{{ $comment->id }}">
    <!-- media-left -->
    <div class="media-body">
      <div id="_token" class="hidden" data-token="{{ csrf_token() }}"></div>
      <div id="commentContent_{{ $comment->id }}" class="editable" data-pk="{{ $comment->id }}" data-type="textarea" data-url="{{ secure_url('updateComment') }}"
           data-toggle="manual" data-title="Enter comment" data-placement="top" data-inputclass="form-control">
        {{ $comment->comment }}
      </div>
      
      <div class="comment-meta">
        @if(\Illuminate\Support\Facades\Auth::id() == $comment->user_id)
          <span>
            <a href="javascript:void(0)" onclick="editComment(event,'{{ $comment->id }}')">Edit</a>
          </span>
        @endif
        @if(\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role>=1)
          <span><a class="btn delete_button" href="javascript:void(0)" onclick="deleteComment({{ $comment->id }})">Delete</a></span>
        @endif
        @if(\Illuminate\Support\Facades\Auth::check())
          <span><a class="" role="button" data-toggle="collapse" href="#replyComment_{{ $comment->id }}" aria-expanded="false" aria-controls="collapseExample">reply</a></span>
        @endif
        <div class="collapse" id="replyComment_{{ $comment->id }}">
          <form class="comment-reply-form" method="post" action="{{ secure_url('replyComment') }}" novalidate>
            {{ csrf_field() }}
            <input type="hidden" name="event_id" value="{{ $event_id }}">
            <input type="hidden" name="parent" value="{{ $comment->id }}">
            <div class="form-group">
              <label for="comment">Your Comment</label>
              <textarea name="comment" class="form-control" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-default">Send</button>
          </form>
        </div>
      </div>
      <!-- comment-meta -->
      <div id="replies_{{ $comment->id }}">
        @foreach($comments as $reply)
          @if($reply->parent == $comment->id)
            <div class="media" id="comment_{{ $reply->id }}">
              <!-- answer to the first comment -->
              <div class="media-heading">
                <button class="btn btn-default btn-collapse btn-xs" type="button" data-toggle="collapse" data-target="#collapse_{{ $reply->id }}"
                        aria-expanded="false"
                        aria-controls="collapseExample"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button>
                <span class="label label-gold-rss">
                                  {{ $reply->name }}
                                </span>
                @if(\Illuminate\Support\Facades\Auth::check())
                  @if($reply->role==1)
                    &nbsp;
                    <small>(Moderator)</small>
                  @elseif($reply->role==2)
                    &nbsp;
                    <small>(Admin)</small>
                  @endif
                @endif
                &nbsp;{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $reply->created_at)->diffForHumans() }}
              </div>
              <div class="panel-collapse collapse in" id="collapse_{{ $reply->id }}">
                <!-- media-left -->
                <div class="media-body">
                  <div id="commentContent_{{ $reply->id }}" class="editable" data-pk="{{ $reply->id }}" data-type="textarea" data-url="{{ secure_url('updateComment') }}"
                       data-toggle="manual" data-title="Enter comment" data-placement="top" data-inputclass="form-control">
                    {{ $reply->comment }}
                  </div>
                  <div class="comment-meta">
                    @if(\Illuminate\Support\Facades\Auth::id() == $reply->user_id)
                      <span><a href="javascript:void(0)" onclick="editComment(event,'{{ $reply->id }}')">Edit</a></span>
                    @endif
                    @if(\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role>=1)
                      <span><a class="btn btn-delete" href="javascript:void(0)" onclick="deleteComment({{ $comment->id }})">Delete</a></span>
                    @endif
                    @if(\Illuminate\Support\Facades\Auth::check())
                      <span>
                        <a class="" role="button" data-toggle="collapse" href="#replyComment_{{ $comment->id }}" aria-expanded="false"
                           aria-controls="collapseExample">reply</a>
                      </span>
                    @endif
                  </div>
                  <!-- comment-meta -->
                </div>
              </div>
              <!-- comments -->
            </div>
          @endif
        @endforeach
      </div>
      <!-- answer to the first comment -->
    </div>
  </div>
  <!-- comments -->
</div>