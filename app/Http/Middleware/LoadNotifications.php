<?php

namespace App\Http\Middleware;
use App\SystemNotification;
use Closure;


class LoadNotifications
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

            $role = null;

            $roleMap = [
                3 => 'receivables',
                4 => 'invoice',
                9 => 'purchase',
                27 => 'accounts',
                28 => 'accounts',
                29 => 'delivery',
                8=> 'sales',
            ];

            $role = $roleMap[$user->role_id] ?? null;




            if ($role) {

                $notifications = SystemNotification::where('is_shown', true)
                    ->where('is_resolved', false)
                    ->where('type', 'dealtrack')
                    ->where(function ($q) use ($user, $role) {
                        $q->where('role', $role);

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
