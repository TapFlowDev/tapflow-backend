<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserType
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
        if(!($request->user_type == '1'))
        {   
            $response=['data'=>'You not authorized to access freelancer pages'];
                return ($response);
        }
        elseif(!($request->user_type == '2'))
        {
            $response=['data'=>'You not authorized to access client pages'];
            return ($response);
        }
        else
        {
            $response=['data'=>'This type of user not registered'];
            return ($response);
        }

        
    }
}
