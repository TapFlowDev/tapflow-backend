<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\users_category;  
use Illuminate\Http\Request;

class UserCategoriesController extends Controller
{
    //add row 
    function Insert($arr, $userId)
    {
       
        $info['user_id'] = $userId; 
        $info['category_id'] = $arr['catId']; 
        $info['sub_category_id'] = $arr['subId']; 
        


        users_category::create($info);
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
