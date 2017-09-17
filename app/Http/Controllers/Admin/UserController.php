<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\Stream;
use App\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
  public function index()
  {
    $verifiedUsers = DB::table('users')->where(['verified_user' => 1, 'ban' => 0])->get();
    $banUsers = DB::table('users')->where('ban', 1)->get();
    $regularUsers = DB::table('users')->where(['ban' => 0, 'verified_user' => 0, 'role' => 0])->get();
    $moderatorUsers = DB::table('users')->where(['role' => 1, 'ban' => 0])->get();
    $adminUsers = DB::table('users')->where(['role' => 2, 'ban' => 0])->get();
    $sponsorUsers = DB::table('users')->where(['role' => 0, 'ban' => 0,'sponsor'=>1])->get();
    
    return view('admin.usersList', [
      'verifiedUsers' => $verifiedUsers,
      'banUsers' => $banUsers,
      'regularUsers' => $regularUsers,
      'moderatorUsers' => $moderatorUsers,
      'adminUsers'=>$adminUsers,
      'sponsorUsers'=>$sponsorUsers
    ]);
  }
  
  public function setSponsorUser($userId, $value)
  {
    $user = User::find($userId);
    $user->sponsor = $value;
    $user->save();
    Cache::flush();
    return redirect('moderator/user')->with('done', 'User sponsor status updated');
  }
  
  public function setVerifyUser($userId, $value)
  {
    $user = User::find($userId);
    $user->verified_user = $value;
    $user->save();
    Cache::flush();
    return redirect('moderator/user')->with('done', 'User verification status updated');
  }

  public function setApprovedUser($userId, $value)
  {
    $user = User::find($userId);
    $user->approved = $value;
    $user->save();
    Cache::flush();
    return redirect('moderator/user')->with('done', 'User approve status updated');
  }

  public function setBanUser($userId, $value)
  {
    $user = User::find($userId);
    $user->ban = $value;
    $user->save();
    
    $streams = DB::table('streams')->where('user_id', '=', $userId )->get();
    foreach( $streams as $stream ){
        Cache::forget( 'streams_'.$stream->event_id );
    }
    Cache::flush();
    return redirect('moderator/user')->with('done', 'User ban status updated');
  }
  
  public function setModerator($userId, $value)
  {
    $user = User::find($userId);
    $user->role = $value;
    $user->save();
    Cache::flush();
    return redirect('moderator/user')->with('done', 'User moderator status updated');
    
  }
  
  public function setAdmin($userId, $value)
  {
    $user = User::find($userId);
    $user->role = $value;
    $user->save();
    return redirect('moderator/user')->with('done', 'User set as admin done');
    
  }
  
  public function broadcast(){
    $messages = Notification::where(['type' => 2])
              ->orWhere(['type' => 3])
              ->groupBy('title')->orderBy('created_at', 'desc')->get();
    return view('admin.broadcast')->withMessages($messages);
  }

  public function userMessages(){
    $messages = DB::table('notifications AS n')
              ->leftJoin('users AS u', 'u.id', 'n.actor_id')
              ->orderBy('created_at', 'desc')
              ->where(['type' => 1, 'target_id' => Auth::id()])
              ->select('n.*', 'u.name AS username')->get();

    return view('admin.userMessages')->withMessages($messages);
  }

  public function sendMessage( Request $request)
  {
      $userId = $request->userId;
      
  }
  
}
