<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\task;
use App\Models\Assigned_task;
use App\Http\Controllers\AssignedToController;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

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
                $AssignedTo->Insert($task['assignees'],$tasks->id);
               
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
            "name"=>$task->name,
            "description"=>$task->description,
            "assignees"=>($assigned),
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
    function deleteTasksByMilestoneId($id)
    {   
        $assignedToObj=new AssignedToController;
         
        // $tasks_ids=DB::table('tasks')
        // ->where('milestone_id','=',$id)
        // ->select('id')
        // ->get();
        $tasks_ids=task::where('milestone_id','=',$id)
        ->select('id')
        ->get();
        
       if($tasks_ids->isEmpty())
       {
           
       }
       else{   
           foreach($tasks_ids as $task_id)

        {    
            // dd($task_id->id);
            Assigned_task::where('task_id', $task_id->id)->delete();
           task::where('id',$task_id->id)->delete();
        }
       }
     
    }
}
