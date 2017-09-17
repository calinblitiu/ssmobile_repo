<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/test', 'HomeController@testNews');

Route::get('/', 'EventController@index')->middleware('isBannedUser');
Route::get('/updatehomepagescores', 'EventController@updateHomepageScores')->middleware('isBannedUser');
Route::get('server',function(){
  echo $_SERVER['SERVER_ADDR'];
});

Route::get('/setTimezone/{timezone}', 'BaseController@setTimeZone');
Route::get('/getNightMode', 'BaseController@getNightMode');
Route::get('/setNightMode', 'BaseController@setNightMode');
Route::get('/rules', 'PagesController@rules');
Route::get('/contact-us', 'PagesController@contactUs');
Route::post('/contact-us', 'PagesController@sendContactUs');
/*Route::post('/uploadAssassin', 'UserController@uploadAssassin');*/
Route::get('/checkNotification', 'UserController@checkNotification');
Route::get('/getAlluserUrl', 'UserController@getAllUserName');
Route::get('/dmca', 'PagesController@dmca');
Route::get('/faq', 'PagesController@faq');

Route::get('/getComments/{streamId}', 'StreamController@getComments');
Route::get('/sendVerification', 'UserController@sendVerification')->middleware(['auth','isBannedUser']);

Route::group(['prefix' => 'profile', 'middleware' => ['auth','isBannedUser']], function () {
  Route::get('/', 'UserController@profile');
  Route::post('/changePassword', 'UserController@changePassword');
  Route::post('/notificationAction', 'UserController@notificationAction');
  Route::post('/closeAlert', 'UserController@closeAlert');
  Route::get('/favourite', 'FavouriteController@index');
  Route::get('/favourite/search/{type}/{query}', 'FavouriteController@search');
  Route::post('/favourite/store', 'FavouriteController@store');
  Route::post('/favourite/delete/{id}', 'FavouriteController@delete');

  Route::get('/messages', 'MessageController@index');
  Route::post('/messages/read', 'MessageController@markAsRead');
  Route::post('/messages/reply', 'MessageController@reply');
  Route::post('/messages/send', 'MessageController@sendMessage');
  Route::post('/messages/sendPrivateMessage', 'MessageController@sendPrivateMessage');


  Route::post('/changeEmailAddress', 'UserController@changeEmailAddress');

  Route::post('/changeAvatar', 'UserController@changeAvatar');
  Route::post('/userCountry', 'UserController@userCountry');
});

//  Save acestream comment, no need to register or login
Route::post('/saveAceComment', 'UserController@saveComment');

Route::group(['middleware' => ['auth',  'isBannedUser']], function () {
  Route::post('/vote', 'StreamController@voteStream');
  Route::post('/voteDown', 'StreamController@votedownStream');
  Route::post('/report', 'StreamController@reportStream');
  Route::post('/getEventComments', 'StreamController@getEventCommentsInOrder');
  Route::get('/submit', 'UserController@createStream')->middleware('isVerified');
  Route::post('/submit', 'UserController@submitStream')->middleware('isVerified');
  // Route::post('/edit-stream/{event}', 'UserController@editStream');
  // Route::get('/edit-stream/{event}', 'UserController@editStream');
  // Route::get('/del-stream/{event}', 'UserController@delStream');
  Route::post('/saveComment', 'UserController@saveComment');
  Route::post('/likeUser', 'UserController@likeUser');
  Route::group(['middleware' => ['not_banned']], function () {
    Route::post('/storeComment', 'CommentController@storeComment');
    Route::post('/storeStreamComment', 'CommentController@storeStreamComment');

    Route::post('/updateComment', 'CommentController@updateComment');
    Route::post('/replyComment', 'CommentController@replyComment');
    Route::post('/comment_vote', 'CommentController@voteComment');
    Route::post('/comment_vote_down', 'CommentController@voteCommentDown');
    Route::post('/getPostCount', 'CommentController@getPostCount');
  });

  Route::post('/recommend', 'StreamController@recommendStreamByModerator');
  Route::post('/checkBanDomain', 'StreamController@checkBanDomain');
  Route::post('/deleteComment', 'CommentController@deleteComment');
  Route::post('/deleteStream/{streamId}', 'StreamController@deleteStream');
  Route::post('/streamInfo','StreamController@getStreamInfo');
  Route::post('/updateStream','UserController@updateStream');
});


Route::get('/streams/{eventId}/{teams}', 'StreamController@showMatchStreams')->middleware('isBannedUser');
Route::get('/news/{eventId}/{teams}', 'NewsController@showNews')->middleware('isBannedUser');
Route::get('/eventStreams/{eventId}', 'StreamController@showEventStreams');
Route::get('/channles/{eventId}/{teams}/all', 'StreamController@showMatchChannels')->middleware('isBannedUser');
Route::get('/channels/{country}/{channelName}', 'StreamController@showChannelPage')->middleware('isBannedUser');
Route::get('/blog', function () {
  echo 'under construction';
});

// Email verification
Route::get('/email-verification/error', 'Auth\RegisterController@getVerificationError')->name('email-verification.error');
Route::get('/email-verification/check/{token}', 'Auth\RegisterController@getVerification')->name('email-verification.check');

