<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\hire_developer_proposals;
use App\Models\Project;
use App\Models\Proposal_requirement;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HireDeveloperProposalsController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        // $response = Controller::returnResponse(500, "Validation Error", $req->all());
        // return (json_encode($response));
        $userData = $this->checkUser($req);
        $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 1;
        if (!$condtion) {
            $response = Controller::returnResponse(401, "Unauthrized", []);
            return (json_encode($response));
        }
        try {
            $rules = array(
                "project_id" => "required",
                "requirements" => "required",
            );
            $validators = Validator::make($req->all(), $rules);
            if ($validators->fails()) {
                $responseData = $validators->errors();
                $response = Controller::returnResponse(101, "Validation Error", $responseData);
                return (json_encode($response));
            }

            $requirementObj = new Requirement;
            $projectId = $req->project_id;
            $teamId = $userData['group_id'];
            $userId = $userData['user_id'];
            $requirements = $req->requirements;

            $project = Project::where('id', '=', $projectId)->first();
            if (!$project) {
                $response = Controller::returnResponse(422, 'Project does not exsist', []);
                return (json_encode($response));
            }
            if ($project->type != 3) {
                $response = Controller::returnResponse(422, 'this function not possible for this type of projects', []);
                return (json_encode($response));
            }

            // $proposalDoesExsist = Proposal_requirement::where('project_id', '=', $projectId)->where('team_id', '=', $teamId)->first();
            // if ($proposalDoesExsist) {
            //     $response = Controller::returnResponse(422, 'You already applied to this project', ["propsal" => $proposalDoesExsist]);
            //     return (json_encode($response));
            // }

            /**
             * check requirements ids if valid
             */
            // $projectRequirements = $requirementObj->getRequirementsAndHourlyRateByProjectId($projectId);
            // $projectRequirements = $projectRequirements->toArray();
            // $projectRequirementsIds = array_column($projectRequirements, 'requirementId');
            // $requirementsIds = array_column($requirements, 'requirementId');
            // if ($requirementsIds != $projectRequirementsIds) {
            //     $response = Controller::returnResponse(101, 'Requirments not right', []);
            //     return (json_encode($response));
            // }


            $proposalArr = array(
                'team_id' => $teamId,
                'user_id' => $userId,
                'project_id' => $projectId,
                'details' => $req->our_offer,
            );
            // $proposal = hire_developer_proposals::create($proposalArr);
            foreach ($requirements as $keyRequ => $valRequ) {
                $requirementArr = array(
                    'proposal_id' => 10,
                    'requirement_id' => $keyRequ,
                    'hourly_rate' => $valRequ,
                );
                return $requirementArr;
                // Proposal_requirement::create($requirementArr);
            }
            // $responseData = array("proposal_id" => $proposal->id);
            // $response = Controller::returnResponse(200, "proposal added successfully", $responseData);
            /** 
             * send email
             */
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "there is an error", $error->getMessage());
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
