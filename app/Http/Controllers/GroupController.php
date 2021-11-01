<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Group;
use App\Models\Category;
use App\Models\SubCategory;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\GroupCategoriesController;

use Exception;

class GroupController extends Controller
{
    // get all categories 
    function get_categories()
    {
        // try

        // {
        $categories = Category::all();
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
    function add_group_team(Request $req)
    {
        $rules = array(
            "name" => "required|max:255",
            "admin_id" => "required|unique:groups"
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            // $group = new Group;
            $type = 1;
            $teamObj = new TeamController;
            $groupCategoryObj = new GroupCategoriesController;

            try {
                $req->types = $type;
                $group = Group::create($req->only(['name', 'admin_id', 'types']));
                $group_id = $group->id;

                foreach ($req->category as $key => $value) {
                    //     // dd($value);
                    $groupCategoryObj->Insert($value, $group_id);
                }

                $teamArr = array();
                $teamArr['name'] = $req->name;
                $teamArr['group_id'] = $group_id;
                $teamArr['bio'] = $req->bio;
                $teamArr['type'] = 0;

                $teamInfo = $teamObj->Insert($teamArr);
                $teamId = $teamInfo->id;
                if ($req->hasFile('image')) {
                    $destPath = 'images/teams';
                    $ext = $req->file('image')->extension();
                    $imageName = "team-image-" . $teamId . "." . $ext;
                    $req->image->move(public_path($destPath), $imageName);
                    $teamObj->updateFiles($teamId, $imageName, 'image');
                }
                if ($req->hasFile('attachment')) {
                    $destPath = 'images/teams';
                    $ext = $req->file('image')->extension();
                    $imageName = "team-attachment-" . $teamId . "." . $ext;
                    $req->image->move(public_path($destPath), $imageName);
                    $teamObj->updateFiles($teamId, $imageName, 'attachment');
                }
                $response = array("data" => array(
                    "message" => "team added successfully",
                    "status" => "200",
                    "group_id" => $group_id,
                    "team_id" => $teamId,
                ));

                return (json_encode($response));
             } catch (\Exception $error) {
                 $response = array("data" => array(
                     "message" => "There IS Error Occurred",
                     "status" => "500",
                     "error" => $error,
                 ));

                 return (json_encode($response));
            }
        }
    }
    function add_group_company(Request $req)
    {
        $rules = array(
            "name" => "required|max:255",
            "admin_id" => "required|unique:groups"
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            // $group = new Group;
            $type = 2;
            $teamObj = new CompanyController;
            $groupCategoryObj = new GroupCategoriesController;

             try {
                $req->types = $type;
                $group = Group::create($req->only(['name', 'admin_id', 'types']));
                $group_id = $group->id;

                foreach ($req->category as $key => $value) {
                    $groupCategoryObj->Insert($value, $group_id);
                }

                $teamArr = array();
                $teamArr['group_id'] = $group_id;
                $teamArr['bio'] = $req->bio;
                $teamArr['link'] = $req->link;
                $teamArr['country'] = $req->country;

                $teamInfo = $teamObj->Insert($teamArr);
                $teamId = $teamInfo->id;
                if ($req->hasFile('image')) {
                    $destPath = 'images/companies';
                    $ext = $req->file('image')->extension();
                    $imageName = "company-image-" . $teamId . "." . $ext;
                    $req->image->move(public_path($destPath), $imageName);
                    $teamObj->updateFiles($teamId, $imageName, 'image');
                }
                if ($req->hasFile('attachment')) {
                    $destPath = 'images/companies';
                    $ext = $req->file('image')->extension();
                    $imageName = "company-attachment-" . $teamId . "." . $ext;
                    $req->image->move(public_path($destPath), $imageName);
                    $teamObj->updateFiles($teamId, $imageName, 'attachment');
                }
                $response = array("data" => array(
                    "message" => "team added successfully",
                    "status" => "200",
                    "group_id" => $group_id,
                    "company_id" => $teamId,
                ));

                return (json_encode($response));
            } catch (\Exception $error) {
                 $response = array("data" => array(
                     "message" => "There IS Error Occurred",
                     "status" => "500",
                     "error" => $error,
                 ));

                 return (json_encode($response));
             }
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
