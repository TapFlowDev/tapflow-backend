<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Clients_requests;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ClientsRequestsController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        $rules = array(
            "name" => "required|max:255",
            "email" => "required|max:255"
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $response = Controller::returnResponse(101, 'Validation Error', ['error' => $validator->errors(), 'req'=>$req->all()]);
            return json_encode($response);
        }
        try {
            $checkEmailInUsers = User::where('email', '=', $req->email)->get()->first();
            if (isset($checkEmailInUsers->email)) {
                $response = Controller::returnResponse(200, 'You arleady registerd!', []);
                return json_encode($response);
            }
            $checkEmailInRequests = Clients_requests::where('email', '=', $req->email)->get()->first();
            if (isset($checkEmailInRequests->email)) {
                $response = Controller::returnResponse(200, 'Our Team will contact you soon', []);
                return json_encode($response);
            }
            // return $checkEmailInUsers;
            $data = array();
            $data['email'] = $req->email;
            $data['name'] = $req->name;
            $data['answers'] = serialize($req->except('name', 'email'));
            $clientRequest = Clients_requests::create($data);
            $response = Controller::returnResponse(200, 'Thanks to register our team will contact you soon', []);
            return json_encode($response);
        } catch (\Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error->getMessage());
            return json_encode($response);
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
