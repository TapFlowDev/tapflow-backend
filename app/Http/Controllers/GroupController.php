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
use App\Http\Controllers\FreeLancerController;
use App\Http\Controllers\ClientController;

use Exception;

class   GroupController extends Controller
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
            $userObj = new FreeLancerController;

            try {
                $req->type = $type;
                $group = Group::create($req->only(['name', 'admin_id', 'type']));
                $group_id = $group->id;
                $userId = $req->admin_id;

                foreach ($req->category as $key => $value) {
                    $categoryArr = array();
                    foreach ($value['subId'] as $keySub => $subValue) {
                        $categoryArr[$keySub]['group_id'] = $group_id;
                        $categoryArr[$keySub]['category_id'] = $value['catId'];
                        $categoryArr[$keySub]['sub_category_id'] = $subValue;
                    }
                    $groupCategoryObj->addMultiRows($categoryArr);
                }

                $teamArr = array();
                $teamArr['name'] = $req->name;
                $teamArr['group_id'] = $group_id;
                $teamArr['bio'] = $req->bio;
                $teamArr['type'] = 0;
                $teamArr['link'] = $req->link;
                $teamArr['country'] = $req->country;
                $teamArr['employees_number'] = $req->employees_number;

                $teamInfo = $teamObj->Insert($teamArr);
                $teamId = $group_id;
                $userObj->updateTeamId($userId, $group_id);
                if ($req->hasFile('image')) {
                    $destPath = 'images/companies';
                    $ext = $req->file('image')->extension();
                    $imageName = "company-image-" . $teamId . "." . $ext;
                    $image = $req->image;
                    $image->move(public_path($destPath), $imageName);
                    $teamObj->updateFiles($teamId, $imageName, 'image');
                   
                }
                if ($req->hasFile('attachment')) {
                    $destPath = 'images/companies';
                    $ext = $req->file('attachment')->extension();
                    $attachName = "company-attachment-" . $teamId . "." . $ext;
                    $attach = $req->attachment;
                    $attach->move(public_path($destPath), $attachName);
                    $teamObj->updateFiles($teamId, $attachName, 'attachment');
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
            // "admin_id" => "required|unique:groups"
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            // $group = new Group;
            $type = 2;
            $teamObj = new CompanyController;
            $userObj = new ClientController;
            try {
                $req->type = $type;
                
                $group = Group::create($req->only(['name', 'admin_id', 'type']));
                $group_id = $group->id;
                $userId = $req->admin_id;

                $teamArr = array();
                $teamArr['group_id'] = $group_id;
                $teamArr['bio'] = $req->bio;
                $teamArr['link'] = $req->link;
                $teamArr['country'] = $req->country;
                $teamArr['employees_number'] = $req->employees_number;
                $teamArr['field'] = $req->field;

                $teamInfo = $teamObj->Insert($teamArr);
                $teamId = $teamInfo->id;
                $userObj->updateTeamId($userId, $group_id);
                if ($req->hasFile('image')) {
                    $destPath = 'images/companies';
                    $ext = $req->file('image')->extension();
                    $imageName = "company-image-" . $teamId . "." . $ext;
                    $image = $req->image;
                    $image->move(public_path($destPath), $imageName);
                    $teamObj->updateFiles($teamId, $imageName, 'image');
                   
                }
                if ($req->hasFile('attachment')) {
                    $destPath = 'images/companies';
                    $ext = $req->file('attachment')->extension();
                    $attachName = "company-attachment-" . $teamId . "." . $ext;
                    $attach = $req->attachment;
                    $attach->move(public_path($destPath), $attachName);
                    $teamObj->updateFiles($teamId, $attachName, 'attachment');
                }
                $response = array("data" => array(
                    "message" => "team added successfully",
                    "status" => "200",
                    "group_id" => $group_id,
                    "company_id" => $teamId,
                ));

                return (json_encode($response));
            } catch (Exception $error) {
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
