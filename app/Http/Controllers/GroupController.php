<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Models\Group;
use app\Models\Company;
use app\Models\Team;
use app\Models\Category;
use app\Models\SubCategory;
use app\Models\groups_category;
use Exception;

class GroupController extends Controller
{   
    // get all categories 
    function get_categories()
    {
        // try
        
        // {
            $categories=Category::all();
            $response = array("data" => array(
                "message" => "get categories successfully",
                "status" => "200",
                "error" => $categories,
            ));

            return (json_encode($response));
              
        // }
        // catch(Exception $error)
        // {
        //     $response = array("data" => array(
        //         "message" => "There IS Error Occurred",
        //         "status" => "500",
        //         "error" => $error,
        //     ));

        //     return (json_encode($response));
        // }
    }

    //add row 
    function add_group(Request $req)
    {

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
