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
        $rule=array(
            'wallet_id'=>"required|exists:wallets,id",
            'amount'=>"required|min:1",
        );
        $validator=Validator::make($req->all(),$rule);
        if($validator->fails())
        {
            $response=Controller::returnResponse(101,"Validation error",$validator->errors());
            return (json_encode($response));
        }
        else
        {
            try
            {
                $type=1;
                $transaction=wallets_transaction::create($req)+(['type'=>$type]);
                $responseData=array(
                    'transaction_id'=>$transaction->id,
                    'wallet_id'=>$transaction->wallet_id,
                );   
                $response=Controller::returnResponse(200,"Successful",$responseData);
                return (json_encode($response));
                
            }catch(Exception $error)
            {
                $response=Controller::returnResponse(500,"Something wrong",$error);
                return (json_encode($response));
            }
        }
    }
    function withdraw(Request $req)
    {
        $rule=array(
            'wallet_id'=>"required|exists:wallets,id",
            'amount'=>"required|min:1",
        );
        $validator=Validator::make($req->all(),$rule);
        if($validator->fails())
        {
            $response=Controller::returnResponse(101,"Validation error",$validator->errors());
            return (json_encode($response));
        }
        else
        {
            try
            {
                $type=2;
                $transaction=wallets_transaction::create($req)+(['type'=>$type]);
                $responseData=array(
                    'transaction_id'=>$transaction->id,
                    'wallet_id'=>$transaction->wallet_id,
                );   
                $response=Controller::returnResponse(200,"Successful",$responseData);
                return (json_encode($response));
                
            }catch(Exception $error)
            {
                $response=Controller::returnResponse(500,"Something wrong",$error);
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