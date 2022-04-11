<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Billing_info;
use App\Models\withdrawl_request;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WithdrawlRequestController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        try {
            $walletObj = new WalletsController;
            $userData = $this->checkUser($req);
            $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1;
            if (!$condtion) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            $rules = array(
                "amount" => "required|numeric|gt:0",
                "billing_info_id" => "required|exists:billing_infos,id",
            );
            $validator = Validator::make($req->all(), $rules);
            if ($validator->fails()) {
                $responseData = $validator->errors();
                $response = Controller::returnResponse(101, "Validation Error", $responseData);
                return (json_encode($response));
            }

            $biilingInfo = Billing_info::where('id', '=', $req->billing_info_id)->get()->first();
            if ($biilingInfo->group_id != $userData['group_id']) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }

            $wallet = $walletObj->getOrCreateWallet($userData['group_id'], 1);
            $amount = number_format($req->amount, 2, '.', '');
            if ($amount > $wallet->balance) {
                $response = Controller::returnResponse(101, "Validation Error", array('amount' => array('withdraw amount must be less or equal to your wallet balance')));
                return (json_encode($response));
            }
            
            $withdrawlArray = array(
                'user_id' => $userData['user_id'],
                'group_id' => $userData['group_id'],
                'billing_info_id' => $req->billing_info_id,
                'amount' => $amount,
                'type' => 1,
            );
            $withdrawl = withdrawl_request::create($withdrawlArray);
            $response = Controller::returnResponse(200, "Withdraw requested successfully, our team will make your transfer as soon as possible", []);
            return (json_encode($response));
        } catch (Exception $error) {
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
}
