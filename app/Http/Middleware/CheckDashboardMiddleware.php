<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class CheckDashboardMiddleware
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

        session_start();
        $role_id = Session::get('role_id');
        /* if ($role_id == 2) {

            return redirect('student-dashboard');
        } */
        if ($role_id == 7) {
            return redirect('user-dashboard');
        } elseif ($role_id != '') {
            return $next($request);
        } else {
            return redirect('login');
        }
    }
}
