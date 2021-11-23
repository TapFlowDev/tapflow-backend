<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;
use Exception;


class ProjectController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        $rules = array(
            "user_id" => "required|exists:users,id",
            "name" => "required",
            "description" => "required",
            "requirements_description" => "required",
            "budget" => "required",
            "days" => "required"
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
        try {
            $data = $req->except(['gender', 'dob', 'category']);

            // print_r($data);
            $project = Project::create($req->all());
            
            $response = array("data" => array(
                "message" => "project created successfully",
                "status" => "200",
                "project_id" => $project->id,
            ));

            return (json_encode($response));
        } catch (Exception $error) {

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
