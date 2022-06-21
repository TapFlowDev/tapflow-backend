<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Requirement;
use App\Models\hire_developer_final_proposal;
use App\Models\hire_developer_proposals;
use Exception;

class HireDeveloperFinalProposalController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);

            if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1 && $userData['verified'] == 1)) {
                $response = Controller::returnResponse(401, "unauthorized", []);
                return (json_encode($response));
            } else {
                $rules = array(
                    "proposal_id" => "required",
                );
                $validators = Validator::make($req->all(), $rules);
                if ($validators->fails()) {
                    $responseData = $validators->errors();
                    $response = Controller::returnResponse(101, "Validation Error", $responseData);
                    return (json_encode($response));
                }
                $team_id = hire_developer_proposals::where('id', $req->proposal_id)->select('team_id')->first()->team_id;
                if ($team_id != $userData['group_id']) {
                    $response = Controller::returnResponse(401, "unauthorized", []);
                    return (json_encode($response));
                }

                $requirementObj = new Requirement;
                $resourcesObj = new ResourcesController;
                $data = array("proposal_id" => $req->proposal_id, "team_id" => $userData['group_id'], "user_id" => $userData['user_id']);
                $contract = hire_developer_final_proposal::create($data);
                $resources = $requirementObj->getResourcesByProposalId($req->proposal_id);
                $resourcesObj->internalAdd($resources, $contract->id);

                $response = Controller::returnResponse(200, "successful", []);
                return (json_encode($response));
            }
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
    function save(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);

            if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1 && $userData['verified'] == 1)) {
                $response = Controller::returnResponse(401, "unauthorized", []);
                return (json_encode($response));
            } else {
                $team_id = hire_developer_final_proposal::where('id', $req->contract_id)->select('team_id')->first()->team_id;
                if ($team_id != $userData['group_id']) {
                    $response = Controller::returnResponse(401, "unauthorized", []);
                    return (json_encode($response));
                }
                $contract = hire_developer_final_proposal::where('id', $req->contract_id)->update([
                    'starting_date' => $req->starting_date,
                    'notice_period' => $req->notice_period,
                    'resource_replacement' => $req->resource_replacement,
                    'trail_period' => $req->trail_period,
                    'payment_settlement' => $req->payment_settlement,
                    'additional_terms' => $req->additional_terms,
                    'default_terms' => $req->default_terms,
                ]);
                $response = Controller::returnResponse(200, "data updated successfully", []);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function getContract(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);

            if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1 && $userData['verified'] == 1)) {
                $response = Controller::returnResponse(401, "unauthorized", []);
                return (json_encode($response));
            } else {
                $contract = hire_developer_final_proposal::where('id', $req->contract_id)->select('*')->first();
                if ($contract->team_id != $userData['group_id']) {
                    $response = Controller::returnResponse(401, "unauthorized", []);
                    return (json_encode($response));
                }
                $response = Controller::returnResponse(200, "successful",$contract);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
}
