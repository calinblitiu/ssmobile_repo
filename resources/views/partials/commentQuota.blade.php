<div style="padding: 4px 8px">
  <div style="font-style:italic;padding-left: 10px;background: #fbfbfb;border-width: 1px 1px 1px 2px;border-style: solid;border-color: #dacfcf #e8e8e8 #dbdbdb #666666;">
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


    <p style="color:#000;font-size:14px;font-weight:bold;">on <span class='comment-post-time' data-created="{{ $created_at_stamp }}"> {{ $display_time }}</span>, {{\App\User::find($comment->user_id)->name}} said</p>
    <?php
      if($comment->parent){
        $comment_parent = \App\Comment::find($comment->parent);
      }
    ?>
    @if($comment->parent)
          @include('partials.commentQuota', ['comment'=>$comment_parent])
    @endif
    {!! nl2br($comment->comment) !!}
  </div>
</div>
