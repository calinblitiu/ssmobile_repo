<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Jrean\UserVerification\Traits\VerifiesUsers;
use Jrean\UserVerification\Facades\UserVerification;

class RegisterController extends BaseController
{
  /*
  |
  |--------------------------------------------------------------------------
  | Register Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles the registration of new users as well as their
  | validation and creation. By default this controller uses a trait to
  | provide this functionality without requiring any additional code.
  |
  */

  use RegistersUsers;
  use VerifiesUsers;

  /**
   * Where to redirect users after registration.
   *
   * @var string
   */
  protected $redirectTo = 'profile/favourite';

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->middleware('guest', ['except' => ['getVerification', 'getVerificationError']]);
  }

  /**
   * Get a validator for an incoming registration request.
   *
   * @param  array $data
   * @return \Illuminate\Contracts\Validation\Validator
   */
  protected function validator(array $data)
  {
    $messages = [
      'password.regex' => 'Must contain at least one number and one uppercase and lowercase letter, and at least 6 or more characters'
    ];

    return Validator::make($data, [
      'name' => 'required|max:8|min:4|unique:users|regex:/^[A-Za-z0-9]+$/',
      //'email' => 'required|email|max:255|unique:users',
      'email' => 'unique:users',
      'password' => 'required|min:6|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
      // 'g-recaptcha-response' => 'required|captcha'
    ], $messages);
  }
  /**
   * Create a new user instance after a valid registration.
   *
   * @param  array $data
   * @return User
   */
  protected function create(array $data)
  {
    return User::create([
      'name' => $data['name'],
      'email' => ($data['email']) ? $data['email'] : "",
      'password' => bcrypt($data['password']),
    ]);
  }

  /**
   * Handle a registration request for the application.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function register(Request $request)
  {
    $this->validator($request->all())->validate();

    $user = $this->create($request->all());
    event(new Registered($user));

    $this->guard()->login($user);

    if($user->email != ""){
        UserVerification::generate($user);
        UserVerification::send($user, 'SoccerStream account verification');
    }
    return $this->registered($request, $user)
      ?: redirect($this->redirectPath());
  }
}
