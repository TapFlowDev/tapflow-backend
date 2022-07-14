<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Requirement as requirementModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Svg\Tag\Rect;

/*
  $table->integer('id')->autoIncrement();
            $table->integer('project_id');
            $table->integer('user_id');
            $table->integer('milestone');
            $table->string('name','255');
            $table->text('description'); 
*/

class Requirement extends Controller
{
  //add row 
  function Insert($data, $project_id, $user_id)
  {

    foreach ($data as $requirement) {
      $arr = array(
        'project_id' => $project_id,
        'user_id' => $user_id,
        'description' => $requirement,
      );
      requirementModel::create($arr);
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
  function getRequirementsByProjectId($id)
  {
    $requirements = requirementModel::where('project_id', $id)->where('deleted', '=', 0)->select('description')->get();

    return ($requirements);
  }
  function getRequirementsAlldataByProjectId($id)
  {
    $requirements = requirementModel::where('project_id', $id)->where('deleted', '=', 0)->get();

    return ($requirements);
  }
  function getRequirementsAndHourlyRateByProjectId($projectId)
  {
    $requirements = DB::table('requirements')
      ->select('requirements.id as requirementId', 'requirements.description')
      ->where('requirements.project_id', '=', $projectId)
      ->get();
    return $requirements;
  }
  function getHireDevRequirmentData($projectId)
  {
    $requirements = DB::table('requirements')
      ->select('requirements.id', 'requirements.description')
      ->where('requirements.project_id', '=', $projectId)
      ->where('deleted', '=', 0)
      ->get();
    $returnDataArr = $this->splitRequirmnets($requirements);
    // dd($returnDataArr);
    return $returnDataArr;
  }
  private function splitRequirmnets($requirements)
  {
    $splitRequirementsArr = array();
    $skillsArr = [];
    foreach ($requirements as $requirement) {
      $splitArray = explode(",", $requirement->description);
      $countSplitArray = count($splitArray);
      $reqSkillsArr = [];
      for ($i = 0; $i < ($countSplitArray - 4); $i++) {
        $skillsArr[] = trim($splitArray[$i]);
        $reqSkillsArr[] = trim($splitArray[$i]);
      }
      $hourlyRate = (isset($requirement->hourly_rate) ? $requirement->hourly_rate : null);
      $splitRequirementsArr[] = [
        'id' => $requirement->id,
        'quantity' => trim($splitArray[$countSplitArray - 1]),
        'hours' => trim($splitArray[$countSplitArray - 2]),
        'duration' => trim($splitArray[$countSplitArray - 3]),
        'seniority' => trim($splitArray[$countSplitArray - 4]),
        'skills' => $reqSkillsArr,
        'hourlyRate' => $hourlyRate,
      ];
    }
    $returnDataArr = [
      'skills' => array_unique($skillsArr),
      'reqArr' => $splitRequirementsArr
    ];
    // dd(($splitRequirementsArr));
    return $returnDataArr;
  }
  function getHireDevInitialProposalRequirements($proposalId)
  {
    $requirements = DB::table('proposal_requirements')
      ->join('requirements', 'proposal_requirements.requirement_id', '=', 'requirements.id')
      ->select('requirements.*', 'proposal_requirements.hourly_rate')
      ->where('proposal_requirements.proposal_id', '=', $proposalId)
      ->get();
    $returnDataArr = $this->splitRequirmnets($requirements);
    // dd($returnDataArr);
    return $returnDataArr;
  }
  function getResourcesByProposalId($proposalId)
  {
    $requirements = DB::table('proposal_requirements')
      ->join('requirements', 'proposal_requirements.requirement_id', '=', 'requirements.id')
      ->select('requirements.id', 'requirements.description', 'proposal_requirements.hourly_rate')
      ->where('proposal_requirements.proposal_id', '=', $proposalId)
      ->get();
    $returnDataArr = $this->splitRequirmnetsToResources($requirements);
    // dd($returnDataArr);
    return $returnDataArr;
  }
  private function splitRequirmnetsToResources($requirements)
  {
    $resources = [];
    foreach ($requirements as $requirement) {
      $splitArray = explode(",", $requirement->description);
      $countSplitArray = count($splitArray);
      $quantity = (int)trim($splitArray[$countSplitArray - 1]);
      $hourlyRate = (isset($requirement->hourly_rate) ? $requirement->hourly_rate : null);
      for ($i = 0; $i < $quantity; $i++) {
        $resources[] = [
          'id' => $requirement->id,
          'skill' => $splitArray[0],
          'hourlyRate' => $hourlyRate,
        ];
      }
    }
    // dd(($splitRequirementsArr));
    return $resources;
  }
  function editRequirement(Request $req)
  {
    try {
      $userData = Controller::checkUser($req);
      if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 2)) {
        $response = Controller::returnResponse(401, "unauthorized", []);
        return (json_encode($response));
      } else {
        requirementModel::where('id', $req->requirement_id)->update(['description' => $req->requirement]);
        $response = Controller::returnResponse(200, "successful", []);
        return (json_encode($response));
      }
    } catch (Exception $error) {
      $response = Controller::returnResponse(500, "something went wrong", []);
      return (json_encode($response));
    }
  }
  function deleteRequirement(Request $req)
  {
    try {
      $userData = Controller::checkUser($req);
      if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 2)) {
        $response = Controller::returnResponse(422, "unauthorized", []);
        return (json_encode($response));
      } else {
        requirementModel::where('id', $req->requirement_id)->update(['deleted' => 1]);
        $response = Controller::returnResponse(200, "successful", []);
        return (json_encode($response));
      }
    } catch (Exception $error) {
      $response = Controller::returnResponse(500, "something went wrong", []);
      return (json_encode($response));
    }
  }
  function addRequirement(Request $req)
  {
    try {
      $userData = Controller::checkUser($req);
      if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 2)) {
        $response = Controller::returnResponse(401, "unauthorized", []);
        return (json_encode($response));
      } else {
        $skillsObj = new SkillsController;
        $project_id = $req->project_id;
        $project = Project::select('id')->where([['id', '=', $project_id], ['company_id', '=', $userData['group_id']]])->first();
        if (!$project) {
          $response = Controller::returnResponse(422, "Action denied", []);
          return (json_encode($response));
        }
        $requirementsDescriptionArr = $req->requirements_description;
        $newSkills = $skillsObj->splitSkillsRequirmnets($requirementsDescriptionArr);
        $reqs = $this->Insert($requirementsDescriptionArr, $project_id, $userData['user_id']);
        // requirementModel::where('id', $req->requirement_id)->update(['description' => $req->requirement]);
        $response = Controller::returnResponse(200, "successful", $reqs);
        return (json_encode($response));
      }
    } catch (Exception $error) {
      $response = Controller::returnResponse(500, "something went wrong", []);
      return (json_encode($response));
    }
  }
}
