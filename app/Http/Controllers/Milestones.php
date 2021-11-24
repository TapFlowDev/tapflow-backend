<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Milestone;
use Exception;
use Illuminate\Foundation\Mix;

use App\Http\Controllers\TasksController;


class Milestones extends Controller
{
    //add row 
    function Insert($data,$project_id,$final_proposal_id)

    {  
       
        $Tasks=new TasksController;
       
      
        foreach($data as $milestone)
        {  
            $arr=array(
                "project_id"=>$project_id,
                "final_proposal_id"=>$final_proposal_id,
                "name"=>$milestone['name'],
                "days"=>$milestone['days'],
                "description"=>$milestone['description'],
                "price"=>"100",
            );
            $milestone_info=Milestone::create($arr);
           
            $Tasks->Insert($milestone['tasks'],$milestone_info->id);
           
        }
      
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
