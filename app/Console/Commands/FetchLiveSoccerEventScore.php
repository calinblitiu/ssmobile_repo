<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException as GuzzleException;
use Carbon\Carbon;

use App\Repositories\EventRepository;
use App\Event;
use App\Competition;
use App\Nation;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Support\Facades\Cache;


class FetchLiveSoccerEventScore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:livesoccereventscore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches live events score data from livesoccer private API.';

    /**
     * To proxy requests through.
     * Must be of format 1.1.1.1:1111
     *
     * @var null|string
     */
    protected $proxy = null;

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
     * @var EventRepository
     */
    protected $eventRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Guzzle $guzzle, EventRepository $eventRepository)
    {
        parent::__construct();

        $this->client = $guzzle;
        $this->eventRepository = $eventRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // set the token
        $this->apiToken = $this->getApiToken();

        if (!$this->apiToken) {
            throw new \Exception('No Api Token provided.');
        }

        // Get today's timestamp to fetch today's competitions
        $timestamp1 = time() - (24*60*60);
        $timestamp2 = time();
        $timestamp3 = time() + (24*60*60);

        $matches = [];

        $data = $this->fetchMatches(['iso_code' => 'us', 'timestamp' => $timestamp1])['competitions'];

        $matches = array_merge(
            $matches, $data
        );

        $data = $this->fetchMatches(['iso_code' => 'us', 'timestamp' => $timestamp2])['competitions'];

        $matches = array_merge(
            $matches, $data
        );

        $data = $this->fetchMatches(['iso_code' => 'us', 'timestamp' => $timestamp3])['competitions'];

        $matches = array_merge(
            $matches, $data
        );

        foreach ($matches as $match) {
            try {
                // do it all in a transaction
                \DB::beginTransaction();
                foreach ($match['fixtures'] as $event) {
                    if($event['status'] == null)
                        continue;
                    // $this->info("{$event['team1_name']} - {$event['status']}");
                    $eventToUpdate = Event::where('external_key', $event['url'])->first();
                    if($eventToUpdate) {
                        $eventToUpdate->event_status = $event['team1_result'] . ' : ' . $event['team2_result'];
                        $eventToUpdate->event_minute = $event['status'];
                        $eventToUpdate->save();

                        Cache::store(env('CACHE_DRIVER'))->put( "{$eventToUpdate->event_id}._minute", $eventToUpdate->event_minute, 180);
                        Cache::store(env('CACHE_DRIVER'))->put( "{$eventToUpdate->event_id}._status", $eventToUpdate->event_status, 180);
                    }
                }
            } catch (\Exception $e) {
                $this->error("Error! {$e->getMessage()}");

                \DB::rollBack();

                continue;
            }

            \DB::commit();
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
