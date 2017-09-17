<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Evaluation;
use App\Event;
use App\Stream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StreamController extends BaseController
{
  public function __construct()
  {
    parent::__construct();
  }
  
  public function showMatchStreams(Request $request, $eventId)
  {
    $languages = DB::table('languages')->get();

    if (isLocalDev())  Cache::flush();

    $event = Cache::remember('event_' . $eventId, 3, function () use ($eventId) {
      return Event::getEventInfo($eventId);
    });
    if (is_null($event)) {
      return redirect('/')->with('error', 'This match no longer available!');
    } else {

      if (!isLocalDev()) {

        if( strtotime( $event->end_date) + 2 * 60 * 60 < time()  )
        {
            DB::table('streams')->where('event_id', '=', $eventId)->delete();
            Cache::flush();
        }

      }

      $allStreams = Cache::remember('streams_' . $eventId, 3, function () use ($eventId) {
        return Stream::getAllEventStreams($eventId, Auth::check() ? Auth::id() : 0);
      });

      $streamComments = array();
      foreach ($allStreams as $stream) {
        $streamId = $stream->stream_id;
        $sc = Cache::remember('streamsComments_' . $streamId, 3, function () use ($streamId) {
          return Comment::getStreamComments($streamId);
        });

        $tsc = $this->_buildTree($sc);
        $streamComments[$streamId] = array('comments' => $sc, 'treeComments' => $tsc);
      }

      $comments = Cache::remember('event_comments_' . $eventId, 3, function () use ($eventId) {
        return Comment::getEventComments($eventId, true);
      });
      $aceComments = Cache::remember('event_ace_comments_' . $eventId, 3, function () use ($eventId) {
        return Comment::getEventAceStreamComments($eventId);
      });
      $aceComments = $this->_buildTree($aceComments);

      $commentsCount = count($comments);
      $hComments = $this->_buildTree($comments);
      $streamTypes = $this->_extractStreamsTypes($allStreams);
      $streamQuality = $this->_extractStreamsQuality($allStreams);
      $streamLanguage = $this->_extractStreamsLanguage($allStreams);
      $groupInfo = $this->_buildGroupByUser( $allStreams );

      foreach ($groupInfo as $key => $group) {        
        foreach($group['data'] as $stream){
          if(array_key_exists('usc', $groupInfo[$key])){
            $groupInfo[$key]['usc'] = $groupInfo[$key]['usc']->merge($streamComments[$stream->stream_id]['comments']);
          } else {
            $groupInfo[$key]['usc'] = $streamComments[$stream->stream_id]['comments'];            
          }
        }
        $groupInfo[$key]['usc'] = $this->_buildTree($groupInfo[$key]['usc']);
      }

      $commentSortOptions = Array(
        'VOTE_DESC' => 'Sort by newest voteup',
        'DATE_DESC' => 'Sort by newest first',
        'DATE_ASC'  => 'Sort by oldest first'
      );
      $orderType = 'VOTE_DESC';

      $userCommentCount = Array();
      foreach($comments as $comment) {
        $userCommentCount[$comment->id] = Comment::where(['user_id' => $comment->user_id ])->count();
      }
      $liked_streammers = (Auth::check()) ? 
                              Cache::remember('liked_streammers_' . Auth::user()->id, 3, function () {
                                return Auth::user()->streammersLiked()->pluck('streammer_id')->all();
                              }) : array();
      return view('streams', [
        'allStreams' => $allStreams,
        'streamComments' => $streamComments,
        'groupInfo' => $groupInfo,
        'event' => $event,
        'streamTypes' => $streamTypes,
        'streamQuality' => $streamQuality,
        'streamLanguage' => $streamLanguage,
        'comments' => $comments,
        'comment_count' => $commentsCount,
        'hComments' => $hComments,
        'languages' => $languages,
        'comment_sort_options' => $commentSortOptions,
        'order_type' => $orderType,
        'user_comment_count' => $userCommentCount,
        'aceComments' => $aceComments,
        'liked_streammers' => $liked_streammers
      ]);
    }
    
  }

  public function showMatchChannels(Request $request, $eventId)
  {
    
    $languages = DB::table('languages')->get();

    if (isLocalDev())  Cache::flush();

    $event = Cache::remember('event_' . $eventId, 3, function () use ($eventId) {
      return Event::getEventInfo($eventId);
    });
    if (is_null($event)) {
      return redirect('/')->with('error', 'This match no longer available!');
    } else {

      if (!isLocalDev()) {

        if( strtotime( $event->end_date) + 2 * 60 * 60 < time()  )
        {
            DB::table('streams')->where('event_id', '=', $eventId)->delete();
            Cache::flush();
        }

      }

      $allStreams = Cache::remember('streams_' . $eventId, 3, function () use ($eventId) {
        return Stream::getAllEventStreams($eventId, Auth::check() ? Auth::id() : 0);
      });

      $streamComments = array();
      foreach ($allStreams as $stream) {
        $streamId = $stream->stream_id;
        $sc = Cache::remember('streamsComments_' . $streamId, 3, function () use ($streamId) {
          return Comment::getStreamComments($streamId);
        });

        $tsc = $this->_buildTree($sc);
        $streamComments[$streamId] = array('comments' => $sc, 'treeComments' => $tsc);
      }
      
      $comments = Cache::remember('event_comments_' . $eventId, 3, function () use ($eventId) {
        return Comment::getEventComments($eventId);
      });

      $commentsCount = Comment::where(['event_id' => $eventId ])->count();
      $hComments = $this->_buildTree($comments);
      $streamTypes = $this->_extractStreamsTypes($allStreams);
      $streamQuality = $this->_extractStreamsQuality($allStreams);
      $streamLanguage = $this->_extractStreamsLanguage($allStreams);

      $groupInfo = $this->_buildGroupByUser( $allStreams );

      $commentSortOptions = Array(
        'VOTE_DESC' => 'Sort by newest voteup',
        'DATE_DESC' => 'Sort by newest first',
        'DATE_ASC' => 'Sort by oldest first'
      );
      $orderType = 'VOTE_DESC';

      $userCommentCount = Array();
      foreach($comments as $comment) {
        $userCommentCount[$comment->id] = Comment::where(['user_id' => $comment->user_id ])->count();
      }

      return view('channels', [
        'allStreams' => $allStreams,
        'streamComments' => $streamComments,
        'groupInfo' => $groupInfo,
        'event' => $event,
        'streamTypes' => $streamTypes,
        'streamQuality' => $streamQuality,
        'streamLanguage' => $streamLanguage,
        'comments' => $comments,
        'comment_count' => $commentsCount,
        'hComments' => $hComments,
        'languages' => $languages,
        'comment_sort_options' => $commentSortOptions,
        'order_type' => $orderType,
        'user_comment_count' => $userCommentCount
      ]);
    }
  }

  public function showChannelPage(Request $request, $country, $channel)
  { 
    $channelName = $channel;      
    $countryName = $country;
    $events = DB::table('events')->get();
    $competition = DB::table('competitions')->get();
    $team        = DB::table('teams')->get();
    $allChannels    = DB::table("channels")->get();
    $channel_description = "";
    $channel_acquire = "";
    
    foreach ($allChannels as $ch) {
      //echo  ($ch->channel_name)."\n";
      if(($ch->slug)==$channelName)
      {

        $channel_description = $ch->description;
        $channel_acquire = $ch->acquire_rights;
      }
    }    

      
    $url="http://api.livesoccertv.com/";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL,$url);
    $result=curl_exec($ch);
    curl_close($ch);
    $api_key = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result),true);

    $hash = hash('md5', "some soccer" . $api_key["secret"] . "_ salt");
    // pad with zeroes if hash length is less than 32
    while (strlen($hash) < 32) {
        $hash = "0" . $hash;
    }

    $channel_url="http://api.livesoccertv.com/channels/".$channelName."/";
    $cha = curl_init();
    curl_setopt($cha, CURLOPT_RETURNTRANSFER, true);
    // Set the header
    curl_setopt($cha, CURLOPT_HTTPHEADER, array('X-Api-Token:'. $hash));
    curl_setopt($cha, CURLOPT_URL, $channel_url);
    $result1=curl_exec($cha);
    curl_close($cha);

    $channels_data = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $result1),true);


    return view('channelPage', [
      'channelName'=>$channelName,
      'country'=>$countryName,
      'allEvents'=>$events,
      'allCompetitions'=>$competition,
      'allTeams'=>$team,
      'allSchedule'=>$channels_data,
      'channel_description'=>$channel_description,
      'channel_acquire'=>$channel_acquire
      ]);
  }


  
  public static function getComments($streamId)
  {
    return Cache::remember('stream_comments_' . $streamId, 1, function () use ($streamId) {
      $commentsArray =  Comment::getStreamComments($streamId)->toArray();
      return self::_buildTree($commentsArray);
    });
  }

  public static function getCommentsCount($streamId)
  {
    $commentsArray =  Comment::getStreamComments($streamId)->toArray();
    $userCommentCount = Array();
    foreach($commentsArray as $comment) {
      $userCommentCount[$comment->id] = Comment::where(['user_id' => $comment->user_id ])->count();
    }
  }

  public function getEventCommentsInOrder(Request $request)
  {
    $eventId = $request->eventId;
    $orderType = $request->orderType;

    $comments = Comment::getEventCommentsInOrder($eventId, $orderType);
    $hComments = $this->_buildTree($comments);
    $commentsCount = Comment::where(['event_id' => $eventId ])->count();
    $allStreams = Cache::remember('streams_' . $eventId, 3, function () use ($eventId) {
      return Stream::getAllEventStreams($eventId, Auth::check() ? Auth::id() : 0);
    });
    $commentSortOptions = Array(
      'VOTE_DESC' => 'Sort by newest voteup',
      'DATE_DESC' => 'Sort by newest first',
      'DATE_ASC' => 'Sort by oldest first'
    );

    $userCommentCount = Array();
    foreach($comments as $comment) {
      $userCommentCount[$comment->id] = Comment::where(['user_id' => $comment->user_id ])->count();
    }

    return view('eventComments', [
      'streams' => $allStreams,
      'allStreams' => $allStreams,
      'comments' => $comments,
      'event_id' => $eventId,
      'comment_count' => $commentsCount,
      'treeComments' => $hComments,
      'comment_sort_options' => $commentSortOptions,
      'order_type' => $orderType,
      'user_comment_count' => $userCommentCount
    ]);
  }
  
  public function showEventStreams(Request $request, $eventId)
  {
    
    $event = Event::getEventInfo($eventId);
    if (is_null($event)) {
      return redirect('/')->with('error', 'This event no longer available!');
    } else {
      $vStreams = Stream::getStreams($eventId, 1);
      $streams = Stream::getStreams($eventId, 0);
      $streamTypes = $this->_extractStreamTypes($vStreams, $streams);
      $streamQuality = $this->_extractStreamQuality($vStreams, $streams);
      $streamLanguage = $this->_extractStreamLanguage($vStreams, $streams);
      return view('eventStreams', [
        'vStream' => $vStreams,
        'streams' => $streams,
        'event' => $event,
        'streamTypes' => $streamTypes,
        'streamQuality' => $streamQuality,
        'streamLanguage' => $streamLanguage,
      ]);
    }
    
  }
  
  public function voteStream(Request $request)
  {
    $streamId = $request->stream;
    $eventId = $request->eventId;
    $evaluation = Evaluation::where('user_id', '=', Auth::id())->where('stream_id', '=', $streamId )->first();

    if( $evaluation === null )
    {
      $evaluation = new Evaluation;
      $evaluation->user_id = Auth::id();
      $evaluation->stream_id = $streamId;
      $evaluation->eval_type = 1;
      $evaluation->save();
      echo 1;
    }
    else{
      $evaluation->delete();
      echo 0;
    }
    Cache::forget('streams_' . $eventId);
    Cache::flush();
  }

  public function votedownStream(Request $request)
  {
    $streamId = $request->stream;
    $eventId = $request->eventId;
    $evaluation = Evaluation::where('user_id', '=', Auth::id())->where('stream_id', '=', $streamId )->first();

    if( $evaluation === null )
    {
      $evaluation = new Evaluation;
      $evaluation->user_id = Auth::id();
      $evaluation->stream_id = $streamId;
      $evaluation->eval_type = -1;
      $evaluation->save();
      echo 1;
    }
    else{
      $evaluation->delete();
      echo 0;
    }
    Cache::forget('streams_' . $eventId);
    Cache::flush();
  }
  
  public function recommendStreamByModerator(Request $request)
  {
    $streamId = $request->stream;
    $mdStream = Stream::find($streamId);
    $mdStream->mod_recommended = $request->action;
    $mdStream->save();
    Cache::forget('streams_' . $request->event);
    Cache::flush();
  }
  
  public function reportStream(Request $request)
  {
    $eventId = $request->eventId;
    $streamId = $request->stream;
    $evaluation = new Evaluation;
    $evaluation->user_id = Auth::id();
    $evaluation->stream_id = $streamId;
    $evaluation->comment = $request->comment;
    $evaluation->eval_type = 0;
    $evaluation->save();
    Cache::forget( 'streams_'.$eventId );
    Cache::flush();
  }
  
  private function _extractStreamTypes($vStreams, $streams)
  {
    $streamTypes = [];
    foreach ($streams as $stream) {
      if (!in_array($stream->stream_type, $streamTypes)) {
        array_push($streamTypes, $stream->stream_type);
      }
    }
    foreach ($vStreams as $stream) {
      if (!in_array($stream->stream_type, $streamTypes)) {
        array_push($streamTypes, $stream->stream_type);
      }
    }
    
    return $streamTypes;
  }
  
  private function _extractStreamQuality($vStreams, $streams)
  {
    $streamQuality = [];
    foreach ($streams as $stream) {
      if (!in_array($stream->quality, $streamQuality)) {
        array_push($streamQuality, $stream->quality);
      }
    }
    foreach ($vStreams as $stream) {
      if (!in_array($stream->quality, $streamQuality)) {
        array_push($streamQuality, $stream->quality);
      }
    }
    
    return $streamQuality;
  }
  
  private function _extractStreamLanguage($vStreams, $streams)
  {
    $streamLangs = [];
    foreach ($streams as $stream) {
      if (!in_array($stream->language, $streamLangs)) {
        array_push($streamLangs, $stream->language);
      }
    }
    foreach ($vStreams as $stream) {
      if (!in_array($stream->language, $streamLangs)) {
        array_push($streamLangs, $stream->language);
      }
    }
    
    return $streamLangs;
  }
  
  private function _extractStreamsTypes($streams)
  {
    $streamTypes = [];
    foreach ($streams as $stream) {
      if (!in_array($stream->stream_type, $streamTypes)) {
        array_push($streamTypes, $stream->stream_type);
      }
    }
    
    return $streamTypes;
  }
  
  private function _extractStreamsQuality($streams)
  {
    $streamQuality = [];
    foreach ($streams as $stream) {
      if (!in_array($stream->quality, $streamQuality)) {
        array_push($streamQuality, $stream->quality);
      }
    }
    
    return $streamQuality;
  }
  
  private function _extractStreamsLanguage($streams)
  {
    $streamLangs = [];
    foreach ($streams as $stream) {
      if (!in_array($stream->language, $streamLangs)) {
        array_push($streamLangs, $stream->language);
      }
    }
    
    return $streamLangs;
  }
  
  private static function _buildTree($elements, $parentId = 0)
  {
    $branch = array();
    
    foreach ($elements as $element) {
      if ($element->parent == $parentId) {
        $children = self::_buildTree($elements, $element->id);
        if ($children) {
          $element->replies = $children;
        }
        $branch[] = $element;
      }
    }
    
    return $branch;
  }
  
  public function checkBanDomain(Request $request)
  {
    
    $url = $request->url;
    $event_id = $request->eventId;
    $domains = DB::table('domains')->get();
    foreach ($domains as $domain) {
      if ((strpos($url, $domain->domain) !== false) && $domain->ban == 1) {
        echo 0;
        exit;
      }
    }
    
    $urls = DB::table('streams')->where('url', '=', $url)->where('event_id', '=', $event_id)->get();
    if (count($urls)) {
      echo 1;
      exit;
    }
    echo 2;
  }

  public function getStreamInfo( Request $request )
  {
      $stream_id = $request->stream_id;
      
      echo json_encode( Stream::getStreamById( $stream_id ));
  }

  public function deleteStream($streamId)
  {
    $stream = Stream::find($streamId);
    
    if ($stream->user_id != Auth::id()) {
      echo 0;
      return;
    }
    
    $stream->delete();
    parent::registerLog('Delete Stream ID: ' . $streamId, 3);
    
    Cache::forget('streams_' . $stream->event_id);
    Cache::flush();
    echo 1;
  }

  private function _buildGroupByUser( $allStreams )
  {
    $arrRes = array();

    foreach( $allStreams as $key => $stream )
    {
      $username = $stream->username;
      $user_id = $stream->user_id;

      if( isset( $arrRes[$username]) ){
        $arrRes[$username]['count']++;
        $arrRes[$username]['data'][$key] = $stream;
      }
      else{
        $arrRes[$username]['count'] = 1;
        $arrRes[$username]['fans'] = Cache::remember('userfans_' . $user_id, 3, function () use ($user_id) {
          return \App\User::find($user_id)->fans()->count();
        });
        $arrRes[$username]['data'][$key] = $stream;
      }
    }

    return $arrRes;
  }
}
