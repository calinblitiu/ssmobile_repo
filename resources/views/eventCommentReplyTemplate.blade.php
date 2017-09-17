<div class="media"  id="comment_{{ $reply->id }}" data-comment-id = "{{ $reply->id }}">
    <!-- answer to the first comment -->
    <div class="avatar-image">
        <a href="/publicProfile/<?php echo $reply->user_id ?>">
            @if (file_exists('images/avatar' . '/' . Auth::User()->id . '.jpg'))
            <img src="{{ secure_url('images/avatar') . '/' . Auth::User()->id . '.jpg?' . microtime(true) }}">
        @else
            <img src="{{ secure_url('images/noimage/no-image.png') }}">
        @endif
        </a>
        <?php
          $comment_count = \app\Comment::where(['user_id' => $reply->user_id ])->count();
        ?>
        <div style="text-align:center;">
          <p style="font-size:12px;font-weight:700">{{ $comment_count }} Post</p>
        </div>
    </div>

    <div class="comment-content">
        <div class="media-heading">
            <div class="left">

                <span class="label label-gold-rss user_name">{{ \Illuminate\Support\Facades\Auth::user()->name }}</span>
                @if(\Illuminate\Support\Facades\Auth::check())
                    @if($reply->role==1)
                        <small>(Moderator)</small>
                    @elseif($reply->role==2)
                        <small>(Admin)</small>
                    @endif
                @endif
                <?php
                  $nowtime_stamp = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', \Carbon\Carbon::now())->gettimestamp();
                  $created_at_stamp = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $reply->created_at)->gettimestamp();
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
                ?>
                <span class='comment-post-time' data-created="{{ $created_at_stamp }}"> {{ $display_time }} </span>
            </div>
            <div class="right">
                <span class="done" style="display: {{ ($reply->is_voted) ? 'inline':'none' }}" onclick="CommentVoteDown(this,'{{ $reply->id }}')">
                <i class="fa fa-check-circle-o" aria-hidden="true"></i>
                </span>
                <span class="vot" style="display: {{ ($reply->is_voted) ? 'none':'inline' }}" onclick="CommentVoteUp(this,'{{ $reply->id }}')">
                    <i class="fa fa-thumbs-up" style="color: #a4a4a4;" aria-hidden="true"></i>
                </span>
                <span class="vot" style="display: {{ ($reply->is_voted) ? 'none':'inline' }}" onclick="CommentVoteDown(this,'{{ $reply->id }}')">
                    <i class="fa fa-thumbs-down" style="color: #a4a4a4;" aria-hidden="true"></i>
                </span>

                @if(\Illuminate\Support\Facades\Auth::id() == $reply->user_id)
                  <span>
                    <a class="edit_button" onclick="editComment(event,'{{ $reply->id }}',this)" role="button" data-toggle="collapse" href="#editComment_{{ $reply->id }}" aria-expanded="false" aria-controls="collapseExample">
                      <i class="glyphicon glyphicon-pencil" style="color: #a4a4a4;"></i>
                    </a>
                  </span>
                @endif
                @if(\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role>=1)
                    <span>
                        <a class="delete_button" href="javascript:void(0)" onclick="deleteComment({{ $reply->id }})">
                            <i class="glyphicon glyphicon-remove" style="color: #a4a4a4;"></i>
                        </a>
                    </span>
                @endif
                @if(\Illuminate\Support\Facades\Auth::check())
                    <span><a class="quota_button" role="button" data-toggle="collapse" href="#quotaComment_{{ $reply->id }}" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-quote-right" style="color: #a4a4a4;" aria-hidden="true"></i></a></span>
                @else
                    <span><a class="quota_button" href="" onclick="event.preventDefault();sweetAlert('Oops...', 'Only registered user have the ability to reply on comments!', 'error');"><i class="fa fa-quote-right" style="color: #a4a4a4;" aria-hidden="true"></i></a></span>
                @endif
                @if(\Illuminate\Support\Facades\Auth::check())
                    <span><a class="reply_button" role="button" data-toggle="collapse" href="#replyComment_{{ $reply->id }}" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-reply" style="color: #a4a4a4;" aria-hidden="true"></i></a></span>
                @else
                    <span><a class="reply_button" href="" onclick="event.preventDefault();sweetAlert('Oops...', 'Only registered user have the ability to reply on comments!', 'error');"><i class="fa fa-reply" style="color: #a4a4a4;" aria-hidden="true"></i></a></span>
                @endif
            </div>

            <div class="clear-both"></div>
        </div>
        <?php
          if($reply->parent){
            $comment_parent = \App\Comment::find($reply->parent);
          }
        ?>
        @if (isset($comment_parent) && $reply->quota == 1)
          @include('partials.commentQuota', ['comment'=>$comment_parent])
        @endif
        <div class="panel-collapse collapse in" id="commentId_{{ $reply->id }}">
            <!-- media-left -->
            <div class="media-body">
                <div id="commentContent_{{ $reply->id }}" data-validate="validateEdit(value)" class="comment_content editable" data-pk="{{ $reply->id }}" data-type="textarea" data-url="{{ secure_url('updateComment') }}"
                    data-toggle="manual" data-title="Enter comment" data-placement="top" data-inputclass="form-control">
                    {{ $reply->comment }}
                </div>
            </div>
        </div>

        <div class="collapse" id="replyComment_{{ $reply->id }}">
            <form class="comment-reply-form" method="post" action="{{ secure_url('replyComment') }}" novalidate>
                {{ csrf_field() }}
                <input type="hidden" name="event_id" value="{{ $reply->event_id }}">
                <input type="hidden" name="parent" value="{{ $reply->id }}">
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
        <div class="collapse" id="quotaComment_{{ $reply->id }}">
            <form class="comment-quota-form" method="post" action="{{ secure_url('replyComment') }}" novalidate>
                {{ csrf_field() }}
                <input type="hidden" name="event_id" value="{{ $reply->event_id }}">
                <input type="hidden" name="parent" value="{{ $reply->id }}">
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
        <div class="collapse" id="editComment_{{ $reply->id }}">
            <form class="comment-edit-form" method="post" action="{{ secure_url('updateComment') }}" novalidate>
                {{ csrf_field() }}
                <input type="hidden" name="comment_id" value="{{ $reply->id }}">
                <input type="hidden" name="quota" value="1">
                @if(isset($stream))
                <input type="hidden" name="stream_id" value="{{ $stream->stream_id }}">
                @endif
                <div class="form-group">
                    <label for="comment">Edit Comment</label>
                    <textarea name="comment" class="form-control" id="texteditor_{{ $reply->id }}" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-default">Save</button>
                <button type="submit" class="btn btn-default">Close</button>
            </form>
        </div>
    </div>

    <!-- comments -->
</div>
