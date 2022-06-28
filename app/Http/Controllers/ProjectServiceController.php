<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Project_service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    function getProjectServices($projectId){
        // $services = Project_service::where('project_id', '=', $projectId)->get();
        $services = DB::table('project_services')
        ->join('categories', 'project_services.category_id', '=', 'categories.id')
        ->select('categories.*')
        ->where('project_services.project_id', '=', $projectId)
        ->get();
        return $services;

    }
}
