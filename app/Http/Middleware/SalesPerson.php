<?php

namespace App\Http\Middleware;
use App\SystemNotification;
use Closure;


class SalesPerson
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
        if (auth()->check()) {

            $user = auth()->user();

    
            if ($user) {

                $notifications = SystemNotification::where('is_shown', true)
                    ->where('is_resolved', false)
                    ->where('type', 'user')
                    ->where(function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                        // Only filter by company if company_id is NOT 1
                        if (session('logged_session_data.company_id') != 1) {
                            $q->where('company_id', session('logged_session_data.company_id'));
                        }
                    })
                    ->get();


                view()->share('centerNotifications', $notifications);

            }


        }

        return $next($request);
    }
}
