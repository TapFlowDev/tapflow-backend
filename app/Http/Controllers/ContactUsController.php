<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact_Us;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Mail\ContactUs;
use Illuminate\Support\Facades\Mail;

class ContactUsController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        $rules = array(
            'name' => 'required|max:255',
            'email' => 'required|email',
            'message' => 'required|max:255',
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
            return (json_encode($response));
        } else {
            try {
                $query = Contact_Us::create($req->all());
                $details = [
                    'name' => $req->name,
                    'email' => $req->email,
                    'message' => $req->message,
                ];
                Mail::to("noreply@tapflow.app")->send(new ContactUs($details));
                $response = Controller::returnResponse(200, 'successful', []);
                return (json_encode($response));
            } catch (Exception $error) {
                $response = Controller::returnResponse(500, 'something went wrong', $error);
                return (json_encode($response));
            }
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
