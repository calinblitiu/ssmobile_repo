<?php
/**
 * Created by PhpStorm.
 * User: 
 * Date: 
 * Time: 
 */

namespace App\Repositories;

use App\News;
use App\User;
use App\Event;
use App\Nation;

class NewsRepository
{
    /**
     * News Model
     *
     * @var News
     */
    protected $model;

    public function __construct(Event $event)
    {
        $this->model = $news;
    }

    public function upsert(array $news)
    {
        return $this->model->updateOrCreate(
            [
                'news_title' => $news['news_title']
            ],
            $news
        );
    }

    public function get(array $condition = [])
    {
        return $this->model->where($condition)->first();
    }

}