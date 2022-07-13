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
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AgencyResourceController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
        try {
            $userData = $this->checkUser($req);
            $condition = ($userData['privileges'] == 1 && $userData['group_id'] != '');
            if (!$condition) {
                $response = Controller::returnResponse(422, 'Action denied', []);
                return json_encode($response);
            }
            $rules = array(
                "name" => "required|max:255",
                "seniority" => "required|exists:categories,id",
                "country" => "required|exists:countries,id",
                "rate" => "required|numeric",
                "skills" => "required",
                "cv" => "mimes:doc,pdf,docx",
            );
            $validator = Validator::make($req->all(), $rules);
            if ($validator->fails()) {
                $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
                return json_encode($response);
            }
            $agencyResourcesSkillObj = new AgencyResourcesSkillController;
            $agencyResourceArr = [
                'team_id' => $userData['group_id'],
                'name' => $req->name,
                'seniority' => $req->seniority,
                'country' => $req->country,
                'hourly_rate' => $req->rate,
            ];
            $agencyResource = Agency_resource::create($agencyResourceArr);
            if ($req->hasFile('cv')) {
                $destPath = 'images/cvs';
                $imageName = time() . "-" . $req->file('cv')->getClientOriginalName();
                $img = $req->cv;
                $img->move(public_path($destPath), $imageName);
                $this->updateFiles($agencyResource->id, $imageName, 'cv');
            }
            // $newSkills = $skillsObj->splitSkillsRequirmnets($requirementsDescriptionArr);
            $agencyResourcesSkillObj->Insert($agencyResource->id, json_decode($req->skills));
            $responseData = ['agencyResourceId' => $agencyResource->id];
            $response = Controller::returnResponse(200, 'Success', $responseData);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error->getMessage());
            return json_encode($response);
        }
    }
    //update row according to row id
    function Update(Request $req)
    {
        try {
            $userData = $this->checkUser($req);
            $condition = ($userData['privileges'] == 1 && $userData['group_id'] != '');
            if (!$condition) {
                $response = Controller::returnResponse(422, 'Action denied', []);
                return json_encode($response);
            }
            $rules = array(
                "id" => 'exists:agency_resources,id',
                "name" => "required|max:255",
                "seniority" => "required|exists:categories,id",
                "country" => "required|exists:countries,id",
                "rate" => "required|numeric",
                "skills" => "required",
                "cv" => "mimes:doc,pdf,docx",
            );
            $validator = Validator::make($req->all(), $rules);
            if ($validator->fails()) {
                $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
                return json_encode($response);
            }
            $agencyResourcesSkillObj = new AgencyResourcesSkillController;
            $agencyResource = Agency_resource::where('id', '=', $req->id)->where('team_id', '=', $userData['group_id'])->first();
            if (!$agencyResource) {
                $response = Controller::returnResponse(401, 'Invalid action', []);
                return json_encode($response);
            }
            $agencyResource->name = $req->name;
            $agencyResource->seniority = $req->seniority;
            $agencyResource->country = $req->country;
            $agencyResource->hourly_rate = $req->rate;
            if ($req->hasFile('cv')) {
                $destPath = 'images/cvs';
                $imageName = time() . "-" . $req->file('cv')->getClientOriginalName();
                $img = $req->cv;
                $img->move(public_path($destPath), $imageName);
                $agencyResource->cv = $imageName;
                // $this->updateFiles($agencyResource->id, $imageName, 'cv');
            }
            $agencyResource->save();
            $agencyResourcesSkillObj->Delete($req->id);
            $agencyResourcesSkillObj->Insert($agencyResource->id, json_decode($req->skills));
            $responseData = ['agencyResourceId' => $req->id];
            $response = Controller::returnResponse(200, 'Success', $responseData);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error->getMessage());
            return json_encode($response);
        }
    }
    //delete row according to row id
    function Delete($id)
    {
    }
    function updateFiles($id, $imageName, $filedName)
    {
        Agency_resource::where('id', $id)->update(array($filedName => $imageName));
    }
    function getAgencyResources(Request $req, $projectId = 0)
    {
        try {
            $userData = $this->checkUser($req);
            $condition = ($userData['privileges'] == 1 && $userData['group_id'] != '');
            if (!$condition) {
                $response = Controller::returnResponse(422, 'Action denied', []);
                return json_encode($response);
            }
            if ($projectId > 0) {
                $project = Project::select('id')->where('id', '=', $projectId)->first();
                if (!$project) {
                    $response = Controller::returnResponse(401, 'Action denied', []);
                    return json_encode($response);
                }
                $selectProposalsCondtions[] = ['project_id', '=', $projectId];
            } 
            $selectProposalsCondtions[] = ['team_id', '=', $userData['group_id']];
            $proposalIds = hire_developer_proposals::select('id')->where($selectProposalsCondtions)->pluck('id')->toArray();
            $candidates = Candidate::select('agency_resource_id')->whereIn('proposal_id', $proposalIds)->pluck('agency_resource_id')->toArray();
            $agencyResources = Agency_resource::whereNotIn('id', $candidates)->where('team_id', '=', $userData['group_id'])->get();
            $agencyResourcesInfo = $this->getAgencyResourcesInfo($agencyResources);
            $response = Controller::returnResponse(200, 'data found', $agencyResourcesInfo);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error->getMessage());
            return json_encode($response);
        }
    }
    private function getAgencyResourcesInfo($agencyResources)
    {
        foreach ($agencyResources as &$agencyResource) {
            $country = Countries::where('id', '=', $agencyResource->country)->first();
            $seniorty = Category::where('id', '=', $agencyResource->seniority)->first();
            $skills = Agency_resources_skill::select('skill')->where('agency_resource_id', '=', $agencyResource->id)->pluck('skill')->toArray();
            $agencyResource->country = (isset($country->flag) ? $country->flag : "");
            $agencyResource->seniority = (isset($seniorty->name) ? $seniorty->name : "");
            $agencyResource->jobTitle = $agencyResource->seniority . " " . (isset($skills[0]) ? $skills[0] : "");
            $agencyResource->skills = $skills;
            if (isset($agencyResource->cv)) {
                $agencyResource->cv = asset("images/cvs/" . $agencyResource->cv);
            } else {
                $agencyResource->cv  = null;
            }
        }
        return $agencyResources;
    }
}
