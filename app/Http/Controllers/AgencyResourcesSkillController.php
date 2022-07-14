<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agency_resources_skill;
use Illuminate\Http\Request;

class AgencyResourcesSkillController extends Controller
{
    //add row 
    function Insert($agencyResourceId, $skills)
    {
        $skillsObj = new SkillsController;
        foreach ($skills as $skill) {
            $skillName = $skill->name;
            $skillsObj->addNewSkill($skillName);
            $arr = [
                'agency_resource_id' => $agencyResourceId,
                'skill' => $skillName
            ];
            Agency_resources_skill::create($arr);
        }
        return 1;
    }
    //update row according to row id
    function Update($id)
    {
    }
    //delete row according to row id
    function Delete($agency_resource_id)
    {
        Agency_resources_skill::where('agency_resource_id', '=', $agency_resource_id)->delete();
    }
}
