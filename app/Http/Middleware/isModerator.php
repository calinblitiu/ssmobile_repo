<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;


class isModerator
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
    if (\Illuminate\Support\Facades\Auth::user()->role >= 1) {
      return $next($request);
    } else {
      return redirect('/');
    }
  }
}
