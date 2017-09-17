<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class AuthorizedApiUser
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request $request
   * @param  \Closure $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    $user = User::where(['email'=>$request->email,'api_key'=>$request->token])->first();
    if(is_null($user)){
      return response()->json([
        'status'=>false,
        'message'=>'Invalid user, permission denied!'
      ]);
    }else{
      return $next($request);
    }
    
  }
}
