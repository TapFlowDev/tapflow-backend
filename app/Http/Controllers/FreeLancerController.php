<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Freelancer;  

class FreeLancerController extends Controller
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

    function updateTeamId($userId, $teamId){
        Freelancer::where('user_id', $userId)->update(['team_id'=>$teamId]);
    }
}
 