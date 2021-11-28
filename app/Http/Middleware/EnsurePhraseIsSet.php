<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class EnsurePhraseIsSet
 * @package App\Http\Middleware
 */
class EnsurePhraseIsSet
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

        if(!$authUser->wallet || !$authUser->wallet->phrases_created_at || $authUser->is_temporary_secret_phrase) {
            return redirect(route('set-user-phrases'));
        }
        return $next($request);
    }
}
