<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProjectPriority;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    function getProjectPriorities($projectId){
        // $services = Project_service::where('project_id', '=', $projectId)->get();
        $priorities = DB::table('project_priorities')
        ->join('priorities', 'project_priorities.priority_id', '=', 'priorities.id')
        ->select('priorities.*', 'project_priorities.sort')
        ->where('project_priorities.project_id', '=', $projectId)
        ->orderBy('project_priorities.sort', 'asc')
        ->get();
        return $priorities;

    }
}
