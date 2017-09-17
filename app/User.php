<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
  use Notifiable;
  
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'email', 'password',
  ];
  
  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'remember_token',
  ];
  
  public function comments()
  {
    return $this->hasMany('App\Comment', 'id', 'user_id');
  }
  
  public static function bannedUsersIds()
  {
    return Cache::remember('bannedUsers', 5, function () {
      return User::where('ban', 1)->select('id')->get()->toArray();
    });
  }

  public function fans()
  {
    return $this->belongsToMany('App\User', 'streammer_likes','streammer_id','user_id');
  }

  public function streammersLiked()
  {
    return $this->belongsToMany('App\User', 'streammer_likes','user_id','streammer_id');
  }
}
