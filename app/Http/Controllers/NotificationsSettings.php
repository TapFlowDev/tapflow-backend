<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ProjectApply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class NotificationsSettings extends Controller
{
    //add row 
    function Insert(Request $req)
    {

    }
    //update row according to row id
    function Update($id)
    {

    }
    //delete row according to row id
    function Delete($id)
    {

    }
    function test(){
        $user = User::find(3);
        // $user->notify(new ProjectApply());
        Notification::send($user, new ProjectApply());
    }
}
