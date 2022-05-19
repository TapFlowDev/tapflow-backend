<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Priority;
use Exception;
use Illuminate\Http\Request;

class PriorityController extends Controller
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
    function getPriorities()
    {
        try {
            $priorities = Priority::all()->makeHidden(['score', 'created_at', 'updated_at']);
            $response = Controller::returnResponse(200, 'data found', $priorities);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error);
            return json_encode($response);
        }
    }
}
