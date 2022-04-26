<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Billing_info;
use App\Models\Countries;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BillingInfoController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        try {
            $userData = $this->checkUser($req);
            $rules = array(
                "bank_name" => "required|max:255",
                "IBAN" => "required|unique:billing_infos,IBAN",
                "country" => "required|exists:countries,id",
                "legal_name" => "required|max:255",
                "phone" => "required|max:255",
                "legal_address" => "required|max:255",
                "building" => "max:255",
                "city" => "required|max:255",
                "region" => "required|max:255",
                "zip_code" => "required|max:255,numeric",
                "SWIFT" => "required|min:8,max:11",
            );
            $validator = Validator::make($req->all(), $rules);
            if ($validator->fails()) {
                $responseData = $validator->errors();
                $response = Controller::returnResponse(101, "Validation Error", $responseData);
                return (json_encode($response));
            }
            $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1;
            if (!$condtion) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            // if ($userData['group_id'] != $req->group_id) {
            //     $response = Controller::returnResponse(401, "Unauthrized", []);
            //     return (json_encode($response));
            // }
            /**
             * this code prevent user from making more than one billing info 
             * in case we need to let user has more than one billing info just comment 
             * if doesExsists code
             */
            $doesExists = Billing_info::where('group_id', '=', $userData['group_id'])->get()->first();
            if ($doesExists) {
                $response = Controller::returnResponse(101, "group already has billig info", []);
                return (json_encode($response));
            }
            //
            $billingInfoArray = array(
                'group_id' => $userData['group_id'],
                'bank_name' => $req->bank_name,
                'IBAN' => $req->IBAN,
                'country' => $req->country,
                'legal_name' => $req->legal_name,
                'phone' => $req->phone,
                'legal_address' => $req->legal_address,
                'building' => $req->building,
                'city' => $req->city,
                'region' => $req->region,
                'zip_code' => $req->zip_code,
                'SWIFT' => $req->SWIFT,
            );
            $billingInfo = Billing_info::create($billingInfoArray);
            $response = Controller::returnResponse(200, "billing info created successfully", $billingInfo);
            return (json_encode($response));
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    //update row according to row id
    function Update(Request $req, $id)
    {
        try {
            $userData = $this->checkUser($req);
            $rules = array(
                "bank_name" => "required|max:255",
                "IBAN" => "required",
                "country" => "required|exists:countries,id",
                "legal_name" => "required|max:255",
                "phone" => "required|max:255",
                "legal_address" => "required|max:255",
                "building" => "max:255",
                "city" => "required|max:255",
                "region" => "required|max:255",
                "zip_code" => "required|max:255,numeric",
                "SWIFT" => "required|min:8,max:11",
            );
            $validator = Validator::make($req->all(), $rules);
            if ($validator->fails()) {
                $responseData = $validator->errors();
                $response = Controller::returnResponse(101, "Validation Error", $responseData);
                return (json_encode($response));
            }
            $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1;
            if (!$condtion) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            // if ($userData['group_id'] != $req->group_id) {
            //     $response = Controller::returnResponse(401, "Unauthrized", []);
            //     return (json_encode($response));
            // }
            /**
             * this code prevent user from making more than one billing info 
             * in case we need to let user has more than one billing info just comment 
             * if doesExsists code
             */
            $doesIBANExsists = Billing_info::where('id', '<>', $id)->where('IBAN', '=', $req->IBAN)->get()->first();
            if ($doesIBANExsists) {
                $response = Controller::returnResponse(101, "Validation Error", array("IBAN" => array("The i b a n has already been taken.")));
                return (json_encode($response));
            }
            $billingInfo = Billing_info::where('id', '=', $id)->where('group_id', '=', $userData['group_id'])->get()->first();
            if (!$billingInfo) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            //
            $billingInfoUpdated = Billing_info::where('id', '=', $id)->where('group_id', '=', $userData['group_id'])
                ->update([
                    'bank_name' => $req->bank_name,
                    'IBAN' => $req->IBAN,
                    'country' => $req->country,
                    'legal_name' => $req->legal_name,
                    'phone' => $req->phone,
                    'legal_address' => $req->legal_address,
                    'building' => $req->building,
                    'city' => $req->city,
                    'region' => $req->region,
                    'zip_code' => $req->zip_code,
                    'SWIFT' => $req->SWIFT,
                ]);

            $response = Controller::returnResponse(200, "billing info updated successfully", $billingInfo);
            return (json_encode($response));
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    //delete row according to row id
    function Delete($id)
    {
    }
    function getBillingInfo(Request $request)
    {
        try {
            $userData = $this->checkUser($request);
            $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1;
            if (!$condtion) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            $billingInfo = Billing_info::where('group_id', '=', $userData['group_id'])->get();
            if (!$billingInfo) {
                $response = Controller::returnResponse(200, "data empty", []);
                return (json_encode($response));
            }
            foreach ($billingInfo as $keyB => &$info) {
                if ($info->country != '') {
                    $country = Countries::where('id', $info->country)->first();
                    $info->countryCode = $country->code;
                }
            }
            $response = Controller::returnResponse(200, "data found", $billingInfo);
            return (json_encode($response));
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
}
