<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard, $role = null)
    {
        $pass = false;

        if ($guard == 'customer' && Auth::guard('customer')->check()) {
            // Customer
            $pass = true;
        } elseif ($guard == 'staff' && Auth::guard('staff')->check()) {   
            // Staff         
            $pass = true;

            // Admin
            $user = Auth::guard('staff')->user();
            if ($role && !$user->hasRole($role)) {
                $pass = false;
            }
        }

        if ($pass) {
            return $next($request);
        }
        else {
            return redirect('/login');
        }
    }
}
