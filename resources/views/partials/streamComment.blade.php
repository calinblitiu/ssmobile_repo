<div class="media{{ (!$comment->parent)?' parent_comment':'' }}{{ (isset($stream) && !isset($reply))?' stream_comment':'' }}" data-votes="{{ $comment->votes }}" id="comment_{{ $comment->id }}" data-comment-id = "{{ $comment->id }}">

    <div class="avatar-image">
        <a href="/publicProfile/<?php echo $comment->user_id ?>">
            @if (file_exists('images/avatar' . '/' . $comment->user_id . '.jpg'))
                <img src="{{ secure_url('images/avatar') . '/' . $comment->user_id . '.jpg?' . microtime(true) }}">
            @else
                <img src="{{ secure_url('images/noimage/no-image.png') }}">
            @endif
        </a>

        <!--div class="user-comment-star">
            <div class="comment-count" >
                {{ $user_comment_count[$comment->id] }} <span>post</span>
            </div>
            <div class="comment-star">
            </div>
        </div-->
    </div>
    <div class="comment-content">
        <div class="media-heading">
            <div class="left">
                <span class="label label-gold-rss user_name">{{ $comment->name }}</span>
                @if(\Illuminate\Support\Facades\Auth::check())
                    @if($comment->role==1)
                        <small>(Moderator)</small>
                    @elseif($comment->role==2)
                        <small>(Admin)</small>
                    @endif
                @endif
                <span data-created="{{ $comment->created_at }}"> {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $comment->created_at) }}</span>
            </div>
            <div class="right">
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
                    <span><a class="reply_button" role="button" data-toggle="collapse" href="#replyComment_{{ $comment->id }}" aria-expanded="false" aria-controls="collapseExample"><i class="fa fa-reply" style="color: #a4a4a4;" aria-hidden="true"></i></a></span>
                @else
                    <span><a class="reply_button" onclick="event.preventDefault();sweetAlert('Oops...', 'Only registered user have the ability to reply on comments!', 'error');" href=""><i class="fa fa-reply" style="color: #a4a4a4;" aria-hidden="true"></i></a></span>
                @endif
            </div>
            
            <div class="clear-both"></div>
        </div>

        <div class="collapse" id="replyComment_{{ $comment->id }}">
            <form class="comment-reply-form" method="post" action="{{ secure_url('replyComment') }}" novalidate>
                {{ csrf_field() }}
                <input type="hidden" name="event_id" value="{{ $event_id }}">
                <input type="hidden" name="parent" value="{{ $comment->id }}">
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
    </div>

    @if (isset($comment->replies) && count($comment->replies) > 0)
    <div class="replies" id="replies_{{ $comment->id }}" data-comment-id = "{{ $comment->id }}">
        @foreach($comment->replies as $reply)
        @include('partials.comment', ['comment'=>$reply, 'reply' => true])
        @endforeach
    </div>
    @endif
</div>