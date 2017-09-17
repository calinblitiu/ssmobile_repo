<?php

namespace App\Repositories;

use App\Nation;

/**
 * Created by PhpStorm.
 * User: nainy
 * Date: 21.05.17
 * Time: 22:09
 */
class NationRepository
{

    /**
     * Nation Model
     *
     * @var Nation
     */
    protected $model;

    public function __construct(Nation $nation)
    {
        $this->model = $nation;
    }

    public function addAndReturnLiveSoccerNations($nationNames = [])
    {
        $nationsInDatabase = $this->model->query()
            ->whereIn('nation_name', $nationNames)
            ->select('nation_id', 'nation_name')
            ->get()
            ->keyBy('nation_name');

        foreach ($nationNames as $nationName) {
            if (isset($nationsInDatabase[$nationName])) continue;

            $nation = new Nation(
                [
                    'nation_name' => $nationName,
                    'nation_slug' => str_slug($nationName)
                ]
            );

            $nation->save();

            $nationsInDatabase[$nation->nation_name] = $nation;
        }

        return $nationsInDatabase;
    }


    public function upsert(array $nation)
    {
        return $this->model->updateOrCreate(
            ['nation_name' => $nation['nation_name']],
            [
                'nation_name' => $nation['nation_name'],
                'nation_slug' => $nation['nation_slug']
            ]
        );
    }

    public function firstOrCreate(array $nation)
    {
        return $this->model->firstOrCreate(
            ['nation_name' => $nation['nation_name']],
            $nation
        );
    }
}