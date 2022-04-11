<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\deposit_request;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepositRequestController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        try{
        $userData = $this->checkUser($req);
        $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 2;
        if (!$condtion) {
            $response = Controller::returnResponse(422, 'unauthrized', []);
            return json_encode($response);
        }
        $rules = array(
            "amount" => "required|numeric",
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $responseData = $validator->errors();
            $response = Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        }
        $referenceNumber = $this->generateRefrenceNumber();
        $deposit = array(
            'company_id' => $userData['group_id'],
            'reference_number' => $referenceNumber,
            'amount' => $req->amount
        );
        $depositRequest = deposit_request::create($deposit);
        $response = Controller::returnResponse(200, "added successfully", $depositRequest);
        return (json_encode($response));
    }catch(Exception $error){
        $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
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
    private function generateRefrenceNumber()
    {
        $code = rand(100000000,999999999);
        if (deposit_request::get()->contains('reference_number', $code)) {
            $code = $this->generateRefrenceNumber();
        }
        return $code;
    }
    function getDeposits(Request $req, $offset, $limit)
    {
        try {
            $page = ($offset - 1) * $limit;
            $userData = $this->checkUser($req);
            $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 2;
            if (!$condtion) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            $deposits = deposit_request::where('company_id', '=', $userData['group_id'])->latest()->offset($page)->limit($limit)->get();
            $response = Controller::returnResponse(200, "data found", $deposits);
            return (json_encode($response));
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
}
