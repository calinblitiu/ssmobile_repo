<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Favourite extends Model
{    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'item_id', 'item_name', 'item_type',
    ];

	private static $favouriteTeams = array();
	private static $favouriteComps = array();

	public static function comp($a, $b){
		$aIn = in_array($a->competition_id, self::$favouriteComps) ||
		       in_array($a->home_team_id, self::$favouriteTeams) ||
		       in_array($a->away_team_id, self::$favouriteTeams);

		$bIn = in_array($b->competition_id, self::$favouriteComps) || 
		       in_array($b->home_team_id, self::$favouriteTeams) ||
		       in_array($b->away_team_id, self::$favouriteTeams);

		if ( $aIn && $bIn || !($aIn || $bIn))
			return strtotime($a->start_date) < 
				   strtotime($b->start_date) ? -1 : 1;

		return ($aIn ? 1 : 0 > $bIn ? 1 : 0) ? -1 : 1;
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function addDivider($data)
	{
		foreach ($data as $events) {
			foreach ($events as $event) {
				if(in_array($event->competition_id, self::$favouriteComps) || 
		       		in_array($event->home_team_id, self::$favouriteTeams) ||
		       		in_array($event->away_team_id, self::$favouriteTeams)) {
		       		$event->isFav = true;
		       } else {
		       		$event->isFav = false;
		       		break;
		       }
			}			
		}

		return $data;
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function sortEventsByFavourite($events)
	{
		$favouritesTeamObj = $this->where(['user_id' => Auth::user()->id, 'item_type' => 'Team'])->pluck('item_id');
		$favouritesCompObj = $this->where(['user_id' => Auth::user()->id, 'item_type' => 'Competition'])->pluck('item_id');

		foreach ($favouritesTeamObj as $key => $value) {
			self::$favouriteTeams[] = $value;
		}

		foreach ($favouritesCompObj as $key => $value) {
			self::$favouriteComps[] = $value;
		}

		if(self::$favouriteTeams == null && self::$favouriteComps == null)
			return $events;

	    for ($i=0; $i < sizeof($events); $i++) { 
			usort($events[$i], "self::comp");
	    }
	    return self::addDivider($events);;
	}

	public static function comp2($a, $b){
		$ac = 0;
		$bc = 0;
		if(isset($a->competition_popular))
			$ac = $a->competition_popular == "Y";
		if(isset($b->competition_popular))
		$bc = $b->competition_popular == "Y";

		if($a->start_date == $b->start_date)
			return $ac ? -1 : 1;
		return $a->start_date < $b->start_date ? -1 : 1;
	}

	/**
	 * undocumented function
	 *
	 * @return void
	 * @author 
	 **/
	public function sortEventsByPopularity($events)
	{
		usort($events, "self::comp2");

	    return $events;
	}



}
