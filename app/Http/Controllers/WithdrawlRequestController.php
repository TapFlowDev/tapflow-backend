<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Billing_info;
use App\Models\User;
use App\Models\withdrawl_request;
use DateTime;
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
            $latestWithdrawlRequest = withdrawl_request::select('created_at')->where('group_id', '=', $userData['group_id'])->latest()->first();
            if ($latestWithdrawlRequest) {
               $latestWithdrawlRequestArr = $latestWithdrawlRequest->toArray();
                $isAccepted = $this->timeDiff(date('Y-m-d H:i:s', strtotime($latestWithdrawlRequestArr['created_at'])));
                if ($isAccepted != 1) {
                    $response = Controller::returnResponse(422, "Action denied, You must wait 30 mintutes for your next withdrawal request", []);
                    return (json_encode($response));
                }
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
            $fenLink = "/a-user/main/billing/0/withdraw";
            Controller::sendNotification($userData['group_id'], 'Payment', 'You have successfully requested a withdrawal', $fenLink, 2,'withdrawl_requests',$withdrawl->id);
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
    function getWithdrawlRequests(Request $req, $offset, $limit)
    {
        try {
            $page = ($offset - 1) * $limit;
            $userData = $this->checkUser($req);
            $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1;
            if (!$condtion) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            $withdrawlRequests = $this->getWithdrawlData(withdrawl_request::where('group_id', '=', $userData['group_id'])->latest()->offset($page)->limit($limit)->get()->makeHidden(['wallet_transactiond_id', 'type', 'user_id']));
            $withdrawlRequestsCounter = $this->getWithdrawlData(withdrawl_request::where('group_id', '=', $userData['group_id']))->count();
            $responseData=array('allData'=>$withdrawlRequests,'counter'=>$withdrawlRequestsCounter);
            $response = Controller::returnResponse(200, "data found", $responseData);
            return (json_encode($response));
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    private function getWithdrawlData($array)
    {
        foreach ($array as $keyWithdrawl => &$withdrawl) {
            $billingInfo = Billing_info::where('id', '=', $withdrawl->billing_info_id)->get()->first()->toArray();
            $withdrawl->billingInfo = $billingInfo;
            $user = User::where('id', '=', $withdrawl->user_id)->get()->first();
            $withdrawl->admin_name = $user->first_name . " " . $user->last_name;
            if($withdrawl->invoice){
                $withdrawl->invoice = asset('images/invoices/' . $withdrawl->invoice);
            }
        }
        return $array;
    }
    private function timeDiff($latest)
    {
        // dd($latest);
        $now = new DateTime();
        $diff = $now->diff(new DateTime($latest));
        $minutes =  $diff->days * 24 * 60;
        $minutes += $diff->h * 60;
        $minutes += $diff->i;
        if ($minutes < 30) {
            return 0;
        } else {
            return 1;
        }
    }
}
