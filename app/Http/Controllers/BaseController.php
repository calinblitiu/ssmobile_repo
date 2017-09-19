<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use View;
use Response;
use App;
use Auth;
use \App\User;
use App\Event;
use Carbon\Carbon;


class BaseController extends Controller
{
    public $timeZone = [
        "-12" => "(GMT -12:00) Eniwetok, Kwajalein",
        "-11" => "(GMT -11:00) Midway Island, Samoa",
        "-10" => "(GMT -10:00) Hawaii",
        "-9.5" => "(GMT -9:30) Taiohae",
        "-9" => "(GMT -9:00) Alaska",
        "-8" => "(GMT -8:00) Pacific Time (US & Canada)",
        "-7" => "(GMT -7:00) Mountain Time (US & Canada)",
        "-6" => "(GMT -6:00) Central Time (US & Canada), Mexico City",
        "-5" => "(GMT -5:00) Eastern Time (US & Canada), Bogota, Lima",
        "-4.5" => "(GMT -4:30) Caracas",
        "-4" => "(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz",
        "-3.5" => "(GMT -3:30) Newfoundland",
        "-3" => "(GMT -3:00) Brazil, Buenos Aires, Georgetown",
        "-2" => "(GMT -2:00) Mid-Atlantic",
        "-1" => "(GMT -1:00) Azores, Cape Verde Islands",
        "+0" => "(GMT +0:00) Western Europe Time, London, Lisbon, Casablanca",
        "+1" => "(GMT +1:00) Brussels, Copenhagen, Madrid, Paris",
        "+2" => "(GMT +2:00) Kaliningrad, South Africa",
        "+3" => "(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg",
        "+3.5" => "(GMT +3:30) Tehran",
        "+4" => "(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi",
        "+4.5" => "(GMT +4:30) Kabul",
        "+5" => "(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent",
        "+5.5" => "(GMT +5:30) Bombay, Calcutta, Madras, New Delhi",
        "+5.75" => "(GMT +5:45) Kathmandu, Pokhara",
        "+6" => "(GMT +6:00) Almaty, Dhaka, Colombo",
        "+6.5" => "(GMT +6:30) Yangon, Mandalay",
        "+7" => "(GMT +7:00) Bangkok, Hanoi, Jakarta",
        "+8" => "(GMT +8:00) Beijing, Perth, Singapore, Hong Kong",
        "+8.75" => "(GMT +8:45) Eucla",
        "+9" => "(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk",
        "+9.5" => "(GMT +9:30) Adelaide, Darwin",
        "+10" => "(GMT +10:00) Eastern Australia, Guam, Vladivostok",
        "+10.5" => "(GMT +10:30) Lord Howe Island",
        "+11" => "(GMT +11:00) Magadan, Solomon Islands, New Caledonia",
        "+11.5" => "(GMT +11:30) Norfolk Island",
        "+12" => "(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka",
        "+12.75" => "(GMT +12:45) Chatham Islands",
        "+13" => "(GMT +13:00) Apia, Nukualofa",
        "+14" => "(GMT +14:00) Line Islands, Tokelau"];

    public function __construct()
    {
        if (Session::has('visitorTZ')) {
            View::share('visitorTZ', Session::get('visitorTZ'));
            View::share('timeZoneOffsets', $this->timeZone);
        } else {
            View::share('visitorTZ', false);
            View::share('timeZoneOffsets', $this->timeZone);
        }

    }

    public function setTimeZone(Request $request, $timezone)
    {
        session(['visitorTZ' => $timezone]);
        return response()->json(Session::get('visitorTZ'));
    }

    public function getNightMode()
    {
      if(Session::get('NightMode') && Session::get('NightMode') == 1){
        return 1;
      }
      else{
        return 0;
      }
    }
    public function setNightMode()
    {
      if(Session::get('NightMode')){
        if(Session::get('NightMode') == 1){
          session(['NightMode' => 0]);
        }else if(Session::get('NightMode') == 0){
          session(['NightMode' => 1]);
        }
      }else{
        session(['NightMode' => 1]);
      }
      return Session::get('NightMode');
    }

    public function cookieSet($name, $value, $time)
    {
        setcookie($name, $value, $time, "/");
    }

    public function eventRSSFeed(){
        $event = new Event;
        $events = $this->getEventList();
        /*echo '<pre>';
        print_r($events);
        die;*/

        /*$date = $events[0]->start_date;
        $gmt_date = gmdate('D, d M Y H:i:s e', strtotime($date) );
        echo $gmt_date;
        die;*/

        /*header("Content-Type: text/xml; charset=ISO-8859-1");
        $rssfeed = '<?xml version="1.0" encoding="ISO-8859-1"?>';*/

        header("Content-Type: text/xml; charset=UTF-8");
        $rssfeed = '<?xml version="1.0" encoding="UTF-8"?>';
        $rssfeed .= '<rss version="2.0"  xmlns:sos="http://www.feedforall.com/sos-dtd/" xmlns:atom="http://www.w3.org/2005/Atom">';
        $rssfeed .= '<channel>';
        $rssfeed .= '<title>Event List</title>';
        //$rssfeed .= '<atom:link href="'.$_SERVER['APP_URL'].$_SERVER['REQUEST_URI'].'" rel="self" type="application/rss+xml" />';
        $rssfeed .= '<link>'.$_SERVER['APP_URL'].'</link>';
        $rssfeed .= '<description>Event List RSS feed</description>';
        $rssfeed .= '<language>en-us</language>';
        $rssfeed .= '<copyright>Copyright (C) 2017 soccerstreams.net</copyright>';
        if (!empty($events)) {
            foreach ($events as $val) {
                $rssfeed .= '<item>';
                if(is_null($val->event_title) || $val->event_title == 'NULL' || empty($val->event_title)){
                    $rssTitle = $val->home_team_slug.' vs '.$val->away_team_slug;
                }else{
                    $rssTitle = $val->event_title;
                }
                $rssfeed .= '<title>' . $rssTitle . '</title>';
                $rssfeed .= '<pubDate>' . $val->start_date . '</pubDate>';
                $rssfeed .= '<sos:competitionName>' . $val->competition_name . '</sos:competitionName>';

                if(is_null($val->event_title) || $val->event_title == 'NULL' || empty($val->event_title)){
                    $rssfeed .= '<link>' . secure_url('streams/'.$val->event_id.'/'.$val->home_team_slug.'_vs_'.$val->away_team_slug) . '</link>';
                    $rssfeed .= '<guid>' . secure_url('streams/'.$val->event_id.'/'.$val->home_team_slug.'_vs_'.$val->away_team_slug) . '</guid>';
                }else{
                    $rssfeed .= '<link>' . secure_url('streams/'.$val->event_id.'/'.urlencode($val->event_title)) . '</link>';
                    $rssfeed .= '<guid>' . secure_url('streams/'.$val->event_id.'/'.urlencode($val->event_title)) . '</guid>';
                }

                $rssfeed .= '</item>';
            }
        } else {
            $rssfeed .= '<error>There is no any event!</error>';
        }
        $rssfeed .= '</channel>';
        $rssfeed .= '</rss>';
        return Response::make($rssfeed, '200')->header('Content-Type', 'text/xml');
    }

    public function getEventList(){

        $event = new Event;
        $events = $event->getEventsByInterval2();

        $validEvents = [];



        foreach ($events as $event) {

                $validEvents[] = $event;

        }
               return $validEvents;
    }
}
