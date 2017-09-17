<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public $timestamps = false;
    public $primaryKey = 'team_id';
    protected $table = 'teams';

    protected $guarded = [];


    public function nation()
    {
        return $this->belongsTo('App\Nation', 'nation_id');
    }

}
