<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project_skills;
use Illuminate\Http\Request;

class ProjectSkillsController extends Controller
{
    //add row 
    function Insert($data, $project_id)
    {
        foreach ($data as $skill) {
            $arr = array(
                'project_id' => $project_id,
                'skill_id' => $skill
            );
            Project_skills::create($arr);
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
