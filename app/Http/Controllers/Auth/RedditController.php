<?php

namespace App\Http\Controllers\Auth;
ini_set("display_errors", "1");
error_reporting(E_ALL);
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use \App\User;

use Illuminate\Support\Facades\DB;

class RedditController extends BaseController
{
    use AuthenticatesUsers;

    public function redditLogin(){
        if (isLocalDev()) {
            return redirect('/login');
        } else {
            $string = $this->generateRandomString();
            return redirect('https://ssl.reddit.com/api/v1/authorize?response_type=code&client_id='.env("REDDIT_CLIENR_ID").'&scope=identity&state='.$string.'&redirect_uri='.env("REDDIT_REDIRECT_URL"));
            //redirect('https://ssl.reddit.com/api/v1/authorize?response_type=code&client_id=R62bLgvw9yUQcA&scope=identity&state='.$string.'&redirect_uri=https://dev.siolab.pwd/reddit_callback');    
        }
    }

    public function generateRandomString() {
        $length = 10;
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function reddit_callback(Request $request){
        if($request->input('error')){
            return redirect('/login')->with('error','These credentials do not match our records.');
        }

        $code = $request->input('code');
        $tokenResponse = $this->getAccessToken($code);
        $userResponse = [];
        if(isset($tokenResponse->access_token)) {
            $userResponse = $this->getRedditUserDetails($tokenResponse->access_token);
        }

        $userName = (isset($userResponse->name)) ? $userResponse->name : "";
        if($userName != ""){
            if($this->socialMediaLogin($userName)){

                //authentication passed
                $user = $this->getUserDetails($userName);
                if( $user->ban == 1)
                {
                    $this->guard()->logout();
                    return redirect('/login')->with('error','You have been banned from participating on this website. Please use the contact form for more information or contact the moderators on /r/SoccerStreams.');
                }else {
                    $user_id = Auth::User()->id;
                    $obj_user = User::find($user_id);
                    $obj_user->updated_at = date('Y-m-d H:i:s');
                    $obj_user->save();

                    return redirect('/profile');
                }
            }
            else{
                return redirect('/login')->with('error','These credentials do not match our records.');
            }
        }
    }

    public function getAccessToken($code){
        $client_id = "";
        $client_secret = "";
        $username = "";
        $fields_string = "";

        $url = "https://ssl.reddit.com/api/v1/access_token";
        $fields = array("grant_type" => "authorization_code", "code" => $code, "redirect_uri" => env("REDDIT_REDIRECT_URL"));

        foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, env("REDDIT_CLIENR_ID").":".env("REDDIT_SECRET_KEY"));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        $result = curl_exec($ch);
        if(curl_error($ch))
        {
            echo 'error:' . curl_error($ch);
        }
        curl_close($ch);

        if($result){
            return \GuzzleHttp\json_decode($result);
        } else {

        }
    }

    public function getRedditUserDetails($access_token){
        $user_info_url = "https://oauth.reddit.com/api/v1/me";

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$user_info_url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: bearer ".$access_token, "User-Agent: flairbot/1.0 by ".$_SERVER ['HTTP_USER_AGENT']));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);


        $result = curl_exec($ch);
        if(curl_error($ch))
        {
            echo 'error:' . curl_error($ch);
        }
        curl_close($ch);

        if($result){
            return \GuzzleHttp\json_decode($result);
        } else {

        }
    }

    public function socialMediaLogin($userName){
        $users = $this->getUserDetails($userName);

        //If New User then store in to database
        if(empty($users)){
            $users = DB::table('users')->insert(
                ['name' => $userName, 'password' => bcrypt($userName), 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'verification_token' => '1','social_login' => "reddit"]
            );

            //After new user record inserted
            $users = $this->getUserDetails($userName);
        }


        $request = array(
            "name" => $userName,
            "password" => $userName
        );
        //if (Auth::guard()->guest($request)){
        if (Auth::guard()->attempt($request)){
           return true;
        }
        else{
            return false;
        }

        //return redirect()->back()->with('error','These credentials do not match our records.');

    }

    public function getUserDetails($userName){
        $users = DB::table('users')
            ->where('name', '=', $userName)
            ->first();
        return $users;
    }
}