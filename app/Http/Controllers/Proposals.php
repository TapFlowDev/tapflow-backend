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
        $rules =array(
            "team_id"=>"required",
            "project_id"=>"required",
            "price"=>"required",
            "days"=>"required",
            "why_us"=>"required",
        );
        $validators=Validator::make($req->all(),$rules);
        if ($validators->fails())
        {
        $response =array('data'=>array(
                "message"=>"Validation Error",
                "status"=>"101",
                "error"=>$validators->errors()
        ))  ;
        return (json_encode($response));          
        }
        else
        try{
        {
            $proposal=proposal::create($req->all());
            $proposal_id=$proposal->id;
            $response =array('data'=>array(
                "message"=>"proposal added successfully",
                "status"=>"200",
                "proposal_id"=>$proposal_id
        ))  ;
        return (json_encode($response));       

        }
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
}
