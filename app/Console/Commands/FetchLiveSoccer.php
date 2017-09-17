<?php

namespace App\Console\Commands;

use GuzzleHttp\Psr7\Response;
use Illuminate\Console\Command;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException as GuzzleException;
use Carbon\Carbon;
use Intervention\Image\ImageManagerStatic as Intervention;

use App\Repositories\CompetitionRepository;
use App\Repositories\NationRepository;
use App\Repositories\EventRepository;
use App\Repositories\TeamRepository;

use App\Event;
use App\Competition;
use App\Nation;
use App\Team;
use Intervention\Image\ImageManager;
use Psr\Http\Message\ResponseInterface;

class FetchLiveSoccer extends Command
{
    /**
     * To proxy requests through.
     * Must be of format 1.1.1.1:1111
     *
     * @var null|string
     */
    protected $proxy = null;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "fetch:livesoccer {--no-proxy} {--no-commit} {--update-images}";

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
    public function __construct(
        Guzzle $guzzle,
        CompetitionRepository $competitionRepository,
        NationRepository $nationRepository,
        TeamRepository $teamRepository,
        EventRepository $eventRepository
    )
    {
        parent::__construct();

        $this->client = $guzzle;
        $this->competitionRepository = $competitionRepository;
        $this->nationRepository = $nationRepository;
        $this->teamRepository = $teamRepository;
        $this->eventRepository = $eventRepository;

        require __DIR__ . '/../Git.php';

        $this->gitClient = \Git::open(base_path());

        $this->parseAllowedCompetitionsHash();
    }

    /**
     * Creates a hashmap from the array of allowed competitions.
     */
    protected function parseAllowedCompetitionsHash()
    {
        $competitions = require __DIR__ . '/Competitions.php';

        foreach ($competitions as $competition) {
            $key = str_slug($competition['nation_name'] . '-' . $competition['competition_name']);

            $this->allowed[$key] = true;
        }
    }

    /**
     * Generates a secret API token for Livesoccer API.
     *
     * @return string
     * @throws \Exception
     */
    protected function getApiToken()
    {
        try {
            // make empty request withotu api key to trigger error
            $this->makeRequest('http://api.livesoccertv.com');
        } catch (GuzzleException $e) {
            // get response body
            $response = $this->decodeResponse($e->getResponse());

            if (!isset($response['secret']) || !$response['secret']) {
                throw new \Exception('No secret key returned by the LiveSoccer API. Cannot create API token.');
            }

            // hash the secret phrase
            $hash = hash('md5', "some soccer" . $response['secret'] . "_ salt");

            // pad with zeroes if hash length is less than 32
            while (strlen($hash) < 32) {
                $hash = "0" . $hash;
            }

            return $hash;
        }
    }


    protected function getProxy()
    {
        $proxyList = [
            'http://siolio:34223422@104.206.119.68:55555',
            'http://siolio:34223422@154.16.85.97:55555',
            'http://siolio:34223422@107.150.80.174:55555',
            'http://siolio:34223422@107.150.80.182:55555',
            'http://siolio:34223422@155.94.132.189:55555',
            'http://siolio:34223422@107.150.80.63:55555',
            'http://siolio:34223422@104.206.119.56:55555',
            'http://siolio:34223422@154.16.87.24:55555',
            'http://siolio:34223422@107.150.80.169:55555',
            'http://siolio:34223422@104.206.119.70:55555',
        ];


        return $proxyList[array_rand($proxyList)];
    }

