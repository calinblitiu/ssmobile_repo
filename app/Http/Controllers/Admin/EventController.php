<?php

namespace App\Http\Controllers\Admin;

use App\Competition;
use App\Nation;
use App\Sport;
use App\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Event;
use Carbon\Carbon;
use Datatables;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
  
  public function index()
  {
  
    $event = new Event;
    $events = $event->getEventsByInterval();
    
    return view('admin.events')->withEvents($events);
  }
  
  public function delete($eventId)
  {
    $event = Event::find($eventId);
    $event->delete();
    Cache::flush();
    echo true;
  }
  
  public function createEvent()
  {
    $nations = Nation::all();
    $sports = Sport::all();
    $competitions = Competition::all();
    return view('admin.createEvent',
      ['nations' => $nations, 'sports' => $sports, 'competitions' => $competitions]
    );
  }
  
  public function storeEvent(Request $request)
  {
    $event = new Event;
    $event->start_date = $request->kickoff;
    $event->end_date = $request->end_date;
    $event->sport_id = $request->sport;
    $event->competition_id = $request->competition;
    $event->nation_id = $request->nation;
    $event->event_title = $request->title;
    $event->save();
    Cache::forget('allEvents');
    Cache::flush();
    if ($request->submit == 'back') {
      return redirect('moderator/event')->with('done','New event added successfully');
    } else {
      return back()->with('done','New event added successfully');
    }
  }
  
  public function createMatch()
  {
    $nations = Nation::all();
    $sports = Sport::all();
    $competitions = Competition::all();
    $teams = Team::all();
    return view('admin.createMatchEvent', ['nations' => $nations, 'sports' => $sports, 'competitions' => $competitions, 'teams' => $teams]);
  }
  
  public function storeMatch(Request $request)
  {
    $event = new Event;
    $event->start_date = $request->kickoff;
    $event->end_date = $request->end_date;
    $event->sport_id = $request->sport;
    $event->competition_id = $request->competition;
    $event->home_team_id = $request->home_team;
    $event->away_team_id = $request->away_team;
    $event->nation_id = $request->nation;
    if(isset($request->game_week)){
      $event->game_week = $request->game_week;
    }
    if(isset($request->round_name)){
      $event->round_name = $request->round_name;
    }
    $event->save();
    Cache::forget('allEvents');
    Cache::flush();
    if ($request->submit == 'back') {
      return redirect('moderator/event')->with('done','New event added successfully');
    } else {
      return back()->with('done','New event added successfully');
    }
  }


  /**
   * undocumented function
   *
   * @return void
   * @author Risul Islam - risul321@gmail.com
   **/
  public function updateEventDate(Request $request, $eventId)
  {
    $event = Event::find($eventId);
    $event->start_date = $request->start_date;
    $event->end_date = $request->end_date;
    $event->save();
    Cache::forget('allEvents');
    Cache::flush();
    return redirect('moderator/event');
  }
}