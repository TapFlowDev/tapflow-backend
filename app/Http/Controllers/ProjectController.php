<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProjectCategoriesController;
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
        else{
        try {
            $data = $req->except(['gender', 'dob', 'categories']);
            $ProjectCategoriesObj=new ProjectCategoriesController;
            // print_r($data);
            $project = Project::create($req->all());
            $project_id=$project->id;
            $cats = json_decode($req->categories);
                if (isset($cats)) {

                    foreach ($cats as $key => $value) {
                        $categoryArr = array();
                        foreach ($value->subCat as $keySub => $subValue) {
                            $categoryArr[$keySub]['project_id'] = $project_id;
                            $categoryArr[$keySub]['category_id'] = $value->catId;
                            $categoryArr[$keySub]['sub_category_id'] = $subValue;
                        }
                       $add_cat= $ProjectCategoriesObj->addMultiRows($categoryArr);
                       if($add_cat == 500)
                       {
                        $delProject=Project::where('id',$project_id)->delete();
                        $response = Controller::returnResponse(500, 'add cast error',[]);
                        return json_encode($response);
                       }
                    }
                }
            $response = array("data" => array(
                "message" => "project created successfully",
                "status" => "200",
                "project_id" => $project->id,
            ));

            return (json_encode($response));
        
        }catch (Exception $error) {

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

    function exploreProject()
    {
        $allProjects=Project::all();
        
    }
}
