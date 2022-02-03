<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assigned_task;
use Faker\Provider\ar_JO\Person;

class AssignedToController extends Controller
{
    //add row 
    function Insert($data,$task_id)
    
    
    {  
        
      
         $t=new Assigned_task;
     
        foreach($data as $a)
        {
            $arr=array(
                "user_id"=>$a,
                "task_id"=>$task_id

            );
           $ass=Assigned_task::create($arr);
         
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
    function getAssignedByTaskId($id)
    {
        $assigned =Assigned_task::where('task_id',$id)->get();
        return ( $assigned);
    }
}
