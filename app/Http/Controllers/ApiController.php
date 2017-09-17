<?php

namespace App\Http\Controllers;

use App\Settings;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
  public function getEvents()
  {
    $interval = Settings::where(['setting_key' => 'events_duration'])->first()->setting_value;
    $competitionLogoPath = secure_url('/') . Settings::where(['setting_key' => 'competition_small_logo_path'])->first()->setting_value;
    $teamLogoPath = secure_url('/') . Settings::where(['setting_key' => 'team_small_logo_path'])->first()->setting_value;
    $today = Carbon::now();
    $duration = Carbon::now()->addHours($interval);
    $events = DB::table('events AS e')
      ->leftJoin('teams AS ht', 'e.home_team_id', '=', 'ht.team_id')
      ->leftJoin('teams AS at', 'e.away_team_id', '=', 'at.team_id')
      ->leftJoin('competitions AS c', 'c.competition_id', '=', 'e.competition_id')
      ->leftJoin('event_details AS ed', 'ed.event_id', '=', 'e.event_id')
      ->where('e.end_date', '>=', $today)
      ->where('e.start_date', '<=', $duration)
      ->where('e.sport_id', 1)
      ->orderBy('e.start_date', 'ASC')
      ->select('e.start_date', 'c.competition_name AS competition_name', DB::raw('CONCAT("' . $competitionLogoPath . '",c.competition_logo) AS competition_logo'),
        'ht.team_name AS home_team', DB::raw('CONCAT("' . $teamLogoPath . '",ht.team_logo) AS home_team_logo'), 'ht.team_slug AS home_team_slug',
        'e.event_status', 'at.team_name AS away_team', DB::raw('CONCAT("' . $teamLogoPath . '",at.team_logo) AS away_team_logo'), 'at.team_slug AS away_team_slug',
        'e.event_id', 'ed.game_minute')
      ->get();
    
    return response()
      ->json([
        'status' => true,
        'events' => $events
      ])->header('Content-Type', 'application/json; charset=utf-8');
  }
  
  public function getStreams($eventId)
  {
    $competitionLogoPath = secure_url('/') . Settings::where(['setting_key' => 'competition_logo_path'])->first()->setting_value;
    $teamLogoPath = secure_url('/') . Settings::where(['setting_key' => 'team_logo_path'])->first()->setting_value;
    $languageLogoPath = secure_url('/') . Settings::where(['setting_key' => 'language_flag_path'])->first()->setting_value;
    
    $event = DB::table('events AS e')
      ->leftJoin('teams AS ht', 'e.home_team_id', '=', 'ht.team_id')
      ->leftJoin('teams AS at', 'e.away_team_id', '=', 'at.team_id')
      ->leftJoin('competitions AS c', 'c.competition_id', '=', 'e.competition_id')
      ->leftJoin('nations AS n', 'n.nation_id', '=', 'e.nation_id')
      ->where('e.event_id', $eventId)
      ->select('e.event_id','e.start_date','e.end_date','e.game_week','e.event_status','e.event_title','e.round_name',
        'ht.team_name AS home_team', DB::raw('CONCAT("' . $teamLogoPath . '",ht.team_logo) AS home_team_logo'), 'at.team_name AS away_team',
        DB::raw('CONCAT("' . $teamLogoPath . '",at.team_logo) AS away_team_logo'), 'n.nation_name', 'c.competition_name',
        DB::raw('CONCAT("' . $competitionLogoPath . '",c.competition_logo) AS competition_logo'))
      ->first();
    if (is_null($event)) {
      return response()
        ->json([
          'status' => false,
          'message' => 'No events, Incorrect event ID'
        ])->header('Content-Type', 'application/json; charset=utf-8');
    } else {
      $vStreams = DB::table('streams AS s')
        ->leftJoin('users AS u', 'u.id', 's.user_id')
        ->leftJoin('languages AS l', 'l.language_name', 's.language')
        ->leftJoin('evaluations AS ev', function ($join) {
          $join->on('ev.stream_id', '=', 's.stream_id');
          $join->where('ev.eval_type', '=', 1);
        })
        ->where(['s.event_id' => $eventId, 'u.verified_user' => 1, 'u.ban' => 0, 's.approved' => 1])
        ->groupBy('s.stream_id')
        ->orderBy('vote', 'desc')
        ->select('s.*', 'u.name AS username', 'u.verified_user', DB::raw('CONCAT("'.$languageLogoPath.'",l.language_flag,".png") AS language_flag'),
          DB::raw('COUNT(ev.id) AS vote'))->get();
      
      $streams = DB::table('streams AS s')->leftJoin('users AS u', 'u.id', 's.user_id')
        ->leftJoin('languages AS l', 'l.language_name', 's.language')
        ->leftJoin('evaluations AS ev', function ($join) {
          $join->on('ev.stream_id', '=', 's.stream_id');
          $join->where('ev.eval_type', '=', 1);
        })
        ->where(['s.event_id' => $eventId, 'u.verified_user' => 0, 'u.ban' => 0, 's.approved' => 1])
        ->groupBy('s.stream_id')
        ->orderBy('vote', 'desc')
        ->select('s.*', 'u.name AS username', 'u.verified_user', DB::raw('CONCAT("'.$languageLogoPath.'",l.language_flag,".png") AS language_flag'),
          DB::raw('COUNT(ev.id) AS vote'))->get();
      return response()
        ->json([
          'status' => true,
          'event' => $event,
          'vstreams' => $vStreams,
          'streams' => $streams
        ])->header('Content-Type', 'application/json; charset=utf-8');
      
    }
  }
}
