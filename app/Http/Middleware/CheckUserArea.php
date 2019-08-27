<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserArea
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if (Auth::user()->user_group_id == 2 and $request->route()->getPrefix() == '/extranet')
                return redirect(route('site.dashboard'));

            /*if (Auth::user()->user_group_id == 1 and !$request->route()->getPrefix())
                return redirect(route('admin.home'));*/
        }

        return $next($request);
    }
}
