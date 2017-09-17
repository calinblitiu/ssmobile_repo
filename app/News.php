<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class News extends Model
{
  public $timestamps = false;
  public $primaryKey = 'news_id';
  
  // public function comments()
  // {
  //   return $this->hasMany('App\Comment', 'stream_id', 'stream_id');
  // }
  
  public static function getNews($eventId, $userType)
  {
    $news = DB::table('news AS n')
      ->leftJoin('users AS u', 'u.id', 'n.user_id')
      ->leftJoin('languages AS l', 'l.language_name', 'n.language')
      ->where(['n.event_id' => $eventId, 'u.verified_user' => $userType, 'u.ban' => 0])
      ->groupBy('n.news_id')
      ->orderBy('vote', 'desc')
      ->select('n.*', 'u.name AS username', 'u.verified_user','u.approved', 'l.language_flag', 'l.language_name')->get();

    return ( $news );
  }
  
  public static function getAllEventNews($eventId, $userId = 0)
  {
    $news = DB::select("SELECT 
                                    news.news_id,
                                    news.event_id,
                                    news.news_title,
                                    news.news_article,
                                    news.news_image,
                                    news.feed_url,
                                    news.language                                    
                              FROM  news 
                              WHERE event_id = $eventId                              
                              ORDER BY news_id "
      );

    return ( $news );
  }

  
}
