<div class="media{{ (!$comment->parent)?' parent_comment':'' }}{{ (isset($stream) && !isset($reply))?' stream_comment':'' }}" data-votes="{{ $comment->votes }}" id="comment_{{ $comment->id }}" data-comment-id = "{{ $comment->id }}">
    <div class="avatar-image">
        <a href="/publicProfile/{{ $comment->user_id }}">
            @if (file_exists('images/avatar' . '/' . $comment->user_id . '.jpg'))
                <img src="{{ secure_url('images/avatar') . '/' . $comment->user_id . '.jpg?' . microtime(true) }}">
            @else
                <img src="{{ secure_url('images/noimage/no-image.png') }}">
            @endif
        </a>
        <?php
        $comment_count = \App\Comment::where(['user_id' => $comment->user_id ])->count();
        ?>
        <div style="text-align:center;">
          <p style="font-size:12px;font-weight:700">{{ $comment_count }} {{ str_plural('Posts', $comment_count) }}</p>
        </div>
    </div>
    <div class="comment-content">
        <div class="media-heading" style="margin-bottom: @if(isset($aceComment) && $aceComment == 1)  -20px @else -5px !important; @endif">
            <div class="left">
                <span class="label label-gold-rss user_name">{{ $comment->name }}</span>
                @if(\Illuminate\Support\Facades\Auth::check())
    	            @if($comment->role==1)
    	            	<small>(Moderator)</small>
    	            @elseif($comment->role==2)
    	            	<small>(Admin)</small>
    	            @endif
                @endif
                <?php
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
                ?>
                <span class='comment-post-time' data-created="{{ $created_at_stamp }}"> {{ $display_time }}</span>
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
                    <a class="edit_button" onclick="editComment(event,'{{ $comment->id }}',this)" role="button" data-toggle="collapse" href="#editComment_{{ $comment->id }}" aria-expanded="false" aria-controls="collapseExample">
                      <i class="glyphicon glyphicon-pencil" style="color: #a4a4a4;"></i>
                    </a>
                  </span>
                	{{-- <span>
                		<a href="javascript:void(0)" onclick="editComment(event,'{{ $comment->id }}')">
                			<i class="glyphicon glyphicon-pencil" style="color: #a4a4a4;"></i>
                		</a>
                	</span> --}}
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
        <?php
          if($comment->parent){
            $comment_parent = \App\Comment::find($comment->parent);
          }
        ?>
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
                {{-- <div class="stream_comment_row stream_{{ $stream->stream_id }} @if(strtolower($stream->compatibility)=='no') hidden-xs @endif" data-href="{{ $stream->url }}"
                    data-stream-id="{{ $stream->stream_id }}" data-type="{{ strtoupper($stream->stream_type) }}" data-quality="{{ strtoupper($stream->quality) }}"
                    data-language="{{ strtoupper($stream->language_name) }}" data-mobile="{{ $stream->compatibility }}">
                    <td width="7%" class="rating">
                        <span class="rate">
                        {{ $stream->vote }}
                        </span>
                        @if(\Illuminate\Support\Facades\Auth::guest())
                        <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Only registered user have the ability to vote streams!', 'error');">
                        <i class="fa fa-thumbs-up fa-2x" style="color: green;margin: 0;vertical-align: top;" aria-hidden="true"></i>
                        </a>
                        @else
                        @if(is_null($stream->is_voted))
                        <span class="vot">
                        <a href="javascript:void(0);" onclick="voteUp(this,'{{ $stream->stream_id }}')">
                        <i class="fa fa-thumbs-up fa-2x" style="color: green;margin: 0;vertical-align: top;" aria-hidden="true"></i>
                        </a>
                        </span>
                        @else
                        <span class="done"><i class="fa fa-check-circle-o fa-2x" aria-hidden="true"></i></span>
                        @endif
                        <span class="done" style="display: none">
                        <i class="fa fa-check-circle-o fa-2x" aria-hidden="true"></i>
                        </span>
                        @endif
                    </td>
                    <td width="5%" class="">
                        @if(strtolower($stream->stream_type)=='acestream' || strtolower($stream->stream_type)=='vlc' || strtolower($stream->stream_type)=='sopcast')
                        <button data-clipboard-text="{{ $stream->url }}" class="btn btn-rss btn-copy">
                        <i class="fa fa-clipboard" aria-hidden="true"></i>
                        </button>
                        @else
                        <a href="{{ $stream->url }}" target="_blank" class="btn btn-rss">
                        <i class="fa fa-play-circle-o" aria-hidden="true"></i>
                        </a>
                        @endif
                    </td>
                    <td width="1%">
                        <a class="btn-copy" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $stream->other_info }}">
                        @if( $stream->other_info )<i class="fa fa-info-circle"></i>
                        @endif
                        </a>
                    </td>
                    <td class="clickable">
                        <img src="{{ secure_asset('images/languages/'.$flag.'.png') }}" alt="{{ $stream->language_flag }}">
                        <p class="hidden languageValue">{{ $stream->language_name }}</p>
                    </td>
                    <td class="clickable" width="40%">
                        {{ $stream->username }}
                        @if( $stream->approved == 1 )
                        <span verified-hover-position="top" class="tag verified approved"><b>APPROVED STREAMER</b></span>
                        @endif
                        @if($stream->verified_user==1)
                        <span verified-hover-text="Verified Streamers are handpicked and represent the highest quality and/or most stable streams on Soccer Streams"
                            verified-hover-position="top"
                            class="tag verified"><b>VERIFIED STREAMER</b></span>
                        @endif
                    </td>
                    <td class="clickable">
                        @if(strtolower($stream->stream_type)=='vlc')
                        <span class="tag stream-type-tag">VLC</span>
                        @elseif(strtolower($stream->stream_type)=='acestream')
                        <span class="tag stream-type-tag">ACE</span>
                        @elseif(strtolower($stream->stream_type)=='sopcast')
                        <span class="tag stream-type-tag">SOP</span>
                        @elseif(strtolower($stream->stream_type)=='http')
                        <span class="tag stream-type-tag">HTTP</span>
                        @else
                        <span class="tag stream-type-tag">Other</span>
                        @endif
                        <p class="hidden">{{ $stream->stream_type }}</p>
                    </td>
                    <td class="clickable">
                        @if(strtolower($stream->quality)=='hd' || strtolower($stream->quality)=='sd')
                        <span class="tag stream-type-tag qualityValue">{{ $stream->quality }}</span>
                        @elseif(strtolower($stream->quality)=='520p')
                        <span class="tag quality-tag qualityValue">520</span>
                        @else
                        <span class="tag unknown quality-tag"></span>
                        @endif
                    </td>
                    <td class="clickable hidden-xs">
                        @if(strtolower($stream->compatibility)=='no')
                        <img class="small_icon" src="{{ secure_asset('icons/streaminfo/mobilecompatno.png') }}" alt="In compatible" title="Not a mobile compatible">
                        @else
                        <img class="small_icon" src="{{ secure_asset('icons/streaminfo/mobilecompat.png') }}" alt="compatible" title="Mobile Compatible">
                        @endif
                    </td>
                    <td>
                        <span class="tag ad_number">{{ $stream->ad_number>0?$stream->ad_number.' Ad-overlays':'no Ad-overlays' }}</span>
                    </td>
                    <td>
                        @if($stream->nsfw==1)
                        <span class="tag nsfw-tag">NSFW</span>
                        @endif
                    </td>
                    <td>
                        <a data-slug="{{ $stream->username }}_{{ $stream->stream_id }}" data-clipboard-text="{{ Request::url() }}#{{ $stream->username }}_{{ $stream->stream_id }}" class="btn-copy permalink" data-toggle="tooltip" data-placement="bottom" data-original-title="Copy stream permalink">
                        <i class="fa fa-share-square-o" aria-hidden="true"></i>
                        </a>
                    </td>
                    <td>
                        @if(Auth::guest())
                        <a href="javascript:void(0);" onclick="sweetAlert('Oops...', 'Only registered user have the ability to vote streams!', 'error');">
                        <i class="fa fa-exclamation-triangle" style="color: red" aria-hidden="true"></i>
                        </a>
                        @else
                        @if(is_null($stream->is_reported))
                        <a href="javascript:void(0);" onclick="report(this,'{{ $stream->stream_id }}','{{ $stream->event_id }}')" title="Report stream">
                        <i class="fa fa-exclamation-triangle" style="color: red" aria-hidden="true"></i>
                        </a>
                        @else
                        <span><i class="fa fa-check" aria-hidden="true"></i></span>
                        @endif
                        <span style="display: none"><i class="fa fa-check" aria-hidden="true"></i></span>
                        @endif
                    </td>
                </div>
                <br> --}}
                @endif
                <div id="_token" class="hidden" data-token="{{ csrf_token() }}"></div>
                <div id="commentContent_{{ $comment->id }}" class="comment_content editable" data-pk="{{ $comment->id }}" data-type="textarea" data-url="{{ secure_url('updateComment') }}"
                    data-toggle="manual" data-title="Enter comment" data-placement="top" data-inputclass="form-control">
                    {!! stripslashes(nl2br($comment->comment)) !!}
                </div>
            </div>
        </div>

        <div class="collapse" id="replyComment_{{ $comment->id }}">
            <form class="comment-reply-form" method="post" action="{{ secure_url('replyComment') }}" novalidate>
                {{ csrf_field() }}
                <input type="hidden" name="event_id" value="{{ $event_id }}">
                <input type="hidden" name="parent" id="reply-parent" value="{{ $comment->id }}">
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
            <form class="comment-quota-form" method="post" action="{{ secure_url('replyComment') }}" novalidate>
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
        <div class="collapse" id="editComment_{{ $comment->id }}">
            <form class="comment-edit-form" method="post" action="{{ secure_url('updateComment') }}" novalidate>
                {{ csrf_field() }}
                <input type="hidden" name="comment_id" value="{{ $comment->id }}">
                <input type="hidden" name="quota" value="1">
                @if(isset($stream))
                <input type="hidden" name="stream_id" value="{{ $stream->stream_id }}">
                @endif
                <div class="form-group">
                    <label for="comment">Edit Comment</label>
                    <textarea name="comment" class="form-control" id="texteditor_{{ $comment->id }}" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-default">Save</button>
                <button type="submit" class="btn btn-default">Close</button>
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
