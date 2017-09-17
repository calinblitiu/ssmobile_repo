<div id="comment_{{ $comment['id'] }}" class="post-footer-comment-wrapper">
  <div class="comment">
    <div class="media">
      <div class="media-body">
        <h4 class="media-heading">{{ $comment['username'] }}
          @if($comment['role']==1)
            (Moderator)
          @elseif($comment['role']==2)
            (Admin)
          @endif
          : </h4><span>{{ $comment['comment'] }}</span>
        @if(\Illuminate\Support\Facades\Auth::user()->role>=1)
          <a class="btn btn-delete" href="javascript:void(0);" onclick="deleteComment({{ $comment['id'] }})">Delete</a>
        @endif
        <p class="anchor-time">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $comment['created_at'])->diffForHumans() }}</p>
      </div>
    </div>
  </div>
</div>