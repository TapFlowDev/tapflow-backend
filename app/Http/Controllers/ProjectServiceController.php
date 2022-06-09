<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Project_service;
use Illuminate\Http\Request;

class ProjectServiceController extends Controller
{
    //add row 
    function Insert($projectId, $data)
    {
        foreach ($data as $service) {
            $arr = array(
                'project_id' => $projectId,
                'category_id' => $service
            );
            Project_service::create($arr);
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
