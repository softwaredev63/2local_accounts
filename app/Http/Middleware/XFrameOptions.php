<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class XFrameOptions
 * @package App\Http\Middleware
 */
class XFrameOptions
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $xframeOptions = env('X_FRAME_OPTIONS', 'DENY');

        if ($xframeOptions) {
            if (false !== strpos($xframeOptions, 'ALLOW-FROM')) {
                $url = trim(str_replace('ALLOW-FROM', '', $xframeOptions));

                $response->headers->set('Content-Security-Policy', 'frame-ancestors '.$url);
            } else {
                $response->headers->set('Content-Security-Policy', 'frame-ancestors none');
            }
        }

        $response->headers->set('X-Frame-Options', $xframeOptions);
        return $response;
    }
}
