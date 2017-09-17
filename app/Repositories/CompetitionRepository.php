<?php

namespace App\Repositories;

use App\Competition;
use App\Nation;

/**
 * Created by PhpStorm.
 * User: nainy
 * Date: 21.05.17
 * Time: 22:09
 */
class CompetitionRepository
{

    /**
     * Competition Model
     *
     * @var Competition
     */
    protected $model;

    public function __construct(Competition $competition)
    {
        $this->model = $competition;
    }

    public function addAndReturnLiveSoccerCompetitions($rawCompetitions, $nationsInDatabase = [])
    {
        $competitionsInDatabaseQuery = array_reduce(
            $rawCompetitions,
            function ($query, $rawCompetition) use ($nationsInDatabase) {
                $nation = $nationsInDatabase[$rawCompetition['country']];

                $query->andWhere(
                    [
                        'competition_name' => $rawCompetition['competition'],
                        'nation_id' => $nation->id
                    ]
                );

                return $query;
            },
            Competition::query()->select('competition_id', 'competition_name', 'competition_slug')
        );

        $competitionsInDatabase = $competitionsInDatabaseQuery->get()->keyBy('competition_name');

        foreach ($rawCompetitions as $rawCompetition) {
            if (isset($competitionsInDatabase[$rawCompetition['competition']])) continue;

            $nation = $nationsInDatabase[$rawCompetition['country']];
            $key = str_slug($rawCompetition['competition'] . '_' . $nation->nation_name);

            $competition = new Competition(
                [
                    'competition_name' => $rawCompetition['name'],
                    'competition_slug' => $rawCompetition['slug'],
                    'nation_id' => $nation->id
                ]
            );

            $competition->save();

            $competitionsInDatabase[$key] = $competition;
        }

        return $competitionsInDatabase;

    }

    public function upsert(array $competition)
    {
        return $this->model->updateOrCreate(
            [
                'competition_name' => $competition['competition_name'],
                'nation_id' => $competition['nation_id']
            ],
            $competition
        );
    }

    public function firstOrCreate(array $competition)
    {
        return $this->model->firstOrCreate(
            [
                'competition_name' => $competition['competition_name'],
                'nation_id' => $competition['nation_id']
            ],
            $competition
        );
    }

}