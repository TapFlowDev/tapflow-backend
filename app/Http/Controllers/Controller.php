<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function returnResponse($requestStatus=200, $requestMessage = 'successfully', $array){
        $response = array(
            "status" => array(
                "message" => $requestMessage,
                "code" => $requestStatus
        ),
        "data"=>$array
    );
    return $response;
    }
}
