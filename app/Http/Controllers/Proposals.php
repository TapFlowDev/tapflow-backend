<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use App\Models\proposal;
use Exception;

class Proposals extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        $rules = array(
            "team_id" => "required",
            "user_id" => "required",
            "project_id" => "required",
            "price_min" => "required",
            "price_max" => "required",
            "from" => "required",
            "to" => "required",
            "our_offer" => "required"
        );
        $validators = Validator::make($req->all(), $rules);
        if ($validators->fails()) {
            $responseData = $validators->errors();
            $response = Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        }

        try {
            $proposal = proposal::create($req->all());
            $proposal_id = $proposal->id;
            $responseData = array("proposal_id" => $proposal_id);
            $response = Controller::returnResponse(200, "proposal added successfully", $responseData);
            return (json_encode($response));
        } catch (Exception $error) {
            $responseData = array("error" => $error,);
            $response = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
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
    function getProposalByProjectAndTeamId($project_id,$team_id)
    {
        $proposal=proposal::where(['project_id'=>$project_id,"team_id"=>$team_id])->get();
        return $proposal;
    }
}
