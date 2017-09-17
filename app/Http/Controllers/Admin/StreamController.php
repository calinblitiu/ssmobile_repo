<?php

namespace App\Http\Controllers\Admin;

use App\Evaluation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Facades\Datatables;
use \App\Stream;
use \App\Domain;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class StreamController extends Controller
{
  public function showStreams(Request $request, $eventId)
  {
    $streams = DB::table('streams AS s')->where(['s.event_id' => $eventId])->get();
    $event = DB::table('events AS e')
      ->leftJoin('teams AS ht', 'e.home_team_id', '=', 'ht.team_id')
      ->leftJoin('teams AS at', 'e.away_team_id', '=', 'at.team_id')
      ->leftJoin('competitions AS c', 'c.competition_id', '=', 'e.competition_id')
      ->leftJoin('nations AS n', 'n.nation_id', '=', 'e.nation_id')
      ->where('e.event_id', $eventId)
      ->select('e.*', 'ht.team_name AS home_team', 'ht.team_logo AS home_team_logo', 'at.team_name AS away_team', 'at.team_logo AS away_team_logo', 'n.nation_name')
      ->first();
    return view('streams')->withStreams($streams)->withEvent($event);
  }
  
  public function waitingApprove()
  {
    $events = DB::table('events AS e')
      ->leftJoin('streams AS s', 'e.event_id', 's.event_id')
      ->where('s.approved', '0')
      ->groupBy('e.event_id')
      ->select(DB::raw('COUNT(s.stream_id) AS s_count'), 'e.event_id');
    $eventsStream = DB::table('events AS ev')
      ->rightJoin(DB::raw('(' . $events->toSql() . ') ev_table'), 'ev_table.event_id', 'ev.event_id')
      ->addBinding(0)
      ->leftJoin('teams AS ht', 'ev.home_team_id', '=', 'ht.team_id')
      ->leftJoin('teams AS at', 'ev.away_team_id', '=', 'at.team_id')
      ->select('ev.event_id', 'ev_table.s_count', 'ht.team_name AS home_team', 'at.team_name AS away_team', 'ev.start_date')
      ->get();
    return view('admin.approveStream')->withEvents($eventsStream);
  }
  
  public function publishedStreams()
  {
    $today = Carbon::now();
    $duration = Carbon::now()->addHours(24);
    $events = DB::table('events AS e')
      ->leftJoin('streams AS s', 'e.event_id', 's.event_id')
      ->where('s.approved', '1')
      ->where('e.end_date', '>=', $today)
      ->where('e.start_date','<=',$duration)
      ->where('e.sport_id',1)
      ->groupBy('e.event_id')
      ->select(DB::raw('COUNT(s.stream_id) AS s_count'), 'e.event_id');
    $eventsStream = DB::table('events AS ev')
      ->rightJoin(DB::raw('(' . $events->toSql() . ') ev_table'), 'ev_table.event_id', 'ev.event_id')
      ->addBinding(1)
      ->addBinding($today)
      ->addBinding($duration)
      ->addBinding(1)
      ->leftJoin('teams AS ht', 'ev.home_team_id', '=', 'ht.team_id')
      ->leftJoin('teams AS at', 'ev.away_team_id', '=', 'at.team_id')
      ->orderBy('ev.start_date', 'ASC')
      ->select('ev.event_id', 'ev_table.s_count', 'ht.team_name AS home_team', 'at.team_name AS away_team', 'ev.start_date')
      ->get();
    return view('admin.publishedStreams')->withEvents($eventsStream);
  }
  
  public function approve($streamId)
  {
    $stream = Stream::find($streamId);
    $stream->approved = 1;
    $stream->save();
    Cache::flush();
    parent::registerLog('Approve Stream ID: ' . $streamId, 2);
  }
  
  public function delete($streamId)
  {
    $stream = Stream::find($streamId);
    $stream->delete();
    parent::registerLog('Delete Stream ID: ' . $streamId, 3);

    Cache::forget( 'streams_'.$stream->event_id );
    Cache::flush();
    echo true;
  }
  
  public function getDisapprovalStreams()
  {
    return Datatables::queryBuilder(
      DB::table('streams AS s')
        ->leftJoin('events AS e', 'e.event_id', 's.event_id')
        ->leftJoin('teams AS ht', 'e.home_team_id', '=', 'ht.team_id')
        ->leftJoin('teams AS at', 'e.away_team_id', '=', 'at.team_id')
        ->where('s.approved', 0)
        ->orderBy('s.added_time', 'desc')
        ->select('ht.team_name AS home_team', 'at.team_name AS away_team', 's.*')
    )->make(true);
  }
  
  public function showStreamEvaluations(Request $request, $streamId)
  {
    $reports = Evaluation::where(['stream_id' => $streamId, 'eval_type' => 0])->get();
    return view('admin.streamEvaluations', ['reports' => $reports]);
  }

  public function banDomainAction( $streamId )
  {
      $stream = Stream::find($streamId);

      if( !$stream['domain'] ){
          echo "no registered domain";
          return ;
      }

      $domain = Domain::where('domain', '=', $stream['domain'] )->first();
      if( empty( $domain ) )
          $domain = new Domain;

      $parse = parse_url( $stream['url'] );

      $domain->domain  = $parse['host'];
      $domain->ban     = 1;
      $domain->save();

      Cache::forget( 'streams_'.$stream->event_id );
      Cache::forget( 'bannedDomains' );
      Cache::flush();
      echo "true";
  }
}
