<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Settings;
use App\User;
use App\Domain;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Channel extends Model
{
    public $timestamps = false;
    public $primaryKey = 'channel_id';

    protected $guarded = [];

    public function getAllChannels()
    { 
        $channels = DB::table('channels')->get();
        return $channels;
        
    }

   
    // public static function getEventInfo($eventId)
    // {
    //     return DB::table('events AS e')
    //         ->leftJoin('teams AS ht', 'e.home_team_id', '=', 'ht.team_id')
    //         ->leftJoin('teams AS at', 'e.away_team_id', '=', 'at.team_id')
    //         ->leftJoin('competitions AS c', 'c.competition_id', '=', 'e.competition_id')
    //         ->leftJoin('nations AS n', 'n.nation_id', '=', 'e.nation_id')
    //         ->where('e.event_id', $eventId)
    //         ->select('e.*', 'ht.team_name AS home_team', 'ht.team_slug AS home_team_slug', 'ht.team_logo AS home_team_logo', 'at.team_name AS away_team', 'at.team_slug AS away_team_slug',
    //             'at.team_logo AS away_team_logo', 'n.nation_name', 'c.competition_name', 'c.competition_logo')
    //         ->first();
    // }

}