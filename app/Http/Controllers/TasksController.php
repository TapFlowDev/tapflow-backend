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
                dd($task['assignedTo']);
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
    function getTaskByMilestoneId($id)
    {  $AssignedTo=new AssignedToController;
        $tasks=task::select('id','name','description')->where('milestone_id',$id)->get();
        $tasks_details=[];
        
        foreach ($tasks as $task)
        {
            
           $assigned= $AssignedTo->getAssignedByTaskId($task->id);
           array_push($tasks_details,array(
            "task_id"=>$task->id,
            "task_name"=>$task->name,
            "task_description"=>$task->description,
            "assigned"=>($assigned),
           
        ));
        }
        return  ($tasks_details);
    }
    function updateTasksByMileStoneId($data,$milestone_id)
    {
        $del_tasks=task::where('milestone_id',$milestone_id)->delete();
        
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
}
