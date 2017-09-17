<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
  protected $table = 'activity_log';
  public $timestamps = false;
  public $primaryKey = 'id';
  
  public function actorName()
  {
    return $this->belongsTo('App\User','actor','id');
  }
}
