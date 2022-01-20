<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AccessAgency
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
        if (Gate::allows('is-agency')) {

            return $next($request);
        }
        $response  = array(
            "status" => array(
                "message" => 'unathrized action',
                "code" => 401
            ),
            "data" => []
        );
        return response($response, 401);
    }
}
