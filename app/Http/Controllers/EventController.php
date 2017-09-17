<?php

namespace App\Http\Controllers;

use App\Event;
use App\Favourite;
use Carbon\Carbon;
use \Datatables;
use \Auth;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EventController extends BaseController
{
  public function __construct()
  {
    parent::__construct();
  }
  
  public function index()
  { 
    /*$event = new Event;
    $events = $event->getEventsByInterval();*/
    
    $events = $this->getEventList();

    // Sort by popular competitions
    $favourite = new Favourite;
    $events = $favourite->sortEventsByPopularity($events);
    $date = '';
    $events_filtered = [];
    $i = 0;

    foreach ($events as $event) {
      $dateWithTimezone = \Carbon\Carbon::parse($event->start_date)->addMinute(\Session::get('visitorTZ')*60)->format('d M Y');
      if ($date != date('d M Y', strtotime($dateWithTimezone)) && $date != '') {
        $i++;
      }
      $events_filtered[$i][] = $event;
      $date = date('d M Y', strtotime($dateWithTimezone));
    }

    $competitions = $this->_extractCompetitions($events);

    if(Auth::guest()){
      return view('homePage', ['competitions' => $competitions, 'events' => $events_filtered]);
    }else{
      // Sort by favourite team/competition
      //$favourite = new Favourite;
      $events_filtered = $favourite->sortEventsByFavourite($events_filtered);
  
      $favourites = Favourite::where('user_id', Auth::user()->id)->pluck('item_name');
      $notifications = $this->getNotifications();
      foreach( $notifications as &$notification)
      {
          $notification->color = "";
          $colors = $notification->colorInfo;
          if( $colors )
          {
              $color = json_decode( $colors );
              $notification->color = "background:".$color->bgcolor.";color:".$color->txtcolor.";border-color:".$color->border_selector;
          }
      }  
      return view('homePage', ['competitions' => $competitions, 'events' => $events_filtered, 'favourites' => $favourites, 'notifications' => $notifications ]);
    }
  }
  
  private function _extractCompetitions($events)
  {
    $competitions = [];
    foreach ($events as $event) {
      $comp = ['competition_name' => $event->competition_name, 'nation_name' => $event->nation_name];
      if (!in_array($comp, $competitions)) {
        array_push($competitions, ['competition_name' => $event->competition_name, 'nation_name' => $event->nation_name]);
      }
    }

    return $competitions;
  }

  public function updateHomepageScores(Request $request){
    if ($request->has('check')) {
      $request->session()->put('updateScore', $request->check);
      return redirect()->back();
    }

    if($request->data == null)
      return response()->json([
        'result' => 0
      ]);
      
    $res = [];

    foreach ($request->data as $data) {
      $event_minute = Cache::store(env('CACHE_DRIVER'))->get( "{$data}._minute");
      $event_status = Cache::store(env('CACHE_DRIVER'))->get( "{$data}._status");
      $event_id = $data;

      $res[] = array('event_minute' => $event_minute, 'event_status' => $event_status, 'event_id' => $event_id);
    }

    // $res = Event::select('event_minute', 'event_status', 'event_id')->whereIn('event_id', $request->data)
    //             ->get();
    // dd($res);

    return response()->json([
      'result' => $res
    ]);
  }
}