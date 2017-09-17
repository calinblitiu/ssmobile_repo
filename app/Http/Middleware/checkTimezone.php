<?php

namespace App\Http\Middleware;

use Closure;

class checkTimezone
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
    
    //$hasTZ = $request->session()->has('tz') ? true : false;
    return $next($request);
  }
}
