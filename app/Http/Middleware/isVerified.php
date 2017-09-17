<?php

namespace App\Http\Middleware;

use Closure;

class isVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      if( !is_null($request->user()) && !$request->user()->verified){
        return redirect('profile')->with('info','Please check your email to verify your email address');
      }
  
      return $next($request);
    }
}
