<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agency_service;
use Illuminate\Http\Request;

class AgencyServiceController extends Controller
{
    //add row 
    function Insert($agencyId, $data)
    {
        foreach($data as $service){
            $arr=array(
                'group_id'=>$agencyId,
                'category_id'=>$service
            );
            Agency_service::create($arr);
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
