<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FormOptionsController;
use App\Mail\ApproveClient;
use App\Models\Clients_requests;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
            $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
            return json_encode($response);
        }
        try {
            $formOptionsControllerObj = new FormOptionsController;
            $checkEmailInUsers = User::where('email', '=', $req->email)->get()->first();
            if (isset($checkEmailInUsers->email)) {
                $response = Controller::returnResponse(200, 'You arleady registerd', []);
                return json_encode($response);
            }
            $checkEmailInRequests = Clients_requests::where('email', '=', $req->email)->get()->first();
            if (isset($checkEmailInRequests->email)) {
                $response = Controller::returnResponse(200, 'Our Team will contact you as soon as possible', []);
                return json_encode($response);
            }
            // return $checkEmailInUsers;
            $data = array();
            $data['email'] = $req->email;
            $data['name'] = $req->name;
            $data['answers'] = serialize($req->except('name', 'email'));
            $clientRequest = Clients_requests::create($data);
            //set email
            $arrayQuestions = array();
            $questions = $req->except('name', 'email');
            $counter = 0;
            foreach ($questions as $keyId => $valQ) {
                $label = $formOptionsControllerObj->getLabelById((int)$keyId);
                if ($label != '') {
                    $arrayQuestions[$counter]['label'] = $label;
                    $arrayQuestions[$counter]['answer'] = $valQ;
                }
                $counter++;
            }
            $details = [
                'subject'=> "Client Request(". $req->name .")",
                'name' => $req->name,
                'email' => $req->email,
                'questions' => $arrayQuestions
            ];
            // return $details;
            $response = Controller::returnResponse(200, 'Thanks for registering our team will contact you as soon as possible', []);
            Mail::to('hamzahshajrawi@gmail.com')->send(new ApproveClient($details));
            // Mail::to('abed@tapflow.app')->send(new ApproveClient($details));
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
