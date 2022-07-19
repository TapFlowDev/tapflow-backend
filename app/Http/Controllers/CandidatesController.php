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
use Illuminate\Support\Facades\DB;
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
                $proposalArr = [
                    "project_id" => $id,
                    "team_id" => $userData['group_id'],
                    "user_id" => $userData['user_id'],
                ];
                $proposal = hire_developer_proposals::create($proposalArr);
            }
            $candidates = json_decode($req->candidates);
            $candidatesIds = Agency_resource::select('id', 'hourly_rate')->whereIn('id', $candidates)->where('team_id', '=', $userData['group_id'])->get();
            if (count($candidatesIds) < 1) {
                $response = Controller::returnResponse(422, 'invalid candidates', []);
                return json_encode($response);
            }
            foreach ($candidatesIds as $candidate) {
                //check if not exist before add 
                $doesExist = Candidate::select('id')->where('proposal_id', '=', $proposal->id)->where('agency_resource_id', '=', $candidate->id)->first();
                if (!$doesExist) {
                    $candidateArr = [
                        'proposal_id' => $proposal->id,
                        'agency_resource_id' => $candidate->id,
                        'hourly_rate' => $candidate->hourly_rate,
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
                "projectId" => "required"
            );
            $validator = Validator::make($req->all(), $rules);
            if ($validator->fails()) {
                $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
                return json_encode($response);
            }
            $candidatesIds = json_decode($req->candidates);

            $project = Project::select('id')->where('id', '=', $req->projectId)->first();
            if (!$project) {
                $response = Controller::returnResponse(422, 'Action denied', []);
                return json_encode($response);
            }
            $proposalIds = hire_developer_proposals::where('project_id', '=', $req->projectId)->where('team_id', '=', $userData['group_id'])->pluck('id')->toArray();
            if (count($proposalIds) < 1) {
                $response = Controller::returnResponse(422, 'Action denied', []);
                return json_encode($response);
            }
            $candidates = DB::table('candidates')
                ->whereIn('candidates.id', $candidatesIds)->where('proposal_id', '=', $proposalIds[0])
                ->delete();
            // $candidatesIds = Agency_resource::select('id')->whereIn('id', $candidates)->where('team_id', '=', $userData['group_id'])->pluck('id')->toArray();
            // if (count($candidatesIds) < 1) {
            //     $response = Controller::returnResponse(422, 'invalid candidates', []);
            //     return json_encode($response);
            // }
            // $candidate = Candidate::whereIn('agency_resource_id', $candidatesIds)->delete();

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
            if ($userData['type'] == 2) {
                $candidates = DB::table('candidates')
                    ->join('agency_resources', 'candidates.agency_resource_id', '=', 'agency_resources.id')
                    ->select('candidates.id as candidate_id', 'candidates.status as candidate_status', 'candidates.proposal_id', 'agency_resources.*')
                    ->whereIn('candidates.proposal_id', $proposalIds)
                    ->where('candidates.status', '<>', 2)
                    ->get();
            } else {
                $candidates = DB::table('candidates')
                    ->join('agency_resources', 'candidates.agency_resource_id', '=', 'agency_resources.id')
                    ->select('agency_resources.*', 'candidates.id as candidate_id', 'candidates.status as candidate_status', 'candidates.proposal_id', 'candidates.hourly_rate', 'agency_resources.hourly_rate as default_hourly_rate')
                    ->whereIn('candidates.proposal_id', $proposalIds)
                    ->get();
            }
            // $candidatesCount = Candidate::whereIn('proposal_id', $proposalIds)->count();
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
        $skillsObj = new SkillsController;
        foreach ($candidates as &$candidate) {
            $teamAdminId = hire_developer_proposals::select('user_id')->where('id', '=', $candidate->proposal_id)->first();
            $adminInfo = User::select('first_name', 'last_name')->where('id', '=', $teamAdminId->user_id)->first();
            $adminName = $adminInfo->first_name . " " . $adminInfo->last_name;
            $country = Countries::where('id', '=', $candidate->country)->first();
            $seniorty = Category::where('id', '=', $candidate->seniority)->first();
            $skills = Agency_resources_skill::select('skill')->where('agency_resource_id', '=', $candidate->id)->pluck('skill')->toArray();
            $candidate->adminName = $adminName;
            $candidate->country_id =  $candidate->country;
            $candidate->seniority_id =  $candidate->seniority;
            $candidate->country = (isset($country->flag) ? $country->flag : "");
            $candidate->seniority = (isset($seniorty->name) ? $seniorty->name : "");
            $candidate->jobTitle = $candidate->seniority . " " . (isset($skills[0]) ? $skills[0] : "");
            $candidate->skills = $skills;
            $candidate->skillsWithId = (isset($skills) ? $skillsObj->getSkills($skills) : []);

            if (isset($candidate->cv)) {
                $candidate->cv = asset("images/cvs/" . $candidate->cv);
            } else {
                $candidate->cv  = null;
            }
        }
        return $candidates;
    }
    function candidatesStatusActions(Request $req)
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
                "projectId" => "required",
                "status" => "required|gt:0|lt:4",
            );
            $validator = Validator::make($req->all(), $rules);
            if ($validator->fails()) {
                $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
                return json_encode($response);
            }
            $candidatesIds = json_decode($req->candidates);

            $project = Project::select('id')->where('id', '=', $req->projectId)->where('company_id', '=', $userData['group_id'])->first();
            if (!$project) {
                $response = Controller::returnResponse(422, 'Action denied', []);
                return json_encode($response);
            }
            // $candidates = Candidate::whereIn('id', $candidatesIds)->where('status', '<>', 2)->get();

            $candidates = DB::table('candidates')
                ->join('hire_developer_proposals', 'candidates.proposal_id', '=', 'hire_developer_proposals.id')
                ->where('hire_developer_proposals.project_id', '=', $req->projectId)
                ->where('candidates.status', '<>', 2)
                ->where('candidates.status', '<>', 1)
                ->whereIn('candidates.id', $candidatesIds)
                ->update(['candidates.status' => $req->status]);
            if ($req->status == 1) {
                $proposals = DB::table('hire_developer_proposals')
                    ->join('candidates', 'hire_developer_proposals.id', '=', 'candidates.proposal_id')
                    ->where('hire_developer_proposals.project_id', '=', $req->projectId)
                    ->where('hire_developer_proposals.status', '<>', 0)
                    ->whereIn('candidates.id', $candidatesIds)
                    ->update(['hire_developer_proposals.status' => 1]);
            }
            if ($candidates < 1) {
                $response = Controller::returnResponse(200, 'Action denied', []);
                return json_encode($response);
            } else {
                $response = Controller::returnResponse(200, 'Candidates updated successfully', []);
                return json_encode($response);
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error->getMessage());
            return json_encode($response);
        }
    }
    function updateCandidateHourlyRate(Request $req)
    {
        try {
            $userData = $this->checkUser($req);
            $condition = ($userData['privileges'] == 1 && $userData['group_id'] != '');
            if (!$condition) {
                $response = Controller::returnResponse(422, 'Action denied', []);
                return json_encode($response);
            }
            $rules = array(
                "candidateId" => "required|exists:candidates,id",
                "hourlyRate" => "required|gt:0|numeric",
            );
            $validator = Validator::make($req->all(), $rules);
            if ($validator->fails()) {
                $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
                return json_encode($response);
            }
            $candidate = Candidate::where('id', '=', $req->candidateId)->first();
            /**
             * validate candidate  
             */
            $agency_resource = Agency_resource::select('id')->where('id', '=', $candidate->agency_resource_id)->where('team_id', '=', $userData['group_id'])->first();
            if (!$agency_resource) {
                $response = Controller::returnResponse(422, 'Action denied', []);
                return json_encode($response);
            }
            /**
             * update hourly rate
             */
            $candidate->hourly_rate = $req->hourlyRate;
            $candidate->save();
            $response = Controller::returnResponse(200, 'updated successfully', []);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error->getMessage());
            return json_encode($response);
        }
    }
    function getCandidatesByProposalId($proposal_id)
    {
       $cc= DB::table('candidates')
        ->join('agency_resources',' candidates.agency_resource_id','agency_resources.id')
        ->join('agency_resources_skills',' candidates.agency_resource_id','agency_resources_skills.agency_resource_id')
        ->select('agency_resources.name','agency_resources.seniority','agency_resources.hourly_rate','agency_resources.image','agency_resources_skills.skill')
        ->where('candidates.proposal_id','=',$proposal_id)
        ->where('candidates.status','=',1)
        ->get();
        $response = Controller::returnResponse(200, 'successfully', $cc);
        return json_encode($response);
    }
}