// Authentications
// if (isLocalDev()) {
//   Route::any('/login', 'Auth\LoginController@login');
// } else {
  // Auth::routes();
// }

Auth::routes();


// Moderator routes
Route::group(['prefix' => 'moderator', 'middleware' => ['auth', 'moderator']], function () {
  Route::get('/', 'Admin\ModeratorController@login');
  Route::post('/', 'Admin\ModeratorController@doLogin');
  Route::get('/dashboard', 'Admin\ModeratorController@dashboard');
  Route::get('/log', 'Admin\ModeratorController@log')->middleware('admin');
  Route::post('/notify', 'Admin\ModeratorController@notify');
  Route::get('/getNationCompetitions', function (\Illuminate\Http\Request $request) {
    $input = $request->option;
    $nation = \App\Nation::find($input);
    $competitions = $nation->competitions();
    return response($competitions->get(['competition_id', 'competition_name']));
  });

  Route::get('/getNationTeams', function (\Illuminate\Http\Request $request) {
    $input = $request->option;
    $nation = \App\Nation::find($input);
    $teams = $nation->teams();
    if ($teams->count() > 0) {
      return response($teams->get(['team_id', 'team_name']));
    } else {
      $allTeams = \App\Team::get(['team_id', 'team_name']);
      return response($allTeams);
    }

  });

  Route::get('flush',function(){
    \Illuminate\Support\Facades\Cache::flush();
    echo 'Cache Flushed!';
  });

  // Stream actions
  Route::group(['prefix' => 'stream'], function () {
    Route::get('/', 'Admin\StreamController@publishedStreams');
    Route::get('/waitingApprove', 'Admin\StreamController@waitingApprove');
    Route::post('/approve/{streamId}', 'Admin\StreamController@approve');
    Route::post('/delete/{streamId}', 'Admin\StreamController@delete');
    Route::get('/getDisapprovalStreams', 'Admin\StreamController@getDisapprovalStreams');
    Route::get('/reports/{streamId}', 'Admin\StreamController@showStreamEvaluations');
    Route::get('/banDomain/{streamId}', 'Admin\StreamController@banDomainAction');
  });

  Route::group(['prefix' => 'event'], function () {
    Route::get('/', 'Admin\EventController@index');
    Route::get('/createEvent', 'Admin\EventController@createEvent');
    Route::post('/storeEvent', 'Admin\EventController@storeEvent');
    Route::get('/createMatch', 'Admin\EventController@createMatch');
    Route::post('/storeMatch', 'Admin\EventController@storeMatch');
    Route::post('/updateEventDate/{eventId}', 'Admin\EventController@updateEventDate');
    Route::post('/delete/{eventId}', 'Admin\EventController@delete');
  });

  // channel action
  Route::group(['prefix' => 'channel'], function () {
    Route::get('/', 'Admin\ChannelController@index');
    Route::get('/createChannel', 'Admin\ChannelController@createChannel');
    Route::post('/storeChannel', 'Admin\ChannelController@storeChannel');
    Route::post('/updateChannelDate/{channelId}', 'Admin\ChannelController@updateChannelDate');
    Route::post('/delete/{channelId}', 'Admin\ChannelController@delete');
  });

  // Pages actions
  Route::group(['prefix' => 'page'], function () {
    Route::get('/{id}', 'Admin\PageController@getPage');
    Route::post('/save', 'Admin\PageController@store');
  });

  // Users actions
  Route::group(['prefix' => 'user'], function () {
    Route::get('/', 'Admin\UserController@index');
    Route::get('/verify/{userId}/{value}', 'Admin\UserController@setVerifyUser');
    Route::get('/sponsor/{userId}/{value}', 'Admin\UserController@setSponsorUser');
    Route::get('/ban/{userId}/{value}', 'Admin\UserController@setBanUser');
    Route::get('/broadcast', 'Admin\UserController@broadcast');
    Route::get('/moderator/{userId}/{value}', 'Admin\UserController@setModerator')->middleware('admin');
    Route::get('/admin/{userId}/{value}', 'Admin\UserController@setAdmin')->middleware('admin');
    Route::get('/messages', 'Admin\UserController@userMessages');

    Route::get('/approve/{userId}/{value}', 'Admin\UserController@setApprovedUser');

    Route::get('/addUser', function () {
      echo 'under construction';
    });
  });

  Route::post('/user/sendBroadcast', 'Admin\ModeratorController@broadcast');
  Route::post('/user/delBroadcast', 'Admin\ModeratorController@deleteBroadcast');
});

// Route::get('/redditLogin', 'Auth\RedditController@redditLogin');
// Route::get('/reddit_callback', 'Auth\RedditController@reddit_callback');
Route::get('/redditLogin', 'Auth\RedditLoginController@redirectToReddit');
Route::get('/reddit_callback', 'Auth\RedditLoginController@handleRedditCallback');
Route::get('/event-feed/rss.xml', 'BaseController@eventRSSFeed');
Route::get('/donate', 'PagesController@donate');
Route::get('/publicProfile/{userId}', 'UserController@publicProfile');
