<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Waiting_List;
class WaitingListController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        $rules = array(
           
            "email" => "email|required|max:255",
       
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {

            $responseData = $validator->errors();
            $response = Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        }
        else
        {
            $waiting=Waiting_List::create($req->all());
            $response = Controller::returnResponse(200, "successful", []);
            return (json_encode($response));
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
