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
            $skills = Skills::select('id', 'name')->where('unique_name', 'LIKE', $skillTrimed . '%')->get();
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
    function getSkills($skillsArr)
    {
        $trimedSkills = [];
        foreach ($skillsArr as $skill) {
            $trimedSkills[] = $this->trimedSkill($skill);
        }
        $skills = Skills::select('id', 'name')->whereIn('unique_name', $trimedSkills)->get();
        return $skills;
    }
    function addNewSkill($skill)
    {
        $trimedSkill = $this->trimedSkill($skill);
        $newSkill = Skills::select('unique_name')->where('unique_name', '=', $trimedSkill)->first();
        if (!$newSkill) {
            $skillArr = array(
                'name' => $skill,
                'unique_name' => $trimedSkill,
            );
            Skills::create($skillArr);
        }
    }
    function splitSkillsRequirmnets($requirements)
    {
        try {
            return $requirements;
            $splitRequirementsArr = array();
            $skillsArr = [];
            foreach ($requirements as $requirement) {
                $splitArray = explode(",", $requirement->description);
                $countSplitArray = count($splitArray);
                for ($i = 0; $i < ($countSplitArray - 4); $i++) {
                    $skillsArr[] = trim($splitArray[$i]);
                }
            }
            $addSkills = array_map([$this, 'addNewSkill'], array_unique($skillsArr));

            // dd(($splitRequirementsArr));
            return 1;
        } catch (Exception $error) {
            return 0;
        }
    }
}