    /**
     * Execute the console command.
     *
     * @throws \Exception
     * @return mixed
     */
    public function handle()
    {
        // create an image manager
        Intervention::configure(['driver' => 'imagick']);

        // if proxy isn't disabled, getting a random proxy and recreating the client
        if (!$this->option('no-proxy')) {
            $this->proxy = $this->getProxy();

            $this->client = new Guzzle(['proxy' => $this->proxy]);
        }

        // set the token
        $this->apiToken = $this->getApiToken();

        if (!$this->apiToken) {
            throw new \Exception('No Api Token provided.');
        }

        // Get today's timestamp to fetch today's competitions
        $timestamp = mktime(0, 0, 0);

        // we'll store all request data here
        $matches = [];

        // first, we'll fetch all matches for today + next 7 days
        for ($i = 0; $i < 7; $i++) {
            $data = $this->fetchMatches(['iso_code' => 'us', 'timestamp' => $timestamp]);

            $matches = array_merge(
                $matches, $data['competitions']
            );

            $timestamp += 86400; // add one day
        }

        // then we'll process the matches
        foreach ($matches as $match) {
            $key = str_slug($match['country'] . '-' . $match['competition']);

            // if not in list, we'll continue
            if (!isset($this->allowed[$key])) {
                $this->info("Competition {$match['competition']} in ${match['country']} is not in allowed list.");
                continue;
            }

            try {
                // do it all in a transaction
                \DB::beginTransaction();

                // hack to make the continent replace all international cups
                if ($match['country'] === 'International') {
                    $match['country'] = $match['continent'];
                }

                // upsert and return a nation from db
                $nation = $this->firstOrCreateNation($match);

                $this->info("Got nation {$nation->nation_name}");

                // then, finally let's upsert and return a competition
                $competition = $this->firstOrCreateCompetition($match, $nation);

                $this->info("Got competition {$competition->competition_name}");

                // after that, we'll process the events
                foreach ($match['fixtures'] as $event) {
                    // can't save teams without names
                    if (!$event['team1_name'] || !$event['team2_name']) {
                        $this->info("Unnamed teams. Can't save.");

                        continue;
                    }

                    // can't save teams that have no ids -- these are placeholders
                    if ($event['team1_id'] === "0" || !$event['team2_id'] === "0") {
                        $this->info("Empty team ids. Can't save.");

                        continue;
                    }

                    // we'll fetch the countries for both teams from the match url
                    $countries = $this->fetchTeamCountries($event['url']);

                    //if home or away teams don't have countries, we'll use competition's continent
                    if (!$countries['homeTeamCountry']) {
                        $this->info("Home team {$event['team1_name']} has no country. Using continent: {$match['continent']}");

                        $countries['homeTeamCountry'] = $match['continent'];
                    }

                    if (!$countries['awayTeamCountry']) {
                        $this->info("Away team {$event['team2_name']} has no country. Using continent: {$match['continent']}");

                        $countries['awayTeamCountry'] = $match['continent'];
                    }

                    $homeTeamNation = $this->firstOrCreateNation([
                        'country' => $countries['homeTeamCountry'],
                        'flag' => $match['flag']
                    ]);

                    $awayTeamNation = $this->firstOrCreateNation([
                        'country' => $countries['awayTeamCountry'],
                        'flag' => $match['flag']
                    ]);

                    $homeTeam = $this->firstOrCreateTeam([
                        'team_name' => $event['team1_name'],
                        'team_logo' => $event['team1_logo'],
                    ], $homeTeamNation);

                    $this->info("Got home team {$homeTeam->team_name}.");

                    $awayTeam = $this->firstOrCreateTeam([
                        'team_name' => $event['team2_name'],
                        'team_logo' => $event['team2_logo'],
                    ], $awayTeamNation);

                    $eventChannels = $this->getAllChannels($event['url']);

                    $this->info("Got away team {$awayTeam->team_name}.");

                    $eventFields = [
                        'competition_id' => $competition->competition_id,
                        'nation_id' => $nation->nation_id,
                        'sport_id' => 1,
                        'channels' => $eventChannels['channels'] ? json_encode($eventChannels['channels']) : null,
                        'venue' => mb_strlen($event['venue']) ? $event['venue'] : null,
                        'home_team_id' => $homeTeam->team_id,
                        'away_team_id' => $awayTeam->team_id,
                        'event_status' => $event['team1_result'] . ':' . $event['team2_result'],
                        'event_minute' => $event['status'] ? $event['status'] : null,
                        'game_week' => null,
                        'round_name' => null,
                        'start_date' => Carbon::createFromTimestamp($event['timestamp']),
                        'end_date' => Carbon::createFromTimestamp($event['timestamp'] + 9000), // + 150 minutes
                        'external_key' => $event['url']
                    ];

                    $eventFields = $this->parseMatchWeek($eventFields, $event['matchweek']);

                    // then we'll create the event
                    $eventModel = $this->createOrUpdateEvent($eventFields);

                    if (!$eventModel) {
                        $this->info('Event was a duplicate. Skipping it.');

                        continue;
                    }

                    $this->info("Got event {$event['game']}");
                }
            } catch (\Exception $e) {
                $this->error("Error! {$e->getMessage()}");

                \DB::rollBack();

                continue;
            }

            \DB::commit();
        }

        $this->info('Successfully pulled the data from LiveSoccer API!');

        // we'll commit the images to master, unless otherwise specified
        if (!$this->option('no-commit')) {
            $this->info('Now committing the images...');

            // add all the images to staging
            $this->gitClient->add('public/images/flags');
            $this->gitClient->add('public/images/teams');
            $this->gitClient->add('public/images/teams/small');

            try {
                $this->gitClient->commit('fetch:livesoccer autocommit images.');
                $this->gitClient->push('origin', 'master');

                $this->info('Images committed successfully.');
            } catch (\Exception $e) {
                $this->error("Probably nothing to commit, but here is the error: {$e->getMessage()}");
            }
        }
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
        $mw = $matchweek;

        if (is_numeric($mw) && $mw > 0) {
            $eventFields['game_week'] = $mw;

            return $eventFields;
        }

        switch ($mw) {
            case 'final':
                $mw = 'Final';
                break;
            case '1/4':
                $mw = 'Quarter Finals';
                break;
            case '1/2':
                $mw = 'Semi Finals';
                break;
            case '1/8':
            case '1/16':
            case '1/32':
                $mw .= ' Finals';
                break;
            default:
                break;
        }

        $eventFields['round_name'] = $mw ? $mw : null;

        return $eventFields;
    }

