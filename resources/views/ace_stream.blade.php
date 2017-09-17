<tr>
  <td colspan="20" width="100%" class="multiple_streams">
    <div class="streamUser">
        <div class="user-image">
          <span class="acestreamname">Share Ace Streams</span>
        </div>
		<small class="streamUser__small">Share acestreams below, no registration required!</small>
        <div class="streams-actions">
          <a href="#" onclick="event.preventDefault()" class="btn btn-sm btn-rss leaveReply" data-toggle="collapse" data-target="#comments_aceStream" > Add Stream {{ count($aceComments) != 0 ? "&nbsp;(".count($aceComments).")" : '' }}</a>
        </div>
    </div>
    <div class="comments collapse {{ !$showStreams && count($aceComments) > 0 ? '' : 'in' }}" id="comments_aceStream" style="margin-top: -10px; padding-bottom: 0;">
      <div class="event-comments" style="padding-bottom: 0;">
        <div class="stream-comments-div" id="ace-comments-div" style="padding-bottom: 0;">

          @if(count($aceComments) > 0)
            @if( $showStreams )
              <div class="alert alert-info" role="alert">There are currently {{ count($aceComments) }} submitted acestreams available for this match. These will become visible 1 hour before kick-off time </div>
            @else
              {{-- nested comments --}}
              @if(count($aceComments))
                @foreach($aceComments as $ac)
                  @include('userAceStreamComments', ['comment'=>$ac, 'user_comment_count'=> count($aceComments), 'event_id'=> $event->event_id])
                @endforeach
              @endif
              <br>
            @endif
          @else
            <div class="alert alert-info" role="alert">There are no acestreams for this match right now!</div>
          @endif

          {{-- Anyone can add acestream --}}
          <form onsubmit="event.preventDefault();addAceStreamComment($(this));" class="streamCommentAdd" method="post" >
            <input type="hidden" name="event_id" value="{{ $event->event_id }}">
            <div class="form-group" style="margin-top: 5px;">
              {{-- <textarea name="comment" id="ace-textarea" class="form-control" placeholder="Your ace stream link"></textarea> --}}
              <input type="text" name="comment" id="ace-textarea" class="form-control" placeholder="Your ace stream link" style="height: 50px; width: 100%;">
            </div>
            <button type="submit" class="btn btn-default"> Add Stream</button>
          </form>           
        </div>
        {{-- end of nested comments --}}
      </div>
    </div>
  </td>
</tr>