<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Evaluation;
use App\Event;
use App\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class NewsController extends BaseController
{
  public function __construct()
  {
    parent::__construct();
  }
  
  public function showNews(Request $request, $eventId)
  {
    $languages = DB::table('languages')->get();

    $event = Cache::remember('event_' . $eventId, 3, function () use ($eventId) {
       return Event::getEventInfo($eventId);
    });
    if (is_null($event)) {
      return redirect('/')->with('error', 'This match no longer available!');
    } 
    else {

      if( strtotime( $event->end_date) + 1 * 60 * 60 < time()  )
      {
          DB::table('news')->where('event_id', '=', $eventId)->delete();
          Cache::flush();
      }

      $allNews = Cache::remember('news' . $eventId, 3, function () use ($eventId) {
        return News::getAllEventNews($eventId, Auth::check() ? Auth::id() : 0);
      });     
     
       $orderType = 'VOTE_DESC';

      return view('news', [
        'allNews' => $allNews,
        'event' => $event,
        'languages' => $languages,
        'order_type' => $orderType
      ]);
    }
    
  }
  
  
  
}
