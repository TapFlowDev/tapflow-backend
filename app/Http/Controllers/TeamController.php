<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;

class TeamController extends Controller
{
    //add row 
    function Insert($arr)
    {
        return Team::create($arr);
    }
    //update row according to row id
    function Update($id)
    {

    }
    //delete row according to row id
    function Delete($id)
    {

    }
    function updateFiles($id, $fileName, $columnName){
        Team::where('id', $id)->update(array($columnName => $fileName));

    }
}
