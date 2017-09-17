<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Favourite;
use App\Notification;
use Illuminate\Support\Facades\Auth;

class MessageController extends BaseController
{
	public function index()
	{
      if(Auth::guest())
         return redirect('/login');
      
      $messages = parent::getAllNotifications();
      $unread = parent::getUnreadMessages();
      return view('messages', compact('messages'))->withUnread($unread);
	}

	public function markAsRead( Request $request )
	{
		$message = Notification::find( $request->messageId );
      	$message->action     = 1;

      	$message->save();
      	echo 1;
   	}

   	public function reply( Request $request )
   	{
   		$messageId 	= $request->messageId;
   		$message 	= $request->body;

   		$notification = Notification::find( $messageId );
   		$from 		= $notification->target_id;
   		$to 		= $notification->actor_id;

   		$reply = new Notification;
   		$reply->actor_id 	= $from;
   		$reply->target_id 	= $to;
   		$reply->message 	= $message;
   		$reply->action 		= 0;
   		$reply->type 		= 1;
   		$reply->title 		= "Re: ". $notification->title;

   		$reply->save();
   		echo true;
   	}

      public function sendPrivateMessage( Request $request )
      {
         $to  = $request->to;
         $body    = $request->body;
         $permalink = $request->permalink;

         if( !$body || !$to ){
            echo 0;
            exit;
         }
         $message = new Notification;
         $message->actor_id   = Auth::id();
         $message->target_id  = $to;
         $message->message    = $body;
         $message->action     = 0;
         $message->type       = 2;
         $message->title      = "New message from " . Auth::User()->name;

         $message->save();
         return back();
      }

   	public function sendMessage( Request $request )
   	{
   		$userId 	= $request->userId;
   		$body  	= $request->body;
         $permalink = $request->permalink;

   		if( !$body || !$userId ){
   			echo 0;
   			exit;
   		}

         $message = new Notification;
   		$message->actor_id 	= Auth::id();
   		$message->target_id 	= $userId;
   		$message->message 	= $body. ' ' .$permalink;
   		$message->action 		= 0;
   		$message->type 		= 1;
   		$message->title 		= "New message from Admin";

   		$message->save();
   		echo true;
   	}
}