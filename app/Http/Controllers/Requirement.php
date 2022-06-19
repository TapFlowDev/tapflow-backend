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
    $requirements = requirementModel::where('project_id', $id)->select('description')->get();

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
      ->get();
      $returnDataArr = $this->splitRequirmnets($requirements);
      // dd($returnDataArr);
      return $returnDataArr;
  }
  private function splitRequirmnets($requirements)
  {
    $splitRequirementsArr = array();
    $skillsArr = [];
    foreach($requirements as $requirement){
      $splitArray = explode(",", $requirement->description);
      $countSplitArray = count($splitArray);
      for($i = 0; $i < ($countSplitArray-4); $i++ ){
        $skillsArr[] = trim($splitArray[$i]);
      }
      $hourlyRate = ($requirement->hourly_rate ? $requirement->hourly_rate : null );
      $splitRequirementsArr[] = [
        'quantity'=>trim($splitArray[$countSplitArray-1]),
        'hours'=>trim($splitArray[$countSplitArray-2]),
        'duration'=>trim($splitArray[$countSplitArray-3]),
        'seniority'=>trim($splitArray[$countSplitArray-4]),
        'skills' => $skillsArr,
        'hourlyRate' => $hourlyRate,
      ];
    }
    $returnDataArr = [
      'skills'=>array_unique($skillsArr),
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
}
