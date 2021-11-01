<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Group;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\groups_category;
use App\Http\Controllers\TeamController;

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
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {

            // $group = new Group;
            $type = 1;
            $teamObj = new TeamController;

            try {
                $req->types = $type;
                $group = Group::create($req->only(['name', 'admin_id', 'types']));
                $group_id = $group->id;

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
    //update row according to row id
    function Update($id)
    {
    }
    //delete row according to row id
    function Delete($id)
    {
    }
}
