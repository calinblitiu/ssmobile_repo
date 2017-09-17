<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use \Auth;

use Socialite;

class RedditLoginController extends Controller
{
    /**
     * Redirect the user to the reddit authentication page.
     *
     * @return Response
     */
    public function redirectToReddit()
    {
        return Socialite::with('reddit')->redirect();
    }

    /**
     * Obtain the user information from reddit.
     *
     * @return Response
     */
    public function handleRedditCallback()
    {
        $redditUser = Socialite::with('reddit')->user();
        if($redditUser->getNickname() == "")
			return redirect('/login')->with('error','Something went wrong please try again.');

        $user = User::where('name', $redditUser->getNickname())->first();

        if($user) {
        	if( $user->ban == 1) {
				return redirect('/login')->with('error','You have been banned from participating on this website. Please use the contact form for more information or contact the moderators on /r/SoccerStreams.');
			}

        	Auth::login($user);

		    $user->updated_at = date('Y-m-d H:i:s');
		    $user->verified = $redditUser->user["has_verified_email"] ? 1 : 0;

		    $user->save();

		    return redirect('/profile');
        }

        $user = new User();

	    $user->name = $redditUser->getNickname();
	    $user->password = bcrypt('Risul321');
	    $user->created_at = date('Y-m-d H:i:s');
	    $user->updated_at = date('Y-m-d H:i:s');
	    $user->verified = $redditUser->user["has_verified_email"] ? 1 : 0;
	    $user->social_login = "reddit";

	    $user->save();

    	Auth::login($user);

	    return redirect('/profile/favourite');
    }
}
