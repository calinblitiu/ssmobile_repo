<?php
/**
 * Created by PhpStorm.
 * User: nainy
 * Date: 22.05.17
 * Time: 1:17
 */

namespace App\Repositories;

use App\Event;
use App\Nation;

class EventRepository
{
    /**
     * Event Model
     *
     * @var Event
     */
    protected $model;

    public function __construct(Event $event)
    {
        $this->model = $event;
    }

    public function upsert(array $event)
    {
        return $this->model->updateOrCreate(
            [
                'home_team_id' => $event['home_team_id'],
                'away_team_id' => $event['away_team_id'],
                'competition_id' => $event['competition_id'],
                'nation_id' => $event['nation_id'],
                'sport_id' => $event['sport_id']
            ],
            $event
        );
    }

    public function get(array $condition = [])
    {
        return $this->model->where($condition)->first();
    }

}