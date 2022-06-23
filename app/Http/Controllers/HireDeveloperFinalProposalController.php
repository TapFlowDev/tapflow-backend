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
use App\Mail\HireDeveloperActions;
use App\Models\proposal;

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
                $contractExists = $this->checkIfExist($req->contract_id);
                if ($contractExists != 0) {
                    $response = Controller::returnResponse(422, "already exist", ['contract_id' => $contractExists]);
                    return (json_encode($response));
                }
                $proposal = hire_developer_proposals::where('id', $req->proposal_id)->select('team_id', 'status')->first();
                if ($proposal === null) {
                    $response = Controller::returnResponse(422, "please apply initial first", []);
                    return (json_encode($response));
                } else {
                    if ($proposal->team_id != $userData['group_id']) {
                        $response = Controller::returnResponse(401, "unauthorized", []);
                        return (json_encode($response));
                    } elseif ($proposal->status != 1) {
                        $response = Controller::returnResponse(422, "your initial proposal not accepted yest", []);
                        return (json_encode($response));
                    }

                    $requirementObj = new Requirement;
                    $resourcesObj = new ResourcesController;
                    $data = array("proposal_id" => $req->proposal_id, "team_id" => $userData['group_id'], "user_id" => $userData['user_id']);
                    $contract = hire_developer_final_proposal::create($data);
                    $resources = $requirementObj->getResourcesByProposalId($req->proposal_id);
                    $resourcesObj->internalAdd($resources, $contract->id);

                    $response = Controller::returnResponse(200, "successful", ["contract_id" => $contract->id]);
                    return (json_encode($response));
                }
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
                $response = Controller::returnResponse(200, "successful", $contract);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function checkIfExist($proposal_id)
    {
        $contract = hire_developer_final_proposal::where('proposal_id', $proposal_id)->select('*')->first();
        if ($contract === null) {
            return 0;
        } else {
            return $contract->id;
        }
    }
    function submitContract(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1 && $userData['verified'] == 1)) {
                $response = Controller::returnResponse(401, "unauthorized", []);
                return (json_encode($response));
            } else {
                $rules = array(
                    "contract_id" => "required",
                    'starting_date' => "required",
                    'notice_period' => "required",
                    'resource_replacement' => "required",
                    'trail_period' => "required",
                    'payment_settlement' => "required",
                    'default_terms' => "required",
                );
                $validators = Validator::make($req->all(), $rules);
                if ($validators->fails()) {
                    $responseData = $validators->errors();
                    $response = Controller::returnResponse(101, "Validation Error", $responseData);
                    return (json_encode($response));
                }
                $resourcesObj = new ResourcesController;
                $resources = $resourcesObj->getContractResourcesById($req->contract_id);
                $dates = $resources->pluck('starting_date')->toArray();
                $contractDate = hire_developer_final_proposal::where('id', $req->contract_id)->select('starting_date')->first()->starting_date;
                $dateValidation = $this->checkDates($dates, $contractDate);
                if ($dateValidation == 0) {
                    $response = Controller::returnResponse(422, "one of the resources starting date starts before the contract starts", []);
                    return (json_encode($response));
                }
                $contract = hire_developer_final_proposal::where('id', $req->contract_id)->update([
                    'starting_date' => $req->starting_date,
                    'notice_period' => $req->notice_period,
                    'resource_replacement' => $req->resource_replacement,
                    'trail_period' => $req->trail_period,
                    'payment_settlement' => $req->payment_settlement,
                    'default_terms' => $req->default_terms,
                    'status' => 0,
                ]);
                $response = Controller::returnResponse(200, "contract submitted successfully", []);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    private function checkDates($dates, $contractDate)
    {
        $validity = 1;
        foreach ($dates as $date) {
            if ($date < $contractDate) {
                $validity = 0;
            } else {
                continue;
            }
        }
        return $validity;
    }
    function acceptContract(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);

            if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1 && $userData['verified'] == 1)) {
                $response = Controller::returnResponse(401, "unauthorized", []);
                return (json_encode($response));
            } else {
                hire_developer_proposals::where('id', $req->contract_id)->update(['status' => 1]);
                $response = Controller::returnResponse(200, "successful", []);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function rejectContract(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);

            if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1 && $userData['verified'] == 1)) {
                $response = Controller::returnResponse(401, "unauthorized", []);
                return (json_encode($response));
            } else {
                hire_developer_proposals::where('id', $req->contract_id)->update(['status' => 2]);
                $contract = hire_developer_proposals::where('id', $req->contract_id)->select('user_id', 'proposal_id', 'team_id')->first();
                $proposal = hire_developer_proposals::where('id', $contract->proposal_id)->first();
                // $details = [
                //     "subject" => 'Your FinalProposal Has Been Accepted',
                //     "name" => $adminName,
                //     "project_id" =>  $projectInfo->id,
                //     "project_name" =>  $projectInfo->name,
                //     "type" => 1

                // ];
                // Mail::mailer('smtp2')->to($agencyAdmin->email)->send(new HireDeveloperActions($details));
                $response = Controller::returnResponse(200, "successful", []);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function reviewContract(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);

            if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1 && $userData['verified'] == 1)) {
                $response = Controller::returnResponse(401, "unauthorized", []);
                return (json_encode($response));
            } else {
                hire_developer_proposals::where('id', $req->contract_id)->update(['status' => 3]);
                $response = Controller::returnResponse(200, "successful", []);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function getContractWithResources(Request $req)
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
                $resourcesObj = new ResourcesController;
                $resources = $resourcesObj->getContractResourcesById($req->contract_id);
                foreach ($resources as $resource) {
                    if ($resource->image != '') {
                        $image = asset('images/users/' . $resource->image);
                        $resource->image = $image;
                    } else {
                        $resource->image = asset('images/profile-pic.jpg');
                    }
                    if ($resource->end_date === null) {

                        $resource->end_date = 'Open';
                    }
                }
                $contract->resources = $resources;
                $response = Controller::returnResponse(200, "successful", $contract);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function getContractData($proposalIds, $teamId = 0)
    {
        $conditionArray[] = ['status', '=', 1];
        if ($teamId > 0) {
            $conditionArray[] = ['team_id', '=', $teamId];
            $contracts = hire_developer_final_proposal::whereIn('proposal_id', $proposalIds)->where($conditionArray)->select('*')->get();
            return dd($contracts);
            $contractsData = $this->getContractsResources($contracts)->first();
            $returnData = $contractsData; 
        } else {
            $contracts = hire_developer_final_proposal::whereIn('proposal_id', $proposalIds)->where($conditionArray)->select('*')->get();
            $contractsCount = hire_developer_final_proposal::whereIn('proposal_id', $proposalIds)->where($conditionArray)->select('*')->count();
            $contractsData = $this->getContractsResources($contracts);
            $returnData = [
                'allData' => $contractsData,
                'count' => $contractsCount
            ];
        }
        return $returnData;
    }
    private function getContractsResources($contracts)
    {
        $resourcesObj = new ResourcesController;
        foreach ($contracts as &$contract) {
            $resources = $resourcesObj->getContractResourcesById($contract->id);
            foreach ($resources as $resource) {
                if ($resource->image != '') {
                    $image = asset('images/users/' . $resource->image);
                    $resource->image = $image;
                } else {
                    $resource->image = asset('images/profile-pic.jpg');
                }
                if ($resource->end_date === null) {

                    $resource->end_date = 'Open';
                }
            }
            $contract->resources = $resources;
        }
        return $contracts;
    }
}
