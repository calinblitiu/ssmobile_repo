<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nation extends Model
{
    protected $table = 'nations';
    public $timestamps = false;
    public $primaryKey = 'nation_id';

    protected $guarded = [];


    public function competitions()
    {
        return $this->hasMany('App\Competition', 'nation_id', 'nation_id');
    }

    public function teams()
    {
        return $this->hasMany('App\Team', 'nation_id', 'nation_id');
    }
}
