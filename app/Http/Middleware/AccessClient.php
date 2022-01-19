<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AccessClient
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
        if (Gate::allows('is-client')) {

            return $next($request);
        }
        $response  = array(
            "status" => array(
                "message" => 'unathrized user',
                "code" => 503
            ),
            "data" => []
        );
        return response($response, 503);

    }
}
