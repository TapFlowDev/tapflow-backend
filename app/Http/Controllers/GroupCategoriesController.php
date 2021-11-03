<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\groups_category;


class GroupCategoriesController extends Controller
{
    //add row 
    function Insert($catId, $subId, $groupId)
    {

        $info['group_id'] = $groupId;
        $info['category_id'] = $catId;
        $info['sub_category_id'] = $subId;
        dd($info);
        groups_category::firstOrCreate($info);
    }
    //update row according to row id
    function Update($id)
    {
    }
    //delete row according to row id
    function Delete($id)
    {
    }

    function addMultiRows($info){
        groups_category::insert($info);

    }
}
