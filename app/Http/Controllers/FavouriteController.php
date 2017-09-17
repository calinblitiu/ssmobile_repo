<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Competition;
use App\Favourite;
use App\Team;
use Auth;
use Illuminate\Support\Facades\Cache;

class FavouriteController extends BaseController
{
  /**
   * undocumented function
   *
   * @return view
   * @author risul321@gmail.com
   **/
  public function index()
  {
    $favourites = Favourite::where('user_id', Auth::user()->id)->get();
    $unread = parent::getUnreadMessages();
    
    return view('favourite', compact('favourites'))->withUnread($unread);
  }
  
  /**
   * undocumented function
   *
   * @return void
   * @author risul321@gmail.com
   **/
  public function search($type, $query = "")
  {
    $result = array();
    
    if ($query == "")
      return;
    
    if ($type === "team") {
      $teams = Team::where('team_name', 'like', '%' . $query . '%')
        ->get();
      
      foreach ($teams as $team) {
        $result[] = array('name' => $team->nation->nation_name . ' - ' . $team->team_name, 'value' => $team->team_id, 'text' => $team->nation->nation_name . ' - ' . $team->team_name);
      }
    } else {
      $competitions = Competition::where('competition_name', 'like', '%' . $query . '%')
        ->get();
      
      foreach ($competitions as $competition) {
        $result[] = array('name' => $competition->nation->nation_name . ' - ' . $competition->competition_name, 'value' => $competition->competition_id, 'text' => $competition->nation->nation_name . ' - ' . $competition->competition_name);
      }
    }
    
    $data["results"] = $result;
    
    return response()->json($data);
  }
  
  /**
   * undocumented function
   *
   * @return void
   * @author risul321@gmail.com
   **/
  public function store(Request $request)
  {
    $items = $request->items;
    $item = strtok($items, ',');
    while ($item !== false) {
      $favourite = new Favourite;
      
      $item_name = "";
      if ($request->type === "Team") {
        $item_name = Team::find($item)->team_name;
      } else {
        $item_name = Competition::find($item)->competition_name;
      }
      
      $favourite->user_id = Auth::user()->id;
      $favourite->item_id = $item;
      $favourite->item_name = $item_name;
      $favourite->item_type = $request->type;
      
      // Add new favourite item
      $favourite->save();
      
      $item = strtok(',');
      
    }
    Cache::flush();
    return redirect()->back();
  }
  
  /**
   * undocumented function
   *
   * @return void
   * @author risul321@gmail.com
   **/
  public function delete($id)
  {
    Favourite::find($id)->delete();
    Cache::flush();
    return redirect()->back();
  }
  
}