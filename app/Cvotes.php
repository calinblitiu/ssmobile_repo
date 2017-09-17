<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cvotes extends Model
{
  public $table = 'comment_votes';
  
  public function comment()
  {
    return $this->belongsTo('App\Comment', 'comment_id', 'id');
  }
  
}
