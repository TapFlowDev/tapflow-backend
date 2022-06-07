<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Skills;
use Exception;
use Illuminate\Http\Request;

class SkillsController extends Controller
{
    //add row 
    function Insert($data, $project_id)
    {
        foreach ($data as $skill) {
            $arr = array(
                'project_id' => $project_id,
                'skill_id' => $skill
            );
            Skills::create($arr);
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
    function search($skill)
    {
        try {
            $skillTrimed = $this->trimedSkill($skill);
            $skills = Skills::where('unique_name', 'LIKE','%'. $skillTrimed. '%')->get();
            $response = Controller::returnResponse(200, 'data found', $skills);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error);
            return json_encode($response);
        }
    }
    private function trimedSkill($skill)
    {
        $trimed = str_replace(' ', '-', strtolower(trim($skill)));
        return $trimed;
    }
}
