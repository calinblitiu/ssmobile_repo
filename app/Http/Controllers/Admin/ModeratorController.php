<?php

namespace App\Http\Controllers\Admin;

use App\Activity;
use App\Http\Controllers\Controller;
use App\Notification;
use App\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModeratorController extends Controller
{
  public function login()
  {
    return view('admin.login');
  }
  
  public function doLogin(Request $request)
  {
    dd($request);
  }
  
  public function dashboard()
  {
    return view('admin.dashboard');
  }
  
  public function log(){
    $logs = Activity::orderBy('created_at','desc')->get();
    return view('admin.log')->withLogs($logs);
  }
  
  public function notify(Request $request){
    $notification = new Notification;
    $notification->actor_id   = $request->from;
    $notification->target_id  = $request->to;
    $notification->message    = $request->data;
    $notification->type       = isset( $request->type ) ? $request->type : 2;

    if( isset( $request->title ) )
      $notification->title = $request->title;

    if( isset( $request->colorInfo ) )
      $notification->colorInfo = $request->colorInfo;
    
    $notification->save();
    
  }

  public function broadcast( Request $request ){
    $title    = $request->title;
    $message  = $request->body;
    $from     = Auth::id();
    $group    = $request->group;
    $type     = $request->type;
    $color    = json_encode( $request->colorInfo );

    if( $group == 1 )
      $notify_users = User::where("verified", '=', 1)->get();
    elseif( $group == 2 )
      $notify_users = User::where("verified", '=', 0)->get();
    elseif( $group == 3 )      
      $notify_users = User::where("ban", '=', 1)->get();
    else
      $notify_users = User::where("id", '>', 0)->get();

    $send_request = new Request;

    foreach( $notify_users as $to )
    {
      $send_request->from   = $from;
      $send_request->to     = $to->id;
      $send_request->data   = $message;
      $send_request->title  = $title;
      $send_request->type   = $type;
      $send_request->colorInfo   = $color;

      $this->notify( $send_request );
    }
  }

  public function deleteBroadcast( Request $request )
  {
    $messageId = $request->id;
    $message = Notification::find( $messageId );

    $messages = Notification::where("title", '=', $message->title)->get();
    foreach( $messages as $message )
      $message->delete();

    echo true;
  }
}
