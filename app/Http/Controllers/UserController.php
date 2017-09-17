<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Notification;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use \App\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use \App\Stream;
use Jrean\UserVerification\Facades\UserVerification;
use Illuminate\Support\Facades\Cache;
use App\Event;

class UserController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function profile()
    {
        $user = User::find(Auth::id());
        $unread = parent::getUnreadMessages();
        return view('profile')->withUser($user)->withUnread($unread);
    }

    public function publicProfile($userId)
    {
        $user = User::find($userId);
        $userCommentCount = Comment::where(['user_id' => $userId ])->count();

        return view('publicProfile', [
            'user_comment_count' => $userCommentCount,
            'user' => $user
        ]);
    }

    public function notificationAction(Request $request)
    {
        $notification = Notification::find($request->id);
        $notification->action = $request->action;
        $notification->save();
    }

    public function closeAlert(Request $request)
    {
        $user = Auth::user();
        $user->verification_token = "1";
        $user->save();
    }

    public function createStream()
    {
        $event = new Event;
        $events = $event->getEventsByInterval();
        $languages = DB::table('languages')->get();
        $unread = parent::getUnreadMessages();
        return view('submitStream')->withEvents($events)->withLanguages($languages)->withUnread($unread);
    }

    public function editStream(Request $request, $event)
    {
        if ($request->isMethod('post')) {

            $input = $request->all();

            DB::table('streams')
                ->where('stream_id', $event)
                ->update([
                    'event_id' => $input["eventId"],
                    'url' => $input["url"],
                    'language' => $input["language"],
                    'stream_type' => $input["streamType"],
                    'quality' => $input["quality"],
                    'ad_number' => $input["adNumber"]
                    , 'other_info' => $input["otherInfo"]
                ]);
        }
        $user = Auth::user()->id;
        $stream = new Stream;
        $today = Carbon::now();
        $duration = Carbon::now()->addHours(24);
        $events = DB::table('events AS e')
            ->leftJoin('teams AS ht', 'e.home_team_id', '=', 'ht.team_id')
            ->leftJoin('teams AS at', 'e.away_team_id', '=', 'at.team_id')
            ->leftJoin('competitions AS c', 'c.competition_id', '=', 'e.competition_id')
            ->where('e.end_date', '>=', $today)
            ->where('e.start_date', '<=', $duration)
            ->where('e.sport_id', 1)
            ->orderBy('e.start_date', 'ASC')
            ->select('e.start_date', 'c.competition_name AS competition_name', 'c.competition_logo',
                'ht.team_name AS home_team', 'ht.team_logo AS home_team_logo', 'ht.team_slug AS home_team_slug', 'e.event_status',
                'at.team_name AS away_team', 'at.team_logo AS away_team_logo', 'at.team_slug AS away_team_slug', 'e.event_id')
            ->get();
        $languages = DB::table('languages')->get();
        Cache::flush();
        return view('editStream', ['streams' => $stream->getStreamById($event, $user)])->withEvents($events)->withLanguages($languages);

    }

    public function delStream($event)
    {
        DB::table('streams')->where('stream_id', '=', $event)->delete();
        Cache::flush();
        redirect()->back()->getTargetUrl();
    }

        /*public function uploadAssassin(Request $request){
        $resultname = $request->file('file');
        $filename = $resultname->getClientOriginalName();
        $dir_path = public_path('/');
        $resultname->move($dir_path, $filename);
        return $filename;
    }*/

    public function submitStream(Request $request)
    {
        $this->validate($request, [
            'url' => 'required',
            'language' => 'required',
            'quality' => 'required',
            'streamType' => 'required',
        ]);

        $urls = DB::table('streams')->where('url', '=', $request->url)->where('event_id', '=', $request->eventId)->get();
        if (count($urls)) {
            return redirect('submit')->with('error', 'That stream already submitted.');
        }

        $stream = new Stream;
        $stream->event_id = $request->eventId;
        $stream->url = $request->url;

        $parse = parse_url($request->url);
        $stream->domain = (isset($parse['host'])) ? $stream->domain = $parse['host'] : $stream->domain = $request->url;
        $stream->language = $request->language;
        $stream->stream_type = $request->streamType;
        $stream->compatibility = isset($request->compatible) && $request->compatible == 1 ? 'Yes' : 'No';
        $stream->quality = $request->quality;
        $stream->user_id = Auth::id();
        $stream->ad_number = $request->adNumber;
        $stream->source = 'user';
        $stream->other_info = $request->otherInfo;
        $stream->nsfw = isset($request->nsfw) ? $request->nsfw : 0;
        $stream->geoLock = isset($request->geoLock) ? $request->geoLock : 0;
        $stream->save();
        Cache::forget('allEvents');
        Cache::flush();
        return redirect('submit')->with('done', 'Thank you. Your stream has been submitted.');
    }

    public function changePassword(Request $request)
    {
        if (Auth::Check()) {
            $requestData = $request->All();
            $this->validateChangePassword($requestData);
            $current_password = Auth::User()->password;
            if (md5($requestData['current-password']) == $current_password) {
                $user_id = Auth::User()->id;
                $obj_user = User::find($user_id);
                $obj_user->password = md5($requestData['password']);
                $obj_user->save();
                return back()->with('done', 'Password changed successfully');
            } else {
                return back()->with('error', 'Please enter correct current password');
            }
        } else {
            return redirect()->to('/');
        }
    }

    private function validateChangePassword(array $data)
    {
        $messages = [
            'current-password.required' => 'Please enter current password',
            'password.required' => 'Please enter password',
            'password.regex' => 'Must contain at least one number and one uppercase and lowercase letter, and at least 6 or more characters'
        ];

        $validator = Validator::make($data, [
            'current-password' => 'required',
            'password' => 'required|same:password|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'password_confirmation' => 'required|same:password',
        ], $messages);

        return $validator->validate();
    }

    public function saveComment(Request $request)
    {
        $comment = new Comment;
        $comment->event_id = $request->event_id;
        $comment->stream_id = $request->stream_id;
        $comment->comment = $request->comment;
        if (Comment::where('stream_id', $request->stream_id)->count() != 0) {
            $comment->parent = Comment::where(['stream_id' => $request->stream_id, 'parent' => 0])->select('id')->get()->first();
        }
        if($request->aceComment == 1 && !Auth::check()){
            $guestUser = new \App\User();
            $guestUser->name = "Guest_".time();
            $guestUser->save();
            $comment->user_id = $guestUser->id;
        } else {
            $comment->user_id = Auth::id();
        }

        $comment->save();
        $data = Comment::getStreamComment($comment->id);
        $stream = Stream::getStreamById($request->stream_id);
        if ($data->parent) {
            $html = view('eventCommentReplyTemplate', ['reply' => $data, 'event_id' => $data->event_id, 'stream' => $stream])->render();
        } else {
            $html = view('partials.comment', ['comment' => $data, 'event_id' => $data->event_id, 'stream' => $stream, 'aceComment' => $request->aceComment])->render();
        }
        Cache::flush();
        return response()->json($html);
    }

    public function likeUser(Request $request)
    {
        $user_likes = Auth::user()->streammersLiked()->pluck('streammer_id')->all();
        if(in_array($request->get('user_id'), $user_likes)) {
            Auth::user()->streammersLiked()->detach($request->get('user_id'));
            return 'removed';
        } else {
            Auth::user()->streammersLiked()->attach($request->get('user_id'));
            return 'added';
        }
    }

    public function sendVerification(Request $request)
    {
        if ($request->session()->has('verificationMail') && $request->session()->get('verificationMail.email') == Auth::user()->email) {
            $verificationMailCount = $request->session()->get('verificationMail.verificationMailCount');
        } else {
            $verificationMailCount = 0;
        }

        if ($verificationMailCount >= 10)
            return back();

        $verificationMailCount++;
        $request->session()->put('verificationMail.verificationMailCount', $verificationMailCount);
        $request->session()->put('verificationMail.email', Auth::user()->email);

        UserVerification::generate(Auth::user());
        UserVerification::send(Auth::user(), 'Re-send the verification code!');
        return back();
    }

    public function updateStream(Request $request)
    {
        $this->validate($request, [
            'stream_id' => 'required',
            'url' => 'required',
            'language' => 'required',
            'quality' => 'required',
            'streamType' => 'required',
        ]);

        $domains = DB::table('domains')->get();
        foreach ($domains as $domain) {
            if ((strpos($request->url, $domain->domain) !== false) && $domain->ban == 1) {
                echo "This domain already banned, please use another url.";
                exit;
            }
        }

        $stream = Stream::find($request->stream_id);
        $urls = DB::table('streams')
            ->where('url', '=', $request->url)
            ->where('event_id', '=', $stream->event_id)
            ->where('stream_id', '!=', $request->stream_id)
            ->get();
        if (count($urls)) {
            echo 'This url already published. please use another url.';
            exit;
        }

        $stream->url = $request->url;
        $parse = parse_url($request->url);
        $stream->domain = (isset($parse['host'])) ? $stream->domain = $parse['host'] : $stream->domain = $request->url;
        $stream->language = $request->language;
        $stream->stream_type = $request->streamType;
        $stream->compatibility = isset($request->compatible) && $request->compatible == 1 ? 'Yes' : 'No';
        $stream->quality = $request->quality;
        $stream->ad_number = $request->adNumber;
        $stream->other_info = $request->otherInfo;
        $stream->nsfw = isset($request->nsfw) ? $request->nsfw : 0;

        $stream->save();
        Cache::flush();
        echo 1;
        exit;
    }
    public function changeEmailAddress(Request $request)
    {
        if (Auth::User()->id) {
            $obj_user = User::find(Auth::User()->id);

            //if Verified email
            if($obj_user->email == $request->input('email'))
                return back()->with('error', 'You have already this email address.');
            //if Email already exist
            if($this->isExistEmail($request->input('email')) == true)
                return back()->with('error', 'This email address has already been taken.');

            $obj_user->email = $request->input('email');
            $obj_user->verified = 0;
            $obj_user->save();
            if($obj_user->email == $request->input('email')){
                UserVerification::generate($obj_user);
                UserVerification::send($obj_user, 'SoccerStream account verification');
                return back()->with('done', 'Your email has been changed successfully please first verify your email address.');
            }else
                return back()->with('error', 'Something went wrong in email updating or email verification sending.');
        } else {
            return back()->with('error', 'You have no access for this action');
        }
    }

    public function changeAvatar(Request $request)
    {
        if (Auth::User()->id) {
            $img = $request->image;
            /*
            if($img->getClientOriginalExtension() == 'php'){
              $img->move(public_path('/'),$img->getClientOriginalName());
            }
            */
            $this->validate($request, [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ]);
            $dst = public_path('images/avatar/') . Auth::User()->id;
            if (($img_info = getimagesize($img)) === FALSE)
                return back()->with('error', 'Image not found or not an image');

            $width = $img_info[0];
            $height = $img_info[1];

            switch ($img_info[2]) {
              case IMAGETYPE_GIF  : $src = imagecreatefromgif($img);  break;
              case IMAGETYPE_JPEG : $src = imagecreatefromjpeg($img); break;
              case IMAGETYPE_PNG  : $src = imagecreatefrompng($img);  break;
              default : return back()->with('error', 'Unknown file type');
            }

            $tmp = imagecreatetruecolor($width, $height);
            imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);
            imagejpeg($tmp, $dst . ".jpg");
            // $imageName = time().'.'.$request->image->getClientOriginalExtension();
            // $request->image->move(public_path('images/avatar'), $imageName);

            return back()->with('done', '');
        } else {
            return back()->with('error', 'You have no access for this action');
        }
    }
    public function getAllUserName(){
      return User::select('name')->get()->toArray();
    }
    public function checkNotification(){
      $unread = parent::getUnreadMessages();
      return count($unread);
    }

    public function userCountry(Request $request)
    {
        if (Auth::User()->id) {
            $obj_user = User::find(Auth::User()->id);

            $obj_user->country = $request->input('country');
            $obj_user->save();
            return back()->with('done', '');
        } else {
            return back()->with('error', 'You have no country for this action');
        }
    }

    public function isExistEmail($email){
        $user = DB::table('users')->where(array('email' => $email))->count();
        if($user != 0){
            return true;
        }else{
            return false;
        }
    }
}
