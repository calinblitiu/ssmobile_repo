<?php

namespace App\Http\Controllers\Admin;

use App\Competition;
use App\Nation;
use App\Sport;
use App\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Channel;
use Carbon\Carbon;
use Datatables;
use App\Http\Controllers\Controller;

class ChannelController extends Controller
{
  
  public function index()
  {
  
    $channel = new Channel;
    $allChannels = $channel->getAllChannels();
    return view('admin.channels',[
                  'allChannels'=>$allChannels]);
  }
  
  public function delete($channelId)
  {
    $channel = Event::find($channelId);
    $channel->delete();
    Cache::flush();
    echo true;
  }
  
  public function createChannel()
  {
    $nations = Nation::all();
    return view('admin.createChannel',
      ['nations' => $nations]);
  }
  
  public function storeChannel(Request $request)
  {
    $channel = new Channel;
    $channel->channel_id = $request->id;
    $channel->channel_name = $request->name." ".$request->nation;
    $channel->slug = $request->slug;
    $channel->description = $request->description;
    $channel->acquire_rights = $request->acquire_rights;
    $channel->iframe = $request->iframe;
    $channel->logo = $request->logo;
    $channel->channel_status = $request->status;
    $channel->save();
    Cache::forget('allChannels');
    Cache::flush();
    if ($request->submit == 'back') {
      return redirect('moderator/channel')->with('done','New channel added successfully');
    } else {
      return back()->with('done','New channel added successfully');
    }
  }
  

  /**
   * undocumented function
   *
   * @return void
   * @author Risul Islam - risul321@gmail.com
   **/
  public function updateChannelDate(Request $request, $channelId)
  {
    $channel = Channel::find($channelId);
    $channel->description = $request->description;
    $channel->acquire_rights = $request->acquire_rights;
    $channel->save();
    Cache::forget('allChannels');
    Cache::flush();
    return redirect('moderator/channel');
  }
}