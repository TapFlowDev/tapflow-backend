<?php

 namespace App\Http\Middleware;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

 class Authenticate extends Middleware
 {
     /**
     * Get the path the user should be redirected to when they are not authenticated.
    *
    * @param  \Illuminate\Http\Request  $request
      * @return string|null
     */
     protected function redirectTo($request)
    {
    //    if (! $request->expectsJson()) {
    //         return "status:500 kol";
    //     }
    if(!$request)
    {
        $response = array("data" => array(
            "message" => "Validation Error",
            "status" => "101",
        ));
        return (json_encode($response));
    }
    }
 } 
