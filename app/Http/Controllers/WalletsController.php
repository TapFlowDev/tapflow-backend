<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\wallet;
use App\Models\Group;
use App\Models\Freelancer;
use Exception;
use Illuminate\Support\Facades\Validator;

class WalletsController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        $rules=array(
            'reference_id'=>"required|unique:wallets,reference_id",
            'type'=>"required"
        );
        $validator=Validator::make($req->all(),$rules);
        if($validator->fails())
        {
            $response=Controller::returnResponse(101,'Validation Error',$validator->errors());
            return(json_encode($response));
        }
        else{
            try{
                $type=$req->type;
                $reference_id=$req->reference_id;
            if($type ==1)
            {
                $group=Group::where('id',$reference_id)->first();
                if($group === null)
                {
                    $response=Controller::returnResponse(101,'Group id does not exists',[]);
                    return(json_encode($response));
                }
                else
                {
                    $wallet=wallet::create($req->all());
                    $responseData=array(
                        "wallet_id"=>$wallet->id,
                        "reference_id"=>$wallet->reference_id,
                        );
                    $response=Controller::returnResponse(200,'Wallet created successfully',$responseData);
                    return(json_encode($response));
                }
            }
            elseif($type == 2)
            {
                $freelancer=Freelancer::where('user_id',$reference_id)->first();
                if($freelancer === null)
                {
                    $response=Controller::returnResponse(101,'User id does not exists',[]);
                    return(json_encode($response));
                }
                else
                {
                    $wallet=wallet::create($req->all());
                    $responseData=array(
                    "wallet_id"=>$wallet->id,
                    "reference_id"=>$wallet->reference_id,
                    );
                    $response=Controller::returnResponse(200,'Wallet created successfully',$responseData);
                    return(json_encode($response));
                }
            }
            }catch(Exception $error)
            {
                $response=Controller::returnResponse(500,'Something Wrong ',$error);
                return(json_encode($response));
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
