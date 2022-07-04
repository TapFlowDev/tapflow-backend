<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agency_resource;
use App\Models\Agency_resources_skill;
use App\Models\Candidate;
use App\Models\Category;
use App\Models\Countries;
use App\Models\hire_developer_proposals;
use App\Models\Project;
use App\Models\proposal;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CandidatesController extends Controller
{
    //add row 
    function Insert(Request $req, $id)
    {
        try {
            $userData = $this->checkUser($req);
            $condition = ($userData['privileges'] == 1 && $userData['group_id'] != '');
            if (!$condition) {
                $response = Controller::returnResponse(422, 'Action denied', []);
                return json_encode($response);
            }
            $rules = array(
                "candidates" => "required",
            );
            $validator = Validator::make($req->all(), $rules);
            if ($validator->fails()) {
                $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
                return json_encode($response);
            }
            $proposal = hire_developer_proposals::where('project_id', '=', $id)->where('team_id', '=', $userData['group_id'])->first();
            if (!$proposal) {
                $response = Controller::returnResponse(422, 'Project not found', []);
                return json_encode($response);
            }
            $candidates = $req->candidates;
            $candidatesIds = Agency_resource::select('id')->whereIn('id', $candidates)->where('team_id', '=', $userData['group_id'])->pluck('id')->toArray();
            if (count($candidatesIds) < 1) {
                $response = Controller::returnResponse(422, 'invalid candidates', []);
                return json_encode($response);
            }
            foreach ($candidatesIds as $candidate) {
                //check if not exist before add 
                $doesExist = Candidate::select('id')->where('proposal_id', '=', $proposal->id)->where('agency_resource_id', '=', $candidate)->first();
                if (!$doesExist) {
                    $candidateArr = [
                        'proposal_id' => $proposal->id,
                        'agency_resource_id' => $candidate,
                    ];
                    $candidate = Candidate::create($candidateArr);
                }
            }
            // return $candidateArr;
            $response = Controller::returnResponse(200, 'Candidates added successfully', []);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error->getMessage());
            return json_encode($response);
        }
    }
    //update row according to row id
    function Update($id)
    {
    }
    //delete row according to row id
    function Delete(Request $req)
    {
        try {
            $userData = $this->checkUser($req);
            $condition = ($userData['privileges'] == 1 && $userData['group_id'] != '');
            if (!$condition) {
                $response = Controller::returnResponse(422, 'Action denied', []);
                return json_encode($response);
            }
            $rules = array(
                "candidates" => "required",
            );
            $validator = Validator::make($req->all(), $rules);
            if ($validator->fails()) {
                $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
                return json_encode($response);
            }
            $candidates = $req->candidates;
            $candidatesIds = Agency_resource::select('id')->whereIn('id', $candidates)->where('team_id', '=', $userData['group_id'])->pluck('id')->toArray();
            if (count($candidatesIds) < 1) {
                $response = Controller::returnResponse(422, 'invalid candidates', []);
                return json_encode($response);
            }
            $candidate = Candidate::whereIn('agency_resource_id',$candidatesIds)->delete();
            // return $candidateArr;
            $response = Controller::returnResponse(200, 'Candidates deleted successfully', []);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error->getMessage());
            return json_encode($response);
        }
    }
    function getProjectCandidates(Request $req, $id)
    {
        try {
            $userData = $this->checkUser($req);
            $condition = ($userData['privileges'] == 1 && $userData['group_id'] != '');
            if (!$condition) {
                $response = Controller::returnResponse(422, 'Action denied', []);
                return json_encode($response);
            }
            $selectProjectCondtions[] = ['id', '=', $id];
            if ($userData['type'] == 2) {
                $selectProjectCondtions[] = ['company_id', '=', $userData['group_id']];
            }
            $project = Project::select('id')->where($selectProjectCondtions)->first();
            if (!$project) {
                $response = Controller::returnResponse(401, 'Action denied', []);
                return json_encode($response);
            }
            $selectProposalsCondtions[] = ['project_id', '=', $id];
            if ($userData['type'] == 1) {
                $selectProposalsCondtions[] = ['team_id', '=', $userData['group_id']];
            }
            $proposalIds = hire_developer_proposals::select('id')->where($selectProposalsCondtions)->pluck('id')->toArray();
            $candidates = Candidate::select('*')->whereIn('proposal_id', $proposalIds)->get();
            $candidatesCount = Candidate::whereIn('proposal_id', $proposalIds)->count();
            $candidatesInfo = $this->getCandidatesInfo($candidates);
            $response = Controller::returnResponse(200, 'data found', $candidatesInfo);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error->getMessage());
            return json_encode($response);
        }
    }
    function getCandidatesInfo($candidates)
    {
        foreach ($candidates as &$candidate) {
            $teamAdminId = hire_developer_proposals::select('user_id')->where('id', '=', $candidate->proposal_id)->first();
            $adminInfo = User::select('first_name', 'last_name')->where('id', '=', $teamAdminId->user_id)->first();
            $adminName = $adminInfo->first_name . " " . $adminInfo->last_name;
            $resourceInfo = Agency_resource::where('id', '=', $candidate->agency_resource_id)->first();
            $country = Countries::where('id', '=', $resourceInfo->country)->first();
            $seniorty = Category::where('id', '=', $resourceInfo->seniority)->first();
            $skills = Agency_resources_skill::select('skill')->where('agency_resource_id', '=', $resourceInfo->id)->pluck('skill')->toArray();
            $candidate->name = $resourceInfo->name;
            $candidate->adminName = $resourceInfo->adminName;
            $candidate->country = (isset($country->flag) ? $country->flag : "");
            $candidate->seniority = (isset($seniorty->name) ? $seniorty->name : "");
            $candidate->jobTitle = $candidate->seniority . " " . $skills[0];
            $candidate->hourlyRate = $resourceInfo->hourly_rate;
            $candidate->skills = $skills;
            if (isset($resourceInfo->cv)) {
                $candidate->cv = asset("images/cvs/" . $resourceInfo->cv);
            } else {
                $candidate->cv  = null;
            }
        }
        return $candidates;
    }
}
