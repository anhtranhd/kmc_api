<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class Cors
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
        $headers = [
		    'Access-Control-Allow-Origin'      => '*',
		    'Access-Control-Allow-Methods'     => 'POST, PATCH, GET, OPTIONS, PUT, DELETE',
		    'Access-Control-Allow-Credentials' => 'false',
		    'Access-Control-Max-Age'           => '86400',
		    'Access-Control-Allow-Headers'     => $request->header('Access-Control-Request-Headers')
	    ];
	    if ($request->isMethod('OPTIONS'))
	    {
		    return response()->json('{"method":"OPTIONS"}', 200, $headers);
	    }

	    $response = $next($request);
	    foreach($headers as $key => $value)
	    {
		    $response->header($key, $value);
	    }
	    return $response;
    }
}
