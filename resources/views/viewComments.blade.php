<style>
  .anchor-time {
    color: #ADB2BB;
    font-size: 1.2rem;
  }
  
  .post-footer-comment-wrapper {
    background-color: #F6F7F8;
    margin-bottom: 2px;
  }
  
  .media-body {
    padding: 15px;
  }
  
  h4.media-heading {
    font-weight: bold;
    color: #47649F;
  }
  
  .media-body {
    position: relative;
  }
  
  a.btn-delete {
    position: absolute;
    color: #FFF;
    padding: 5px;
    float: right;
    top: 0;
    right: 0px;
    margin-right: 15px;
    margin-top: 10px;
    background-color: red;
    text-decoration: none;
  }
</style>
<div id="comments">
  @foreach($comments as $comment)
    <div id="comment_{{ $comment->id }}" class="post-footer-comment-wrapper">
      <div class="comment">
        <div class="media">
          <div class="media-body">
            <h4 class="media-heading">{{ $comment->users->name }}
              @if(\Illuminate\Support\Facades\Auth::check())
                @if($comment->users->role==1)
                  (Moderator)
                @elseif($comment->users->role==2)
                  (Admin)
                @endif
              @endif
              : </h4><span>{{ $comment->comment }}</span>
            @if(\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role>=1)
              <a class="btn btn-delete" href="javascript:void(0);" onclick="deleteComment({{ $comment['id'] }})">Delete</a>
            @endif
            <p class="anchor-time">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $comment->created_at)->diffForHumans() }}</p>
          </div>
        </div>
      </div>
    </div>
  @endforeach
  <input type="hidden" name="streamId" id="streamId" value="{{ $streamId }}">
</div>