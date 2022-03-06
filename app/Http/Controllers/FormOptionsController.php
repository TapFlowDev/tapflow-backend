<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Form_options;
use Illuminate\Http\Request;

class FormOptionsController extends Controller
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
    function getData()
    {
        try {
            $options = Form_options::select('id', 'label', 'field_type', 'required')->get();
            $response = Controller::returnResponse(200, 'data found', $options);
            return json_encode($response);
        } catch (\Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error->getMessage());
            return json_encode($response);
        }
    }
}
