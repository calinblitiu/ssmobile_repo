<?php
/**
 * Created by PhpStorm.
 * User: nainy
 * Date: 22.05.17
 * Time: 1:17
 */

namespace App\Repositories;

use App\Nation;
use App\Team;

class TeamRepository
{
    /**
     * Team Model
     *
     * @var Team
     */
    protected $model;

    public function __construct(Team $team)
    {
        $this->model = $team;
    }

    public function upsert(array $team)
    {
        return $this->model->updateOrCreate(
            [
                'team_name' => $team['team_name'],
                'nation_id' => $team['nation_id']
            ],
            $team
        );
    }

    public function firstOrCreate(array $team)
    {
        return $this->model->firstOrCreate(
            [
                'team_name' => $team['team_name'],
                'nation_id' => $team['nation_id']
            ],
            $team
        );
    }

}