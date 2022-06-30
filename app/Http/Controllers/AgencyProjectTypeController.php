<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agency_project_type;
use Illuminate\Http\Request;

class AgencyProjectTypeController extends Controller
{
    //add row 
    function Insert($agencyId, $data)
    {
        foreach($data as $service){
            $arr=array(
                'group_id'=>$agencyId,
                'project_type'=>$service
            );
            Agency_project_type::create($arr);
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
