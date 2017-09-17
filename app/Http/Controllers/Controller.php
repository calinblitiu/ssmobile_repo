<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Activity;
use App\User;

class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
  
  public static function registerLog($actionDescription, $SQLType = 0)
  {
    $action = new Activity();
    $action->actor = Auth::id();
    $action->action = $actionDescription;
    if ($SQLType != 0) {
      $action->sql_type = $SQLType;
    }
    $action->save();
  }
  
  public function getNotifications()
  {
    return Notification::where(['target_id' => Auth::id(), 'action' => 0])->orderBy('created_at', 'desc')->get();
  }

  public function getAllNotifications()
  {
    $notifications = Notification::where(['target_id' => Auth::id()])->orderBy('created_at', 'desc')->get();
    foreach( $notifications as $key => $not )
    {
      $user = User::find( $not->actor_id );
      $notifications[$key]['username'] = $user['name'];
    }

    return $notifications;
  }  

  public function getUnreadMessages(){
    return Notification::where(['target_id' => Auth::id(), 'action' => 0, 'type' => 1])->orderBy('created_at', 'desc')->get(); 
  }

}
