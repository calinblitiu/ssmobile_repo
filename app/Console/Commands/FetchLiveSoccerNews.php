<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Log;

use GuzzleHttp\Psr7\Response;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException as GuzzleException;
use Carbon\Carbon;
use Intervention\Image\ImageManagerStatic as Intervention;

use App\Repositories\CompetitionRepository;
use App\Repositories\NationRepository;
use App\Repositories\EventRepository;
use App\Repositories\TeamRepository;
use App\Repositories\NewsRepository;

use App\Event;
use App\Competition;
use App\Nation;
use App\Team;
use Intervention\Image\ImageManager;
use Psr\Http\Message\ResponseInterface;



class FetchLiveSoccerNews extends Command
{
    /**
     * To proxy requests through.
     * Must be of format 1.1.1.1:1111
     *
     * @var null|string
     */
    protected $proxy = null;

    protected $feed_url = array("https://www.theguardian.com/football/rss",
       "http://www.shoot.co.uk/feed/",
       "https://www.theguardian.com/uk/sport/rss",
       "http://www.dailymail.co.uk/sport/index.rss",
       "http://www.independent.co.uk/sport/rss",
       "http://www.goal.com/en-gb/feeds/news?fmt=rss&ICID=HP",
       "http://www.espnfc.com/rss",
       "https://sports.yahoo.com/top/rss.xml",
       "http://feeds.skynews.com/feeds/rss/sports.xml",
       "http://www.bbc.com/sport/rss.xml",
       "http://www.telegraph.co.uk/football/rss.xml",
       "http://www.foot01.com/news.rss",
       "http://www.mirror.co.uk/sport/football/rss.xml",
       "http://rss.acast.com/forzaitlianfootball",
       "https://www.thesun.co.uk/sport/feed/",
       "http://www.cbc.ca/cmlink/rss-sports",
       "http://sport24.lefigaro.fr/rssfeeds/sport24-football.xml",
       "https://www.si.com/rss/si_soccer.rss",
       "http://www.hitc.com/en-gb/sport/rss.xml",       
       "http://feeds.abcnews.com/abcnews/sportsheadlines",
       "http://www.esquire.com/rss/sports.xml",
       "http://bleacherreport.com/articles/feed",
       "https://www.lequipe.fr/rss/videos_rss.xml",
       "http://thebiglead.com/feed/",
       "https://theathletic.com/feed/",
       "https://www.lequipe.fr/rss/actu_rss_Football.xml",
       "https://www.lequipe.fr/rss/ligue1_video.xml",
       "http://www.thesportreview.com/tsr/sports/football/feed/",
       "http://www.elnuevodiario.com.ni/rss/",
       "http://forum.foot-national.com/rss/news/",
       "http://www.football-italia.net/rss.xml",
       "http://www.fifa.com/rss/index.xml",
       "http://www.uefa.com/rssfeed/news/rss.xml"
       );


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "fetch:livesoccernews";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches data from livesoccer private API.';

    /**
     * @var Guzzle
     */
    protected $client;

    /**
     * LiveSoccer secret api token
     * @var
     */
    protected $apiToken;

    /**
     * @var CompetitionRepository
     */
    protected $competitionRepository;

    /**
     * @var NationRepository
     */
    protected $nationRepository;

    /**
     * @var TeamRepository
     */
    protected $teamRepository;

    /**
     * @var EventRepository
     */
    protected $eventRepository;

    /**
     * @var NewsRepository
     */
    protected $newsRepository;

    /**
     * Cached competitions
     *
     * @var array
     */
    protected $competitions = [];

    /**
     * Cached nations
     *
     * @var array
     */
    protected $nations = [];

    /**
     * Cached competitions
     *
     * @var array
     */
    protected $teams = [];

    /**
     * Competitions allowed to be saved
     *
     * @var array
     */
    protected $allowed = [];

    /**
     * Git client
     *
     * @var \Git
     */
    protected $gitClient;

    /**
     * Image Manager
     *
     * @var ImageManager
     */
    protected $imageManager;

    /**
     * FetchLiveSoccer constructor.
     *
     * @param Guzzle $guzzle
     * @param CompetitionRepository $competitionRepository
     * @param NationRepository $nationRepository
     * @param TeamRepository $teamRepository
     * @param EventRepository $eventRepository
     * @throws \Exception
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Creates a hashmap from the array of allowed competitions.
     */
    protected function parseAllowedCompetitionsHash()
    {
    }

    /**
     * Generates a secret API token for Livesoccer API.
     *
     * @return string
     * @throws \Exception
     */
    protected function getApiToken()
    {
        
    }


    protected function getProxy()
    {
       
    }

