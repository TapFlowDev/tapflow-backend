<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Requirement as requirementModel;
use Exception;
use Illuminate\Http\Request;
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
  function getRequirementsByProjectIdSelectedColumns(Request $req, $projectId)
  {
    try {
      $project = Project::select('id')->where('id', '=', $projectId)->first();
      if (!$project) {
        $response = Controller::returnResponse(422, 'project does not exsist', []);
        return (json_encode($response));
      }
      $requirements = requirementModel::select('id as requirementId', 'description')->where('project_id', '=', $projectId)->get();
    } catch (Exception $error) {
      $response = Controller::returnResponse(500, "there is an error", $error->getMessage());
      return (json_encode($response));
    }
    // $requirements = requirementModel::where('project_id', $id)->select('description')->get();

    return $requirements;
  }
}
