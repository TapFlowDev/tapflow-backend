<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\task;
use App\Http\Controllers\AssignedToController;
class TasksController extends Controller
{
    //add row 
    function Insert($data,$milestone_id)
    {       
            $AssignedTo=new AssignedToController;
            foreach($data as $task)
            { 
                $arr=array(
                "milestone_id"=>$milestone_id,
                "name"=>$task['name'],
                "description"=>$task['description'],
            );
                $tasks=task::create($arr);
               
                $AssignedTo->Insert($task['assignedTo'],$tasks->id);
               
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
