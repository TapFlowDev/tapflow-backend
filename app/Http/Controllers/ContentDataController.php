<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\static_data;
use Exception;

class ContentDataController extends Controller
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
    function getDemoLink()
    {
        try {
            $link = static_data::where('id', 1)->select('link')->first();
            $link = $link->link;
            $response = Controller::returnResponse(200, 'success', $link);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'something went wrong', $error->getMessage());
            return json_encode($response);
        }
    }
    function getTerms()
    {
        try {
            $data = static_data::where('id', 2)->select('big_text')->first();
            $big_text = $data->big_text;
            $response = Controller::returnResponse(200, 'success', $big_text);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'something went wrong', $error->getMessage());
            return json_encode($response);
        }
    }
}
