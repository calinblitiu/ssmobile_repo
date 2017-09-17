<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Comment extends Model
{
  public $timestamps = false;
  
  public function event()
  {
    return $this->belongsTo('App\Event', 'event_id', 'event_id');
  }
  
  public function users()
  {
    return $this->belongsTo('App\User', 'user_id', 'id');
  }
  
  public static function getEventComments($eventId, $only4match = false)
  {
    $result = DB::table('comments AS c')    
    ->where('c.comment', 'not like', '%acestream%')
    ->leftJoin('users AS u', 'u.id', 'c.user_id')
    ->leftJoin('comment_votes AS cv', 'cv.comment_id', 'c.id')
    ->leftJoin('comment_votes AS cvote', function ($join) {
      $join->on('cvote.comment_id', '=', 'c.id');
      $join->where('cvote.user_id', '=', Auth::id());
    })
    ->where(['c.event_id' => $eventId, 'u.ban'=>0]);

    if ($only4match) {
      $result = $result->where(['c.stream_id' => 0]);
    }

    $result = $result->orderBy('votes', 'desc')
    ->orderBy('c.created_at', 'desc')
    ->groupBy('c.id')
    ->select('c.*', 'u.name', DB::raw('COUNT(DISTINCT cv.id) as votes'),'u.role', 'cvote.user_id AS is_voted')->get();

    return $result;
  }

  public static function getEventAceStreamComments($eventId)
  {
    $result = DB::table('comments AS c')    
    ->where('c.comment', 'like', '%acestream%')
    ->leftJoin('users AS u', 'u.id', 'c.user_id')
    ->leftJoin('comment_votes AS cv', 'cv.comment_id', 'c.id')
    ->leftJoin('comment_votes AS cvote', function ($join) {
      $join->on('cvote.comment_id', '=', 'c.id');
      $join->where('cvote.user_id', '=', Auth::id());
    })
    ->where(['c.event_id' => $eventId, 'u.ban'=>0, 'c.stream_id' => 0]);
    
    $result = $result->orderBy('votes', 'desc')
    ->orderBy('c.created_at', 'desc')
    ->groupBy('c.id')
    ->select('c.*', 'u.name', DB::raw('COUNT(DISTINCT cv.id) as votes'),'u.role', 'cvote.user_id AS is_voted')->get();

    return $result;
  }

  public static function getEventCommentsInOrder($eventId, $orderType = 'VOTE_DESC')
  {

    switch($orderType) {
      case 'VOTE_DESC':
        $result = DB::table('comments AS c')
          ->leftJoin('users AS u', 'u.id', 'c.user_id')
          ->leftJoin('comment_votes AS cv', 'cv.comment_id', 'c.id')
          ->leftJoin('comment_votes AS cvote', function ($join) {
            $join->on('cvote.comment_id', '=', 'c.id');
            $join->where('cvote.user_id', '=', Auth::id());
          })
          ->where(['c.event_id' => $eventId, 'stream_id' => 0, 'u.ban'=>0])
          ->orderBy('votes', 'desc')
          ->orderBy('c.created_at', 'desc')
          ->groupBy('c.id')
          ->select('c.*', 'u.name', DB::raw('COUNT(DISTINCT cv.id) as votes'),'u.role', 'cvote.user_id AS is_voted')->get();
        break;
      case 'DATE_DESC':
        $result = DB::table('comments AS c')
          ->leftJoin('users AS u', 'u.id', 'c.user_id')
          ->leftJoin('comment_votes AS cv', 'cv.comment_id', 'c.id')
          ->leftJoin('comment_votes AS cvote', function ($join) {
            $join->on('cvote.comment_id', '=', 'c.id');
            $join->where('cvote.user_id', '=', Auth::id());
          })
          ->where(['c.event_id' => $eventId, 'stream_id' => 0, 'u.ban'=>0])
          ->orderBy('c.created_at', 'desc')
          ->groupBy('c.id')
          ->select('c.*', 'u.name', DB::raw('COUNT(DISTINCT cv.id) as votes'),'u.role', 'cvote.user_id AS is_voted')->get();
        break;
      case 'DATE_ASC':
        $result = DB::table('comments AS c')
          ->leftJoin('users AS u', 'u.id', 'c.user_id')
          ->leftJoin('comment_votes AS cv', 'cv.comment_id', 'c.id')
          ->leftJoin('comment_votes AS cvote', function ($join) {
            $join->on('cvote.comment_id', '=', 'c.id');
            $join->where('cvote.user_id', '=', Auth::id());
          })
          ->where(['c.event_id' => $eventId, 'stream_id' => 0, 'u.ban'=>0])
          ->orderBy('c.created_at', 'asc')
          ->groupBy('c.id')
          ->select('c.*', 'u.name', DB::raw('COUNT(DISTINCT cv.id) as votes'),'u.role', 'cvote.user_id AS is_voted')->get();
        break;
      
    }
    return $result;
  }

  public static function getStreamComments($stream_id){
    return DB::table('comments AS c')
      ->leftJoin('users AS u', 'u.id', 'c.user_id')
      ->leftJoin('comment_votes AS cv', 'cv.comment_id', 'c.id')
      ->leftJoin('comment_votes AS cvote', function ($join) {
        $join->on('cvote.comment_id', '=', 'c.id');
        $join->where('cvote.user_id', '=', Auth::id());
      })
      ->where(['c.stream_id'=> $stream_id, 'u.ban'=>0])
      ->orderBy('votes', 'desc')
      ->orderBy('c.created_at', 'desc')
      ->groupBy('c.id')
      ->select('c.*', 'u.name', DB::raw('COUNT(DISTINCT cv.id) as votes'),'u.role', 'cvote.user_id AS is_voted')
      ->get();
  }
  public static function getStreamComment($comment_id){
    return DB::table('comments AS c')
      ->leftJoin('users AS u', 'u.id', 'c.user_id')
      ->leftJoin('comment_votes AS cv', 'cv.comment_id', 'c.id')
      ->leftJoin('comment_votes AS cvote', function ($join) {
        $join->on('cvote.comment_id', '=', 'c.id');
        $join->where('cvote.user_id', '=', Auth::id());
      })
      ->where('c.id', $comment_id)
      ->where('u.ban', 0)
      ->orderBy('votes', 'desc')
      ->orderBy('c.created_at', 'desc')
      ->groupBy('c.id')
      ->select('c.*', 'u.name','u.role', DB::raw('COUNT(DISTINCT cv.id) as votes'), 'cvote.user_id AS is_voted')
      ->first();
  }
  
}
