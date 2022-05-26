<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProjectPriority;
use Exception;
use Illuminate\Http\Request;

class ProjectPriorityController extends Controller
{
    //add row 
    function Insert($projectId, $priorities)
    {
        try {

            foreach ($priorities as $keyP => $priority) {
                $arr = array(
                    'project_id' => $projectId,
                    'priority_id' => $priority['id'],
                    'sort' => $keyP + 1,
                );
                ProjectPriority::create($arr);
            }
            return ['status' => 200, 'msg' => "added successfully"];
        } catch (Exception $error) {

            return ['status' => 500, 'msg' => $error->getMessage()];
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
