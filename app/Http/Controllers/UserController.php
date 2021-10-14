<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
// use App\Models\Freelancer;
// use App\Models\Company;
// user types 0,1 0:freelancer 1:client
class UserController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        $user=new User;
        $user_type=$req->user_type;
        
        $user->email=$req->email;
            $user->password=$req->password;
            $user->type=$req->user_type;
            $user->save();
            $user_id=$user->id;
        if($user_type == '0')
        {   
            $freelancer=new Freelancer;
            $freelancer->user_id=$user_id;
            $freelancer->


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
