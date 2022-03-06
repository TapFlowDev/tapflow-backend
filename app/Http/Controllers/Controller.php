<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Exception;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function returnResponse($requestStatus = 200, $requestMessage = 'successfully', $array)
    {
        $response = array(
            "status" => array(
                "message" => $requestMessage,
                "code" => $requestStatus
            ),
            "data" => $array
        );
        return $response;
    }

    // this function to check if the user belong to a team or not and what is the privileges for this user
    public function checkUser($req)
    {
        try {
            $userData = $req->user();
            $member = DB::table('group_members')
                ->where('user_id', '=', $userData->id)
                ->first();
            if ($member === null) {
                return ['exist' => 0];
            } else {
                $verified= DB::table('groups')
                ->where('id','=',$member->group_id)
                ->select('verified')
                ->first();
                return ['exist' => 1, 'user_id' => $member->user_id, 'group_id' => $member->group_id, 'privileges' => $member->privileges,"type"=>$userData['type'],'verified'=>$verified];
            }
        } catch (Exception  $error) {
            $response = Controller::returnResponse(500, "check user error", $error->getMessage());
            return (json_encode($response));
        }
    }
}
