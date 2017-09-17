<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Stream extends Model
{
  public $timestamps = false;
  public $primaryKey = 'stream_id';
  
  public function comments()
  {
    return $this->hasMany('App\Comment', 'stream_id', 'stream_id');
  }
  
  public static function getStreams($eventId, $userType)
  {
    $streams = DB::table('streams AS s')
      ->leftJoin('users AS u', 'u.id', 's.user_id')
      ->leftJoin('languages AS l', 'l.language_name', 's.language')
      ->leftJoin('evaluations AS ev', function ($join) {
        $join->on('ev.stream_id', '=', 's.stream_id');
        $join->where('ev.eval_type', '=', 1);
      })
      ->where(['s.event_id' => $eventId, 'u.verified_user' => $userType, 'u.ban' => 0, 's.approved' => 1])
      ->groupBy('s.stream_id')
      ->orderBy('vote', 'desc')
      ->select('s.*', 'u.name AS username', 'u.verified_user','u.approved', 'l.language_flag', 'l.language_name', DB::raw('COUNT(DISTINCT ev.id) AS vote'))->get();

    return Stream::removeBanStreams( $streams );
  }
  
  public static function getAllEventStreams($eventId, $userId = 0)
  {
    $streams = DB::table('streams AS s')
      ->leftJoin('users AS u', 'u.id', 's.user_id')
      ->leftJoin('comments AS c', function ($join) {
        $join->on('c.stream_id', '=', 's.stream_id')->Where('c.parent', 0);
      })
      ->leftJoin('languages AS l', 'l.language_name', 's.language')
      ->leftJoin('evaluations AS ev', function ($join) {
        $join->on('ev.stream_id', '=', 's.stream_id');
        $join->where('ev.eval_type', '<>', 0);
      })
      ->leftJoin('evaluations AS eval', function ($join) use ($userId) {
        $join->on('eval.stream_id', '=', 's.stream_id');
        $join->where('eval.eval_type', '=', 1);
        $join->where('eval.user_id', '=', $userId);
      })
      ->leftJoin('evaluations AS eval_down', function ($join) use ($userId) {
        $join->on('eval_down.stream_id', '=', 's.stream_id');
        $join->where('eval_down.eval_type', '=', -1);
        $join->where('eval_down.user_id', '=', $userId);
      })
      ->leftJoin('evaluations AS report', function ($join) use ($userId) {
        $join->on('report.stream_id', '=', 's.stream_id');
        $join->where('report.eval_type', '=', 0);
        $join->where('report.user_id', '=', $userId);
      })
      ->where(['s.event_id' => $eventId, 'u.ban' => 0, 's.approved' => 1])
      ->groupBy('s.stream_id')
      ->orderBy('u.sponsor', 'desc')
      ->orderBy('u.verified_user', 'desc')
      ->orderBy('u.approved', 'desc')
      ->orderBy('vote', 'desc')
      ->select('s.*', 'u.name AS username','u.sponsor', DB::raw('COUNT(DISTINCT c.id) AS comments'), 'u.verified_user','u.approved', 'l.language_flag', 'l.language_name', DB::raw('SUM(ev.eval_type) AS vote'), 'eval.user_id AS is_voted', 'eval_down.user_id AS is_downvoted','report.user_id AS is_reported');

    $streams = $streams->get();
    // $streams = $streams->toSql();
    // dd($eventId . '|' . $userId);
    // dd($streams);

    return Stream::removeBanStreams( $streams );
  }

  public static function getStreamById($stream, $userId = 0){
    
    $streams = DB::table('streams AS s')
      ->leftJoin('users AS u', 'u.id', 's.user_id')
      ->leftJoin('comments AS c', 'c.stream_id', 's.stream_id')
      ->leftJoin('languages AS l', 'l.language_name', 's.language')
      ->leftJoin('evaluations AS ev', function ($join) {
        $join->on('ev.stream_id', '=', 's.stream_id');
        $join->where('ev.eval_type', '=', 1);       
      })->leftJoin('evaluations AS eval', function ($join) use ($userId) {
        $join->on('eval.stream_id', '=', 's.stream_id');
        $join->where('eval.eval_type', '=', 1);
        if ($userId) {
          $join->where('eval.user_id', '=', $userId);
        }
      })
      ->leftJoin('evaluations AS report', function ($join) use ($userId) {
        $join->on('report.stream_id', '=', 's.stream_id');
        $join->where('report.eval_type', '=', 0);
        $join->where('report.user_id', '=', $userId);
      })
      ->where('s.stream_id', '=', $stream)
      ->groupBy('s.stream_id')
      ->orderBy('u.verified_user', 'desc')
      ->orderBy('vote', 'desc')
      ->select('s.*', 'u.name AS username', 'u.verified_user','u.approved', DB::raw('COUNT(DISTINCT c.id) AS comments'), 'l.language_flag', 'l.language_name', DB::raw('COUNT(DISTINCT ev.id) AS vote'),'eval.user_id AS is_voted','report.user_id AS is_reported')->first();
    return $streams;
  }
  
  private static function removeBanStreams($streams)
  {
    $res = $streams;
    $domains = Cache::remember('bannedDomains', 10, function () {
      return DB::table('domains')->get();
    });
    
    foreach ($streams as $key => $stream) {
      $url = $stream->url;
      $banned = 0;
      foreach ($domains as $domain) {
        if ((strpos($url, $domain->domain) !== false) && $domain->ban == 1) {
          $banned = 1;
        }
      }
      
      if ($banned == 1)
        unset($res[$key]);
    }
    // exit;
    return $res;
  }
}