    /**
     * Creates or returns an existing Nation
     *
     * @param array $match
     * @return Nation
     */
    protected function firstOrCreateNation(array $match)
    {
        $key = str_slug($match['country']);

        if (!isset($this->nations[$key])) {
            $slug = $key;
            $flagFileName = $slug . '.png';

            $isSaved = $this->saveImage(strtok($match['flag'], '?'), '/images/flags/' . $flagFileName);

            if (!$isSaved) {
                $flagFileName = 'generic.png'; // fallback
            }

            $this->nations[$key] = $this->nationRepository->firstOrCreate(
                [
                    'nation_name' => $match['country'],
                    'nation_slug' => $key,
                    'nation_flag' => $flagFileName
                ]
            );
        }

        return $this->nations[$key];
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
        $key = str_slug($nation->nation_name . '-' . $match['competition']);

        if (!isset($this->competitions[$key])) {
            $logoFileName = 'generic.png'; // fallback

            $this->competitions[$key] = $this->competitionRepository->firstOrCreate(
                [
                    'competition_name' => $match['competition'],
                    'competition_slug' => $match['slug'],
                    'competition_logo' => $logoFileName,
                    'commonname' => $match['competition'],
                    'popular' => $match['popular'],
                    'sport_id' => 1,
                    'nation_id' => $nation->nation_id
                ]
            );
        }

        return $this->competitions[$key];
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
        $slug = str_slug($nation->nation_name . '-' . $team['team_name']);
        $logoFileName = $slug . '.png';

        $url = strtok($team['team_logo'], '?');

        // save the small as 30x30
        $this->saveImage($url, '/images/teams/small/' . $logoFileName, [30, 30]);

        // save the original as 150x150
        $isSaved = $this->saveImage($url, '/images/teams/' . $logoFileName, [150, 150]);

        if (!$isSaved) {
            $logoFileName = 'generic.png'; // fallback
        }

        $teamUrlSlug = str_slug($team['team_name']);

        return $this->teamRepository->firstOrCreate(
            [
                'team_name' => $team['team_name'],
                'team_logo' => $logoFileName,
                'team_slug' => $slug,
                'external_key' => "/teams/{$nation->nation_slug}/{$teamUrlSlug}",
                'sport_id' => 1,
                'nation_id' => $nation->nation_id
            ]
        );
    }

    /**
     * Creates or updates an event
     *
     * @param array $event
     * @return Event|boolean
     */
    protected function createOrUpdateEvent(array $event)
    {
        $existingEvent = $this->eventRepository->get([
            'competition_id' => $event['competition_id'],
            'nation_id' => $event['nation_id'],
            'sport_id' => $event['sport_id'],
            'start_date' => $event['start_date']
        ]);

        // if an event with the same start_date exists
        if ($existingEvent) {
            // and one of the teams matches with the new event
            // it's a duplicate, skipping
            if ($existingEvent->home_team_id == $event['home_team_id'] && $existingEvent->away_team_id != $event['away_team_id']) {
                return false;
            }

            if ($existingEvent->home_team_id != $event['home_team_id'] && $existingEvent->away_team_id == $event['away_team_id']) {
                return false;
            }
        }

        return $this->eventRepository->upsert($event);
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
        $match = $this->makeRequest("http://api.livesoccertv.com{$matchUrl}");

        return [
            'channels' => $match['fixture']['all_channels']
        ];
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
        $match = $this->makeRequest("http://api.livesoccertv.com{$matchUrl}");

        return [
            'homeTeamCountry' => $match['fixture']['team1_country'],
            'awayTeamCountry' => $match['fixture']['team2_country']
        ];
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
        if (!$url) {
            return false;
        }

        $fullPath = public_path() . $path;

        if ($this->option('update-images') || !file_exists($fullPath)) {
            $originalTail = 'https://cdn.livesoccertv.com/';
            $resizeTail = 'https://cdn.livesoccertv.com/tt/';

            // remove the resize url from the link
            if (0 === strpos($url, $resizeTail)) {
                $url = str_replace_first($resizeTail, $originalTail, $url);
            }

            $image = Intervention::make($url);

            if ($resize) {
                // create transparent bg
                $background = Intervention::canvas($resize[0], $resize[1]);

                // resize maintaining aspect ration and preventing upsizing
                $image->resize($resize[0], $resize[1], function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });

                // insert the image onto the bg
                $background->insert($image, 'center');

                $image = $background;
            }

            $image
                ->encode('png', 90)
                ->save($fullPath);
        }

        return true;
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
        $result = $this->client->get($url, [
            'headers' => [
                'X-Api-Token' => $this->apiToken
            ],
            'query' => $query
        ]);

        return $this->decodeResponse($result);
    }

    protected function decodeResponse(ResponseInterface $response)
    {
        return json_decode(
            preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response->getBody()->getContents()),
            true
        );
    }


    /**
     * Fetches the matches from livesoccer api
     *
     * @param array $query
     * @return mixed
     */
    protected function fetchMatches($query = [])
    {
        return $this->makeRequest('http://api.livesoccertv.com/matches', $query);
    }


}
