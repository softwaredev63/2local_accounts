<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePasswordIsChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $authUser = auth()->user();
        if(!$authUser) {
            Auth::logout();
            return redirect('/login');
        }
        if ($authUser->has_changed_password) {
            return $next($request);
        }
        return redirect(route('change-password'));
    }
}
