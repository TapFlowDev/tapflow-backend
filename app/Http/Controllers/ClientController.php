<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;

use Exception;
class ClientController extends Controller
{
    //add row 
    function get_client_info($id)
    {
        try{
            $user=User::where('id',$id)->get();
            $client=Client::where('user_id',$id)->get();
            $response = array("data" => array(
                "user" => $user,
                "info" =>$client,
                "status" => "200",
            ));
            return (json_encode($response));
        }
        catch(Exception $error)
        {
            $response = array("data" => array(
                "message" => "There IS Error Occurred",
                "status" => "500",
                "error" => $error,
            ));
    
            return (json_encode($response));   
        }
    }
    //update row according to row id
    function Update($id)
    {

    }
    //delete row according to row id
    function Delete($id)
    {

    }
    function updateTeamId($userId, $teamId){
        Client::where('user_id', $userId)->update(['company_id'=>$teamId]);
    }
}
