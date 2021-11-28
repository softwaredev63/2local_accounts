<?php

namespace App\Http\Middleware;

use DB;
use Closure;

class ClientMiddleware
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
        if($request->header('Api-Key')){
			$apiKey = $request->header('Api-Key');
			
			$client = DB::table('clients')->where('api_key', $apiKey)->get();
			
			if(count($client) > 0)
			{
				return $next($request);
			} 
		}
		
		return response()->json([
			'message' => 'Bad Request: Api-Key needs to be included to header',
		], 400);
    }
}