    /**
     * Execute the console command.
     *
     * @throws \Exception
     * @return mixed
     */
    public function handle()
    {
        
        $url_count = count($this->feed_url);

               
        $events = DB::select("SELECT 
                                    events.event_id,
                                    teams.team_name                                  
                              FROM  events 
                              LEFT JOIN teams
                              ON teams.team_id = events.home_team_id or teams.team_id = events.away_team_id
                              WHERE start_date BETWEEN CURDATE() AND CURDATE() + INTERVAL 1 DAY
                              ORDER BY event_id ");

        
        $news = DB::select("SELECT 
                                    news.news_id,
                                    news.news_title                                    
                              FROM  news 
                              WHERE added_time BETWEEN CURDATE() - INTERVAL 1 DAY AND CURDATE() + INTERVAL 1 DAY                             
                              ORDER BY news_id ");
        
        
        for ($i=0; $i<$url_count; $i++){

            $arrEventTitles = array();
            foreach ($news as $tmp) {
                $arrEventTitles[] = $tmp->news_title.'';
            }
            

            foreach ($events as $event) 
            {
                 
                $all_news = simplexml_load_file($this->feed_url[$i]) or die("Error: Cannot create object");
                foreach ($all_news->channel[0]->item as $record)
                {
                    $lang = $all_news->channel[0]->language;

                    $search_title = mb_strtolower($record->title, 'UTF-8');
                    $search_descrption = mb_strtolower($record->description, 'UTF-8');
                    $find_team_name = mb_strtolower($event->team_name, 'UTF-8');

                    if (strpos($search_title, $find_team_name) || strpos($search_descrption, $find_team_name))
                    {

                        $ns_media = $record->children('http://search.yahoo.com/mrss/');

                        $title      = $record->title;
                        $link       = $record->link;
                        $descr      = $record->description;
                        
                        // displays "<media:content>"
                        if ($ns_media->content) 
                        {
                            $image_url  = $ns_media->content->attributes()['url'];
                        }
                        elseif($ns_media->thumbnail)
                        {
                            $image_url  = $ns_media->thumbnail->attributes()['url'];
                        }    
                        elseif($record->enclosure)  
                        {
                            $image_url = $record->enclosure->attributes()['url'];
                        }
                        elseif ($record->image) {
                            $image_url = $record->image;
                        }
                        else
                        {
                            $image_url = null;
                        }
                        
                        $duplicateflag = false;
                        foreach ($arrEventTitles as $tmptile) {
                            if($title == $tmptile) {
                                $duplicateflag = true;
                                break;
                            }
                        }
                        if ($duplicateflag == false)
                        {
                            DB::table('news')->insert(
                                array(  'event_id'=> $event->event_id,
                                        'feed_url'=> $link,
                                        'language'=> $lang,
                                        'news_title'=>$title,
                                        'news_article'=>$descr,
                                        'news_image'=>$image_url)
                            );
                            $arrEventTitles[] = $title.'';
                        }
                       
                    }
                    else
                    {
                        continue;
                    }
                }     
                
            }    
        }
    }

    /**
     * Creates or updates an news
     *
     * @param array $news
     * @return news|boolean
     */
    protected function insertEventNews(array $news)
    {
        
    }

    /**
     * Rewrites the matchweek into HR format
     *
     * @param array $eventFields
     * @param array $matchweek
     * @return array eventFields
     */
    protected function parseMatchWeek(array $eventFields, $matchweek)
    {
        
    }

    /**
     * Creates or returns an existing Nation
     *
     * @param array $match
     * @return Nation
     */
    protected function firstOrCreateNation(array $match)
    {
        
    }

    /**
     * Creates or returns an existing competition.
     *
     * @param array $match
     * @param Nation $nation
     * @return Competition
     */
    protected function firstOrCreateCompetition(array $match, Nation $nation)
    {
        
    }


    /**
     * Creates or returns an existing team.
     *
     * @param array $team
     * @param Nation $nation
     * @return Team
     */
    protected function firstOrCreateTeam(array $team, $nation)
    {
        
    }

    

    /**
     * Fetches the country of the away team
     *
     * @param $matchUrl
     * @paran $teamNumber
     * @return mixed
     */
    protected function getAllChannels($matchUrl)
    {
        
    }

    /**
     * Fetches the country of the away team
     *
     * @param $matchUrl
     * @paran $teamNumber
     * @return mixed
     */
    protected function fetchTeamCountries($matchUrl)
    {

    }

    /**
     * Saves image if it doesn't exist
     *
     * @param $url
     * @param $path
     * @param $resize [width, height]
     * @return boolean
     */
    protected function saveImage($url, $path, $resize = [])
    {
       
    }

    /**
     * Helper method that makes a request and returns a decoded JSON body.
     *
     * @param $url
     * @param array $query
     * @return mixed
     */
    protected function makeRequest($url, $query = [])
    {
        
    }

    protected function decodeResponse(ResponseInterface $response)
    {
        
    }


    /**
     * Fetches the matches from livesoccer api
     *
     * @param array $query
     * @return mixed
     */
    protected function fetchMatches($query = [])
    {
       
    }


}
