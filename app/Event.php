<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Settings;
use App\User;
use App\Domain;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Event extends Model
{
    public $timestamps = false;
    public $primaryKey = 'event_id';

    protected $guarded = [];


    public function sport()
    {
        return $this->belongsTo('App\Sport', 'sport_id', 'sport_id')->select(['sport_name']);
    }

    public function homeTeam()
    {
        return $this->belongsTo('App\Team', 'home_team_id', 'team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo('App\Team', 'away_team_id', 'team_id');
    }

    public function competition()
    {
        return $this->belongsTo('App\Competition', 'competition_id', 'competition_id');
    }

    public function getEventsByInterval()
    {
        $interval = Cache::remember('events_duration', 25, function () {
            return Settings::where(['setting_key' => 'events_duration'])->first()->setting_value;
        });

        $banned_users = User::bannedUsersIds();
        if (count($banned_users) > 0) {
            $users = [];
            foreach ($banned_users as $key => $value) {
                $users[] = $value['id'];
            }
            $banned_users = array_values($users);
        }

        $today = Carbon::now();
        $duration = Carbon::now()->addHours($interval);

        $domains = Cache::remember('bannedDomains', 10, function () {
            return Domain::all();
        });

        return Cache::remember('allEvents', 1, function () use ($banned_users, $domains, $today, $duration) {
            return DB::table('events AS e')
                ->leftJoin('teams AS ht', 'e.home_team_id', '=', 'ht.team_id')
                ->leftJoin('teams AS at', 'e.away_team_id', '=', 'at.team_id')
                ->leftJoin('comments AS co', 'co.event_id', '=', 'e.event_id')
                ->leftJoin('streams AS s', function ($join) use ($banned_users, $domains) {
                    $join->on('s.event_id', '=', 'e.event_id')->where('s.approved', 1);
                    if (count($banned_users) > 0) {
                        $join->whereNotIn('s.user_id', $banned_users);
                    }
                    foreach ($domains as $domain) {
                        if ($domain->ban == 1)
                            $join->where('s.url', 'not like', $domain->domain);
                    }
                })
                ->leftJoin('competitions AS c', 'c.competition_id', '=', 'e.competition_id')
                ->leftJoin('event_details AS ed', 'ed.event_id', '=', 'e.event_id')
                ->leftJoin('nations AS n', 'n.nation_id', '=', 'c.nation_id')
                ->where('e.end_date', '>=', $today)
                ->where('e.start_date', '<=', $duration)
                ->where('e.channels', '!=', null)
                ->where('e.sport_id', 1)
                ->orderBy('e.start_date', 'ASC')
                ->select('e.start_date', 'e.end_date', 'e.event_title', 'e.channels', 'n.nation_name', 'c.competition_name AS competition_name', 'c.competition_logo',
                    'ht.team_name AS home_team', 'ht.team_logo AS home_team_logo', 'ht.team_slug AS home_team_slug', 'e.event_status',
                    'at.team_name AS away_team', 'at.team_logo AS away_team_logo', 'at.team_slug AS away_team_slug', 'e.event_id', 'e.event_minute', DB::raw('count(DISTINCT co.id) as comments'), DB::raw('count(DISTINCT s.stream_id) as streams'))->groupBy('e.event_id')->get();
        });
    }

    public function getEventsByInterval2()
    {
        $interval = Cache::remember('events_duration', 25, function () {
            return Settings::where(['setting_key' => 'events_duration'])->first()->setting_value;
        });

        /*$banned_users = User::bannedUsersIds();
        if (count($banned_users) > 0) {
            $users = [];
            foreach ($banned_users as $key => $value) {
                $users[] = $value['id'];
            }
            $banned_users = array_values($users);
        }*/

        $today = Carbon::now();
        $duration = Carbon::now()->addHours($interval);

        $domains = Cache::remember('bannedDomains', 10, function () {
            return Domain::all();
        });

        return Cache::remember('allEvents', 3, function () use ($domains, $today, $duration) {
            return DB::table('events AS e')
                ->leftJoin('teams AS ht', 'e.home_team_id', '=', 'ht.team_id')
                ->leftJoin('teams AS at', 'e.away_team_id', '=', 'at.team_id')
                ->leftJoin('comments AS co', 'co.event_id', '=', 'e.event_id')
                ->leftJoin('streams AS s', function ($join) use ($domains) {
                    $join->on('s.event_id', '=', 'e.event_id')->where('s.approved', 1);
                   /* if (count($banned_users) > 0) {
                        $join->whereNotIn('s.user_id', $banned_users);
                    }*/
                    foreach ($domains as $domain) {
                        if ($domain->ban == 1)
                            $join->where('s.url', 'not like', $domain->domain);
                    }
                })
                ->leftJoin('users AS u',function($join){
                  $join->on('u.id','=','s.user_id')->where('u.ban','<>',0);
                })
                ->leftJoin('competitions AS c', 'c.competition_id', '=', 'e.competition_id')
                ->leftJoin('event_details AS ed', 'ed.event_id', '=', 'e.event_id')
                ->leftJoin('nations AS n', 'n.nation_id', '=', 'c.nation_id')
                //->where('e.end_date', '>=', $today)
                ->where('e.start_date', '<=', $duration)
                ->where('e.channels', '!=', null)
                ->where('e.sport_id', 1)
                ->orderBy('e.start_date', 'ASC')
                ->select('e.start_date', 'e.end_date', 'e.event_title', 'e.competition_id', 'e.home_team_id', 'e.away_team_id', 'e.channels', 'n.nation_name', 'c.competition_name AS competition_name', 'c.competition_logo', 'c.popular AS competition_popular',
                    'ht.team_name AS home_team', 'ht.team_logo AS home_team_logo', 'ht.team_slug AS home_team_slug', 'e.event_status',
                    'at.team_name AS away_team', 'at.team_logo AS away_team_logo', 'at.team_slug AS away_team_slug', 'e.event_id', 'e.event_minute', DB::raw('count(DISTINCT co.id) as comments'), DB::raw('count(DISTINCT s.stream_id) as streams'))->groupBy('e.event_id')->get();
        });
    }

    public static function getEventInfo($eventId)
    {
        return DB::table('events AS e')
            ->leftJoin('teams AS ht', 'e.home_team_id', '=', 'ht.team_id')
            ->leftJoin('teams AS at', 'e.away_team_id', '=', 'at.team_id')
            ->leftJoin('competitions AS c', 'c.competition_id', '=', 'e.competition_id')
            ->leftJoin('nations AS n', 'n.nation_id', '=', 'e.nation_id')
            ->where('e.event_id', $eventId)
            ->select('e.*', 'ht.team_name AS home_team', 'ht.team_slug AS home_team_slug', 'ht.team_logo AS home_team_logo', 'at.team_name AS away_team', 'at.team_slug AS away_team_slug',
                'at.team_logo AS away_team_logo', 'n.nation_name', 'c.competition_name', 'c.competition_logo')
            ->first();
    }

}