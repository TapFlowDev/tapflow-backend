<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Assigned_task;
use Faker\Provider\ar_JO\Person;
use Illuminate\Support\Facades\DB;

class AssignedToController extends Controller
{
    //add row 
    function Insert($data, $task_id)


    {


        $t = new Assigned_task;

        foreach ($data as $a) {
            $arr = array(
                "user_id" => $a,
                "task_id" => $task_id

            );
            $ass = Assigned_task::create($arr);
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
            foreach ($assigned as &$user) {
                $userData = DB::table('users')->join('freelancers', 'users.id', '=', 'freelancers.user_id')
                    ->select('users.id', 'users.first_name', 'users.last_name', 'users.role', 'freelancers.image')
                    ->get()->first();
                $user->first_name = $userData->first_name;
                $user->last_name = $userData->last_name;
                if (isset($userData->image)) {
                    $user->image =  asset('images/users/' . $userData->image);
                } else {
                    $user->image  = asset('images/profile-pic.jpg');
                }
            }
        }
        return ($assigned);
    }
}
