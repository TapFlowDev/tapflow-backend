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
        foreach ($skills as $skill) {
            $arr = [
                'agency_resource_id' => $agencyResourceId,
                'skill' => $skill
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
    function Delete($id)
    {
    }
}
