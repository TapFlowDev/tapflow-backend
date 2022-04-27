<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\wallets_transaction;
use App\Models\wallet;
use Exception;
use Illuminate\Support\Facades\Validator;


class WalletsTransactionsController extends Controller
{
    //add row 
    function deposit(Request $req)
    {
        $rule = array(
            'wallet_id' => "required|exists:wallets,id",
            'amount' => "required|min:1",
        );
        $validator = Validator::make($req->all(), $rule);
        if ($validator->fails()) {
            $response = Controller::returnResponse(101, "Validation error", $validator->errors());
            return (json_encode($response));
        } else {
            try {
                $type = 1;
                $transaction = wallets_transaction::create($req) + (['type' => $type]);
                $responseData = array(
                    'transaction_id' => $transaction->id,
                    'wallet_id' => $transaction->wallet_id,
                );
                $response = Controller::returnResponse(200, "Successful", $responseData);
                return (json_encode($response));
            } catch (Exception $error) {
                $response = Controller::returnResponse(500, "Something wrong", $error);
                return (json_encode($response));
            }
        }
    }
    function withdraw(Request $req)
    {
        $rule = array(
            'wallet_id' => "required|exists:wallets,id",
            'amount' => "required|min:1",
        );
        $validator = Validator::make($req->all(), $rule);
        if ($validator->fails()) {
            $response = Controller::returnResponse(101, "Validation error", $validator->errors());
            return (json_encode($response));
        } else {
            try {
                $type = 2;
                $transaction = wallets_transaction::create($req) + (['type' => $type]);
                $responseData = array(
                    'transaction_id' => $transaction->id,
                    'wallet_id' => $transaction->wallet_id,
                );
                $response = Controller::returnResponse(200, "Successful", $responseData);
                return (json_encode($response));
            } catch (Exception $error) {
                $response = Controller::returnResponse(500, "Something wrong", $error);
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
    function makePaymentTransactionWithdraw($payment)
    {
        try {
            $walletObj = new WalletsController;
            $wallet = $walletObj->getOrCreateWallet($payment->company_id, 1);
            $walletBalance = $wallet->balance;
            $total = $payment->total_price;
            $transactionData = array(
                'amount' => number_format($payment->total_price, 2, '.', ''),
                'type' => 2,
                'wallet_id' => $wallet->id,
                'payment_id' => $payment->id
            );
            $transaction = wallets_transaction::create($transactionData);
            if ($transaction) {
                if ($walletBalance < $total) {
                    $transaction->status = 0;
                    $transaction->save();
                    return ['paymentStatus' => 0, 'paymentMsg' => 'not enough balance', 'responseCode' => 422];
                } else {
                    $transaction->status = 1;
                    $transaction->save();
                    $newBalance = number_format($walletBalance - $total, 2, '.', '');
                    $wallet->balance = $newBalance;
                    $wallet->save();
                    //notify admin
                    $mail = $this->notifyAdminWalletAction($wallet->reference_id, 2, $total, $newBalance);
                    return ['paymentStatus' => 1, 'paymentMsg' => 'paid successfully',  'responseCode' => 200];
                }
            }
            return ['paymentStatus' => -1, 'paymentMsg' => 'error', 'responseCode' => 500];
        } catch (Exception $error) {
            return ['paymentStatus' => -1, 'paymentMsg' => $error->getMessage(), 'responseCode' => 500];
        }
    }

    function makePaymentTransactionDeposit($payment)
    {
        try {
            $walletObj = new WalletsController;
            $wallet = $walletObj->getOrCreateWallet($payment->agency_id, 1);
            $walletBalance = $wallet->balance;
            $total = $payment->agency_total_price;
            $transactionData = array(
                'amount' => $total,
                'type' => 1,
                'wallet_id' => $wallet->id,
                'payment_id' => $payment->id
            );
            $transaction = wallets_transaction::create($transactionData);
            if ($transaction) {
                $transaction->status = 1;
                $transaction->save();
                $newBalance = number_format($walletBalance + $total, 2, '.', '');
                $wallet->balance = $newBalance;
                $wallet->save();
                //notify admin
                $mail = $this->notifyAdminWalletAction($wallet->reference_id, 2, $total, $newBalance);
                return ['paymentStatus' => 1, 'paymentMsg' => 'paid successfully',  'responseCode' => 200];
            }
            return ['paymentStatus' => -1, 'paymentMsg' => 'error', 'responseCode' => 500];
        } catch (Exception $error) {
            return ['paymentStatus' => -1, 'paymentMsg' => 'error', 'responseCode' => 500];
        }
    }
    function getCompanyTransactions(Request $req, $offset, $limit)
    {
        try {
            $walletObj = new WalletsController;
            $page = ($offset - 1) * $limit;
            $userData = $this->checkUser($req);
            $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 2;
            if (!$condtion) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            $wallet = $walletObj->getOrCreateWallet($userData['group_id'], 1);
            $transactions = wallets_transaction::where('wallet_id', '=', $wallet->id)->orderBy('created_at', 'desc')->offset($page)->limit($limit)->get();
            $responseData = array(
                'walletInfo' => $wallet,
                'transactions' => $transactions
            );
            $response = Controller::returnResponse(200, "data found", $responseData);
            return (json_encode($response));
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function getAgencyTransactions(Request $req, $offset, $limit)
    {
        try {
            $walletObj = new WalletsController;
            $page = ($offset - 1) * $limit;
            $userData = $this->checkUser($req);
            $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1;
            if (!$condtion) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            $wallet = $walletObj->getOrCreateWallet($userData['group_id'], 1);
            $transactions = wallets_transaction::where('wallet_id', '=', $wallet->id)->orderBy('created_at', 'desc')->offset($page)->limit($limit)->get();
            $responseData = array(
                'walletInfo' => $wallet,
                'transactions' => $transactions
            );
            $response = Controller::returnResponse(200, "data found", $responseData);
            return (json_encode($response));
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
}
