<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Milestone;
use Exception;

class Milestones extends Controller
{
    //add row 
    function Insert($req,$project_id)
    {
       dd($req);
    //  try{
    //         $milestones=Milestone::Create($req->all());
    //         $milestone_id=$milestones->id;
    //         $response=array('data'=>array(
    //             'message'=>"final proposal add successfully",
    //             "status"=>"200",
    //             "Accepted_proposal_id"=>$milestone_id,
    //         ));
    //         return (json_encode($response));
    //     }
    //     catch(Exception $error)
    // {
    //     $response = array("data" => array(
    //         "message" => "There IS Error Occurred",
    //         "status" => "500",
    //         "error" => $error,
    //     ));

    //     return (json_encode($response));
    // }
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
