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
use Illuminate\Support\Facades\Mail;
use App\Mail\SubmitHireDeveloper;
use App\Models\Category;
use App\Models\Countries;
use App\Models\Project;
use App\Models\User;

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

            if (!($userData['exist'] == 1 && $userData['privileges'] == 1  && $userData['verified'] == 1)) {
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
                $contract = hire_developer_final_proposal::where('id', $req->contract_id)->select('user_id', 'proposal_id', 'team_id')->first();
                $project_id = hire_developer_proposals::where('id', $contract->proposal_id)->select('project_id')->first()->project_id;
                $clientObj = new ClientController;
                $projectObj = new ProjectController;
                $companyAdmin = json_decode($clientObj->get_client_info($contract->user_id))->data;
                $adminName = $companyAdmin->first_name . ' ' . $companyAdmin->last_name;
                $project_info = json_decode($projectObj->getProject($project_id))->data;
                $details = [
                    "subject" => 'Contract Submitted ',
                    "name" => $adminName,
                    "project_id" =>  $project_info->id,
                    "project_name" =>  $project_info->name,
                ];
                Mail::mailer('smtp2')->to('barbarawiahmad07@gmail.com')->send(new HireDeveloperActions($details));
                // Mail::mailer('smtp2')->to($companyAdmin->email)->send(new HireDeveloperActions($details));
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

            if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 2)) {
                $response = Controller::returnResponse(401, "unauthorized", []);
                return (json_encode($response));
            } else {
                $projectObj = new ProjectController;
                // hire_developer_proposals::where('id', $req->contract_id)->update(['status' => 1]);
                $contract = hire_developer_final_proposal::where('id', $req->contract_id)->select('user_id', 'proposal_id', 'team_id')->first();
                $project_id = hire_developer_proposals::where('id', $contract->proposal_id)->select('project_id')->first()->project_id;
                $freelancerObj = new FreeLancerController;
                $agencyAdmin = json_decode($freelancerObj->get_freelancer_info($contract->user_id))->data;
                $adminName = $agencyAdmin->first_name . ' ' . $agencyAdmin->last_name;
                $project_info = json_decode($projectObj->getProject($project_id))->data;

                $details = [
                    "subject" => 'Your FinalProposal Has Been Rejected',
                    "name" => $adminName,
                    "project_id" =>  $project_info->id,
                    "project_name" =>  $project_info->name,
                    "type" => 1

                ];

                Mail::mailer('smtp2')->to('barbarawiahmad07@gmail.com')->send(new HireDeveloperActions($details));
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

            if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 2)) {
                $response = Controller::returnResponse(401, "unauthorized", []);
                return (json_encode($response));
            } else {
                $projectObj = new ProjectController;
                hire_developer_proposals::where('id', $req->contract_id)->update(['status' => 2]);

                $contract = hire_developer_final_proposal::where('id', $req->contract_id)->select('user_id', 'proposal_id', 'team_id')->first();
                $project_id = hire_developer_proposals::where('id', $contract->proposal_id)->select('project_id')->first()->project_id;
                $freelancerObj = new FreeLancerController;
                $agencyAdmin = json_decode($freelancerObj->get_freelancer_info($contract->user_id))->data;
                $adminName = $agencyAdmin->first_name . ' ' . $agencyAdmin->last_name;
                $project_info = json_decode($projectObj->getProject($project_id))->data;

                $details = [
                    "subject" => 'Your FinalProposal Has Been Rejected',
                    "name" => $adminName,
                    "project_id" =>  $project_info->id,
                    "project_name" =>  $project_info->name,
                    "type" => 2

                ];

                Mail::mailer('smtp2')->to('barbarawiahmad07@gmail.com')->send(new HireDeveloperActions($details));
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

            if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 2)) {
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

            if (!($userData['exist'] == 1 && $userData['privileges'] == 1  && $userData['verified'] == 1)) {
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
        // $conditionArray[] = ['status', '=', 1];
        if ($teamId > 0) {
            $conditionArray[] = ['team_id', '=', $teamId];
            $contracts = hire_developer_final_proposal::whereIn('proposal_id', $proposalIds)->where($conditionArray)->select('*')->get();
            $contractsData = $this->getContractsResources($contracts)->first();
            $returnData = ($contractsData ? $contractsData : []);
        } else {
            $contracts = hire_developer_final_proposal::whereIn('proposal_id', $proposalIds)->select('*')->get();
            $contractsCount = hire_developer_final_proposal::whereIn('proposal_id', $proposalIds)->select('*')->count();
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
        $teamControllersObj = new TeamController;
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
            $teamInfo = $teamControllersObj->get_team_info($contract->team_id);
            $userInfo = User::find($contract->user_id);
            $teamCountry = Countries::find($teamInfo->country);
            $teamArr = array(
                'teamName' => $teamInfo->name,
                'teamAdminName' => $userInfo->name,
                'teamCountry' => ($teamCountry ? $teamCountry->name : "unset"),
                'teamFlag' => ($teamCountry ? $teamCountry->flag : ""),
            );
            $contract->teamInfo = $teamArr;
            $notice_period = Category::find((int)$contract->notice_period);
            $resource_replacement = Category::find((int)$contract->resource_replacement);
            $trail_period = Category::find((int)$contract->trail_period);
            $payment_settlement = Category::find((int)$contract->payment_settlement);
            $contract->notice_period = ($notice_period ? $notice_period->name : "unset");
            $contract->resource_replacement = ($notice_period ? $resource_replacement->name : "unset");
            $contract->trail_period = ($notice_period ? $trail_period->name : "unset");
            $contract->payment_settlement = ($notice_period ? $payment_settlement->name : "unset");
        }
        return $contracts;
    }
    function checkIfExists($proposal_id, $team_id)
    {
        $final_proposal = hire_developer_final_proposal::select('id', 'type', 'status')
            ->where('team_id', '=', $team_id)
            ->where('proposal_id', '=', $proposal_id)
            ->first();
        if ($final_proposal == null) {
            return ['exist' => 0, 'status' => 0];
        } else {
            return ['exist' => 1, "final_proposal_id" => $final_proposal->id, 'type' => (int)$final_proposal->type, 'status' => $final_proposal->status];
        }
    }
    function getContractWithResourcesClient(Request $req, $contractId)
    {
        try {
            $userData = Controller::checkUser($req);

            if (!($userData['exist'] == 1 && $userData['privileges'] == 1)) {
                $response = Controller::returnResponse(401, "unauthorized", []);
                return (json_encode($response));
            } else {
                $contracts = hire_developer_final_proposal::where('id', $contractId)->select('*')->get();
                $contract = $contracts->first();
                $proposal = hire_developer_proposals::select('*')->where('id', '=', $contract->proposal_id)->first();
                if (!$proposal) {
                    $response = Controller::returnResponse(401, "unauthorized 1", []);
                    return (json_encode($response));
                }
                $project = Project::where('id', '=', $proposal->project_id)->where('company_id', '=', $userData['group_id'])->first();
                // dd($proposal);
                if (!$project) {
                    $response = Controller::returnResponse(401, "unauthorized 2", []);
                    return (json_encode($response));
                }
                // $resourcesObj = new ResourcesController;
                // $resources = $resourcesObj->getContractResourcesById($contractId);
                // foreach ($resources as $resource) {
                //     if ($resource->image != '') {
                //         $image = asset('images/users/' . $resource->image);
                //         $resource->image = $image;
                //     } else {
                //         $resource->image = asset('images/profile-pic.jpg');
                //     }
                //     if ($resource->end_date === null) {

                //         $resource->end_date = 'Open';
                //     }
                // }
                // $contract->resources = $resources;
                $contractsData = $this->getContractsResources($contracts)->first();

                $response = Controller::returnResponse(200, "successful", $contractsData);
                return (json_encode($response));
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
}
