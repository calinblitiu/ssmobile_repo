<?php

namespace App\Http\Controllers;

use App\Comment;
use App\User;
use App\Cvotes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

use App\Event;
use App\Notification;

class CommentController extends BaseController
{
  public function __construct()
  {
    parent::__construct();
  }

  public function storeComment(Request $request)
  {
    $request->comment = stripslashes($request->comment);

    try {
      $comment = new Comment;
      $comment->event_id = $request->event_id;
      $comment->comment = $request->comment;
      $comment->parent = $request->parent;
      $comment->user_id = Auth::id();
      $comment->save();
      if ($comment) {
        $userArr = Array();
        preg_match_all('/(@\w+)/', $comment->comment, $userArr);

        $me = User::find(Auth::id());
        $event = Event::find($comment->event_id);
        $subject = $me->name."&nbsp;mentioned you in a comment";
        $permalink = secure_url('streams/'.$event->event_id.'/'.$event->homeTeam->team_slug.'_vs_'.$event->awayTeam->team_slug);
        $body = " Click here to view.";
        // $body .= "\n" . $commentId;


        foreach($userArr[1] as $username) {
          $targetUsers = User::where('name', substr($username, 1))->get();

          foreach ($targetUsers as $t) {
            if($t->id != $me->id){
              $message = new Notification;
              $message->actor_id  = Auth::id();
              $message->target_id   = $t->id;
              $message->message   = $body;
              $message->action    = 0;
              $message->type    = 1;
              $message->title     = $subject;
              $message->link = $permalink;
              $message->save();
            }
          }
        }

        $comments = \App\Comment::all();
        $userCommentCount = Array();
        foreach($comments as $comment) {
          $userCommentCount[$comment->id] = Comment::where(['user_id' => $comment->user_id ])->count();
        }

        Cache::flush();
        return view('eventCommentTemplate', ['comment' => Comment::find($comment->id), 'userCommentCount' => $userCommentCount]);
      } else {
        return response()->json(['status', 0]);
      }
    } catch (\Exception $exception) {
        return response()->json($exception->getMessage());
      }
  }

  public function storeStreamComment(Request $request)
  {
    try {
      $comment = new Comment;
      $comment->event_id = $request->event_id;
      $comment->comment = $request->comment;
      $comment->parent = $request->parent;
      $comment->user_id = Auth::id();
      $comment->save();
      if ($comment) {
        $userArr = Array();
        preg_match_all('/(@\w+)/', $comment->comment, $userArr);


        $me = User::find(Auth::id());
        $event = Event::find($comment->event_id);
        $subject = $me->name."&nbsp;mentioned you in a comment";
        $permalink = secure_url('streams/'.$event->event_id.'/'.$event->homeTeam->team_slug.'_vs_'.$event->awayTeam->team_slug);
        $body = " Click here to view.";
        $body .= "\n" . $comment->comment;
        // $body .= "\n" . $commentId;


        foreach($userArr[1] as $username) {
          $targetUsers = User::where('name', substr($username, 1))->get();

          foreach ($targetUsers as $t) {
            if($t->id != $me->id){
              $message = new Notification;
              $message->actor_id  = Auth::id();
              $message->target_id   = $t->id;
              $message->message   = $body;
              $message->action    = 0;
              $message->type    = 1;
              $message->title     = $subject;
              $message->link = $permalink;
              $message->save();
            }
          }
        }

        $comments = \App\Comment::all();
        $userCommentCount = Array();
        foreach($comments as $comment) {
          $userCommentCount[$comment->id] = Comment::where(['user_id' => $comment->user_id ])->count();
        }

        Cache::flush();
        return view('streamComments', ['comment' => Comment::find($comment->id), 'userCommentCount' => $userCommentCount]);
      } else {
        return response()->json(['status', 0]);
      }
    } catch (\Exception $exception) {
        return response()->json($exception->getMessage());
      }
  }



  public function deleteComment(Request $request)
  {
    $comment_id = $request->id;
    Comment::where('id', $request->id)->orWhere('parent', $request->id)->delete();
    Cvotes::where('comment_id', $comment_id)->delete();
    Cache::flush();
  }
  public function replyComment(Request $request){
    $commentReply = new Comment;
    $commentReply->event_id = $request->event_id;
    $commentReply->comment = stripslashes($request->comment);
    $commentReply->parent = $request->parent;
    $commentReply->quota = $request->quota;
    if ($request->stream_id) {
      $commentReply->stream_id = $request->stream_id;
    }
    if($request->acestream == 1)
      $commentReply->comment = "<acestream>" . $commentReply->comment;
    $commentReply->user_id = Auth::id();
    $commentReply->save();
    $comments = \App\Comment::all();
    $userCommentCount = Array();
    foreach($comments as $comment) {
      $userCommentCount[$comment->id] = Comment::where(['user_id' => $comment->user_id ])->count();
    }

    if ($commentReply) {
      Cache::flush();
      return view('eventCommentReplyTemplate', ['reply' => Comment::find($commentReply->id), 'userCommentCount' => $userCommentCount]);
    } else {
      return response()->json(['status', true]);
    }
  }

  public function updateComment(Request $request)
  {
    $comment = Comment::find($request->comment_id);
    $comment->comment = stripslashes($request->comment);
    $comment->save();
    Cache::flush();
    return $comment->comment;
    // echo '<script>console.log("'.Comment::find($request->comment_id).'")</script>';
  }

  public function voteComment(Request $request){
    $commentId = $request->comment_id;
    if (Cvotes::where(['comment_id' => $commentId, 'user_id' => Auth::id()])->count() > 0 ||  Cvotes::where(['comment_id' => $commentId, 'ip' => $this->get_client_ip()])->count() > 0) {
      return response()->json(['msg' => 'you already voted!']);
    }
    elseif (Comment::where(['id' => $commentId])->first()->user_id == Auth::id()) {
      return response()->json(['msg' => 'You can\'t vote on your own comment!']);
    }else{
      $vote = new Cvotes;
      $vote->user_id = Auth::id();
      $vote->ip = $this->get_client_ip();
      $vote->comment_id = $commentId;
      $vote->save();
      Cache::flush();
    }

  }

  public function voteCommentDown(Request $request)
  {
    $commentId = $request->comment_id;
    if (Cvotes::where(['comment_id' => $commentId, 'user_id' => Auth::id()])->count() == 0) {
      return response()->json(['msg' => 'you didn\'t vote yet!']);
    } elseif (Comment::where(['id' => $commentId])->first()->user_id == Auth::id()) {
      return response()->json(['msg' => 'You can\'t vote on your own comment!']);
    } else {
      Cvotes::where(['comment_id' => $commentId, 'user_id' => Auth::id()])->delete();
      Cache::flush();
    }
  }

  public function getPostCount(Request $request)
  {
    $userCommentCount = Comment::where(['user_id' => $request->user_id ])->count();
    return response()->json(['status' => true, 'count' => $userCommentCount]);
  }

  public function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
  }
}
