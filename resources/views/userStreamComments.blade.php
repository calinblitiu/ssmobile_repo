<div class="media{{ (!$comment->parent)?' parent_comment':'' }}{{ (isset($stream) && !isset($reply))?' stream_comment':'' }}" data-votes="{{ $comment->votes }}" id="comment_{{ $comment->id }}" data-comment-id = "{{ $comment->id }}">

    <div class="avatar-image">
        <a href="/publicProfile/{{$comment->user_id}}">
            @if (file_exists('images/avatar' . '/' . $comment->user_id . '.jpg'))
                <img src="{{ secure_url('images/avatar') . '/' . $comment->user_id . '.jpg?' . microtime(true) }}">
            @else
                <img src="{{ secure_url('images/noimage/no-image.png') }}">
            @endif
        </a> 
        @php
        $comment_count = \App\Comment::where(['user_id' => $comment->user_id ])->count();
        @endphp
        <div style="text-align:center;">
          <p style="font-size:12px;font-weight:700">{{ $comment_count }} {{ str_plural('Posts', $comment_count) }}</p>
        </div>
    </div>
    <div class="comment-content">
        <div class="media-heading">
            <div class="left" style="float:left">
                <span class="label label-gold-rss user_name">{{ $comment->name }}</span>
                @if(\Illuminate\Support\Facades\Auth::check())
                  @if($comment->role==1)
                    <small>(Moderator)</small>
                  @elseif($comment->role==2)
                    <small>(Admin)</small>
                  @endif
                @endif
                @php
                  $nowtime_stamp = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', \Carbon\Carbon::now())->gettimestamp();
                  $created_at_stamp = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $comment->created_at)->gettimestamp();
                  $timestamps = $nowtime_stamp - $created_at_stamp;
                  $days = intval($timestamps/60/60/24);
                  $hours = intval($timestamps/60/60) - 24*$days;
                  $minutes = intval($timestamps/60) - 60*$hours - 60*24*$days;
                  $display_time = "";
                  if($minutes != 0 ){
                    if($days != 0){
                      $display_time .= $days." days ";
                    }
                    if($hours != 0){
                      $display_time .= $hours." hours ";
                    }
                    $display_time .= $minutes." minutes ago";
                  }
                  if($minutes == 0){
                    $display_time .= "Just now";
                  }
                @endphp
                <span class='comment-post-time' data-created="{{ $created_at_stamp }}"> {{ $display_time }}</span>
            </div>
            <div class="right" style="float:right">
                @if ($comment->votes  != '0')
                <b>
                    <span class="votes_count" data-count="{{ ($comment->votes) ? $comment->votes : '0' }}">
                      {{ ($comment->votes) ? $comment->votes : '0' }}
                    </span>
                    votes
                </b>
                @endif

                <span class="done" style="display: {{ ($comment->is_voted) ? 'inline':'none' }}" onclick="CommentVoteDown(this,'{{ $comment->id }}')">
                <i class="fa fa-check-circle-o" aria-hidden="true"></i>
                </span>
                <span class="vot" style="display: {{ ($comment->is_voted) ? 'none':'inline' }}" onclick="CommentVoteUp(this,'{{ $comment->id }}')">
                    <i class="fa fa-thumbs-up" style="color: #a4a4a4;" aria-hidden="true"></i>
                </span>
                <span class="vot" style="display: {{ ($comment->is_voted) ? 'none':'inline' }}" onclick="CommentVoteDown(this,'{{ $comment->id }}')">
                    <i class="fa fa-thumbs-down" style="color: #a4a4a4;" aria-hidden="true"></i>
                </span>

                @if(\Illuminate\Support\Facades\Auth::id() == $comment->user_id)
                  <span>
                    <a href="javascript:void(0)" onclick="editComment(event,'{{ $comment->id }}')">
                      <i class="glyphicon glyphicon-pencil" style="color: #a4a4a4;"></i>
                    </a>
                  </span>
                @endif
                @if(\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role>=1)
                  <span>
                    <a class="delete_button" href="javascript:void(0)" onclick="deleteComment({{ $comment->id }})">
                      <i class="glyphicon glyphicon-remove" style="color: #a4a4a4;"></i>
                    </a>
                  </span>
                @endif
                @if(\Illuminate\Support\Facades\Auth::check())
                    <span><a class="quota_button" role="button" data-toggle="collapse" href="#quotaComment_{{ $comment->id }}" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-quote-right" style="color: #a4a4a4;" aria-hidden="true"></i></a></span>
                @else
                    <span><a class="quota_button" href="" onclick="event.preventDefault();sweetAlert('Oops...', 'Only registered user have the ability to reply on comments!', 'error');"><i class="fa fa-quote-right" style="color: #a4a4a4;" aria-hidden="true"></i></a></span>
                @endif
                @if(\Illuminate\Support\Facades\Auth::check())
                  <span><a class="reply_button" role="button" data-toggle="collapse" href="#replyComment_{{ $comment->id }}" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-reply" style="color: #a4a4a4;" aria-hidden="true"></i></a></span>
                @else
                  <span><a class="reply_button" onclick="event.preventDefault();sweetAlert('Oops...', 'Only registered user have the ability to reply on comments!', 'error');" href=""><i class="fa fa-reply" style="color: #a4a4a4;" aria-hidden="true"></i></a></span>
                @endif
            </div>

            <div class="clear-both"></div>
        </div>
        @php
          if($comment->parent) $comment_parent = \App\Comment::find($comment->parent);
        @endphp
        @if(isset($comment_parent) && $comment->quota == 1)
              @include('partials.commentQuota', ['comment'=>$comment_parent])
        @endif
        <div class="panel-collapse collapse in" id="commentId_{{ $comment->id }}">
            <div class="media-body">
                @if(isset($stream) && !isset($reply))
                  @if(empty($stream->language_flag))
                  <?php $flag = 'unknown'; ?>
                  @else
                  <?php $flag = $stream->language_flag; ?>
                  @endif                
                @endif
                <div id="_token" class="hidden" data-token="{{ csrf_token() }}"></div>
                <div id="commentContent_{{ $comment->id }}" class="comment_content editable" data-pk="{{ $comment->id }}" data-type="textarea" data-url="{{ secure_url('updateComment') }}"
                    data-toggle="manual" data-title="Enter comment" data-placement="top" data-inputclass="form-control">
                    {!! nl2br($comment->comment) !!}
                </div>
            </div>
        </div>

        <div class="collapse" id="replyComment_{{ $comment->id }}">
            <form class="comment-reply-form" method="post" action="{{ secure_url('replyComment') }}" novalidate>
                {{ csrf_field() }}
                <input type="hidden" name="event_id" value="{{ $event_id }}">
                <input type="hidden" name="parent" value="{{ $comment->id }}">
                <input type="hidden" name="quota" value="0">
                @if(isset($stream))
                <input type="hidden" name="stream_id" value="{{ $stream->stream_id }}">
                @endif
                <div class="form-group">
                    <label for="comment">Your Reply</label>
                    <textarea name="comment" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-default">Send</button>
            </form>
        </div>

        <div class="collapse" id="quotaComment_{{ $comment->id }}">
            <form class="comment-quota-form" method="post" action="{{ url('replyComment') }}" novalidate>
                {{ csrf_field() }}
                <input type="hidden" name="event_id" value="{{ $event_id }}">
                <input type="hidden" name="parent" value="{{ $comment->id }}">
                <input type="hidden" name="quota" value="1">
                @if(isset($stream))
                <input type="hidden" name="stream_id" value="{{ $stream->stream_id }}">
                @endif
                <div class="form-group">
                    <label for="comment">Your Quota</label>
                    <textarea name="comment" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-default">Send</button>
            </form>
        </div>
    </div>

    @if (isset($comment->replies) && count($comment->replies) > 0)
      <div class="replies" id="replies_{{ $comment->id }}" data-comment-id = "{{ $comment->id }}">
          @foreach($comment->replies as $reply)
          @include('partials.comment', ['comment'=>$reply, 'reply' => true])
          @endforeach
      </div>
    @endif
    <script>
        $(document).ready(function() {
            if (window.location.hash != '') {
                var comment_id = window.location.hash.replace( /^\D+/g, '');
                $('html, body').animate({
                    scrollTop: $('#comment_' + comment_id).offset().top + 'px'
                }, 'slow');
            }
        });
    </script>
</div>
