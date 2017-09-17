<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    protected $table = 'competitions';
    public $timestamps = false;
    public $primaryKey = 'competition_id';

    protected $guarded = [];


    public function nation()
    {
        return $this->belongsTo('App\Nation', 'nation_id');
    }
}
