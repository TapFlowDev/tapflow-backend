<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assigned_task;
use Faker\Provider\ar_JO\Person;
use Illuminate\Support\Facades\DB;
use Exception;
class AssignedToController extends Controller
{
    //add row 
    function Insert($data, $task_id)
    {
        try{
        foreach ($data as $a) {
            $arr = array(
                "user_id" => $a['user_id'],
                "task_id" => $task_id
            );
            $assigned = Assigned_task::create($arr);
        }
        return ['code'=>200 , 'msg'=>'successful'];
    }catch(Exception $error)
    {
        return ['code'=>500 ,'msg'=>'assigned to controller: '.$error->getMessage()];
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
        $assigned = Assigned_task::where('task_id', $id)->get();
        
        if ($assigned != '') {
            foreach ($assigned as $user) {
              
                $userData = DB::table('users')
                ->join('freelancers','users.id', '=', 'freelancers.user_id' )
                ->where('users.id','=',$user->user_id)
                ->select('users.id', 'users.first_name', 'users.last_name', 'users.role', 'freelancers.image')
                ->first();
                if(isset($userData))
                {               
                 $user->first_name = $userData->first_name;
                $user->last_name = $userData->last_name;
                $user->role = $userData->role;
                $user->image = $userData->image;
                if (isset($userData->image)) {
                    $user->image =  asset('images/users/' . $userData->image);
                } else {
                    $user->image  = asset('images/profile-pic.jpg');
                }
            }
            }
        }
        return ($assigned);
    }
}
