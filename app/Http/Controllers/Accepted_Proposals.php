<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Accepted_proposal;
use App\Http\Controllers\Milestones;
use Exception;

class Accepted_Proposals extends Controller
{
    //add row 
    function Insert(Request $req)
    {      
        $milestone=new Milestones;
        $rules = array(
            "team_id" => "required",
            "project_id" => "required",
            "proposal_id" => "required",
            "price" => "required",
            "days"=>"required",
            "milestones"=>"required",
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $response = array("data" => array(
                "message" => "Validation Error",
                "status" => "101",
                "error" => $validator->errors()
            ));
            return (json_encode($response));

        }
        else
        {
            try{
               
            $final_proposal=Accepted_proposal::create($req->except(['milestones']));
            $final_proposal_id=$final_proposal->id;
            $milestones=$milestone->Insert($req->milestones,$req->project_id);
            $response=array('data'=>array(
                'message'=>"final proposal add successfully",
                "status"=>"200",
                "Accepted_proposal_id"=>$final_proposal_id,
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
    }
    //update row according to row id
    function Update($id)
    {

    }
    //delete row according to row id
    function Delete($id)
    {

    }
}
