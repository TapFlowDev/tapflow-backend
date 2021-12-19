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
use App\Http\Controllers\GroupMembersController;
use Illuminate\Support\Facades\DB;


use Exception;

use function GuzzleHttp\Promise\each;

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
        //check if the team agency or team of freelancers-
        $rules = array(
            "name" => "required|max:255",
            "admin_id" => "required|unique:group_members,user_id|exists:freelancers,user_id"
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
            return json_encode($response);
        } else {
            // $group = new Group;
            $type = 1;
            $teamObj = new TeamController;
            $groupCategoryObj = new GroupCategoriesController;
            $userObj = new FreeLancerController;
            $membersObj = new GroupMembersController;

            try {

                $group = Group::create($req->only(['name', 'admin_id']) + ['type' => $type]);
                $group_id = $group->id;
                $userId = $req->admin_id;
                $membersObj->Insert($group_id, $userId, 1);
               
                if(isset($req->local))
                {
                  
                //    foreach($req->categories as $c)
                //    {
                //        foreach($c['subId'] as $s)
                //        {
                //         $arr=array(
                //             'group_id'=>$group_id,
                //             'category_id'=>$c['catId'],
                //             'sub_category_id'=>$s
    
                //         );
                //         $groupCategoryObj->addMultiRows($arr);
                //        }
                //    }
                }
                else{
                $cats = json_decode($req->categories);
                if (isset($cats)) {

                    foreach ($cats as $key => $value) {
                        $categoryArr = array();
                        foreach ($value->subCat as $keySub => $subValue) {
                            $categoryArr[$keySub]['group_id'] = $group_id;
                            $categoryArr[$keySub]['category_id'] = $value->catId;
                            $categoryArr[$keySub]['sub_category_id'] = $subValue;
                        }
                        $groupCategoryObj->addMultiRows($categoryArr);
                    }
                }
            }
                $teamArr = array();
                $teamArr['group_id'] = $group_id;
                $teamArr['bio'] = $req->bio;
                $teamArr['type'] = 0;
                $teamArr['link'] = $req->link;
                $teamArr['country'] = $req->country;
                $teamArr['employees_number'] = $req->employees_number;
                $teamArr['field'] = $req->field;

                $teamInfo = $teamObj->Insert($teamArr);
                $teamId = $group_id;
                 if ($req->hasFile('image')) {
                     $destPath = 'images/companies';
                     // $ext = $req->file('image')->extension();
                     $imageName = time() . "-" . $req->file('image')->getClientOriginalName();
                     // $imageName = $req->file('image') . "user-image-" . $userId . "." . $ext;
                     $img = $req->image;
                     $img->move(public_path($destPath), $imageName);
                     $teamObj->updateFiles($teamId, $imageName, 'image');
                 }
                // if ($req->hasFile('attachment')) {
                //     $destPath = 'images/groups';
                //     DB::table('groups_attachments')->where('group_id', $teamId)->delete();
                //     foreach ($req->attachment as $keyAttach => $valAttach) {
                //         $ext = $valAttach->extension();

                //         $attachName = mt_rand(100000, 999999) . "-" . $valAttach->getClientOriginalName();
                //         $attach = $valAttach;
                //         $attach->move(public_path($destPath), $attachName);
                //         DB::table('groups_attachments')->insert([
                //             'group_id' => $teamId,
                //             'attachment' => $attachName
                //         ]);
                //     }
                // }
                if ( isset($req->links) && count($req->links) > 0 ) {
                    DB::table('groups_links')->where('group_id', $teamId)->delete();

                    foreach ($req->links as $keyLink => $valLink) {
                        DB::table('groups_links')->insert([
                            'group_id' => $teamId,
                            'link' => $valLink
                        ]);
                    }
                }

                // $response = array("data" => array(
                //     "message" => "team added successfully",
                //     "status" => "200",
                //     "group_id" => $group_id,
                //     "team_id" => $teamId,
                // ));

                // return (json_encode($response));

                $responseData = array(
                    "group_id" => $group_id
                );
                $response = Controller::returnResponse(200, 'team added successfully', $responseData);
                return json_encode($response);
            } catch (\Exception $error) {
                // $response = array("data" => array(
                //     "message" => "There IS Error Occurred",
                //     "status" => "500",
                //     "error" => $error,
                // ));

                // return (json_encode($response));
                $response = Controller::returnResponse(500, 'There IS Error Occurred', $error);
                return json_encode($response);
            }
        }
    }
    function add_group_company(Request $req)
    {
        $rules = array(
            "name" => "required|max:255",
            "admin_id" => "required|unique:group_members,user_id|exists:clients,user_id"
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {

            $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
            return json_encode($response);
        } else {
            // $group = new Group;
            $type = 2;
            $teamObj = new CompanyController;
            $userObj = new ClientController;
            $membersObj = new GroupMembersController;

            try {

                $group = Group::create($req->only(['name', 'admin_id']) + ['type' => $type]);

                $group_id = $group->id;
                $userId = $req->admin_id;
                $membersObj->Insert($group_id, $userId, 1);


                $teamArr = array();
                $teamArr['group_id'] = $group_id;
                $teamArr['bio'] = $req->bio;
                $teamArr['link'] = $req->link;
                $teamArr['country'] = $req->country;
                $teamArr['employees_number'] = $req->employees_number;
                $teamArr['field'] = $req->field;

                $teamInfo = $teamObj->Insert($teamArr);
                $teamId = $group_id;
                if ($req->hasFile('image')) {
                    $destPath = 'images/groups';
                    // $ext = $req->file('image')->extension();
                    $imageName = mt_rand(100000, 999999) . "-" . $req->file('image')->getClientOriginalName();
                    // $imageName = $req->file('image') . "user-image-" . $userId . "." . $ext;

                    $img = $req->image;

                    $img->move(public_path($destPath), $imageName);
                    $teamObj->updateFiles($teamId, $imageName, 'image');
                }
                if ($req->hasFile('attachment')) {
                    $destPath = 'images/groups';
                    DB::table('groups_attachments')->where('group_id', $teamId)->delete();
                    foreach ($req->attachment as $keyAttach => $valAttach) {
                        $ext = $valAttach->extension();

                        $attachName = mt_rand(100000, 999999) . "-" . $valAttach->getClientOriginalName();
                        $attach = $valAttach;
                        $attach->move(public_path($destPath), $attachName);
                        DB::table('groups_attachments')->insert([
                            'group_id' => $teamId,
                            'attachment' => $attachName
                        ]);
                    }
                }
                if (count($req->links) > 0 && isset($req->links)) {
                    DB::table('groups_links')->where('group_id', $teamId)->delete();

                    foreach ($req->links as $keyLink => $valLink) {
                        DB::table('groups_links')->insert([
                            'group_id' => $teamId,
                            'link' => $valLink
                        ]);
                    }
                }
                // $response = array("data" => array(
                //     "message" => "team added successfully",
                //     "status" => "200",
                //     "group_id" => $group_id,
                //     "company_id" => $teamId,
                // ));

                // return (json_encode($response));

                $responseData = array(
                    "group_id" => $group_id
                );
                $response = Controller::returnResponse(200, 'company added successfully', $responseData);
                return json_encode($response);
            } catch (Exception $error) {
                // $response = array("data" => array(
                //     "message" => "There IS Error Occurred",
                //     "status" => "500",
                //     "error" => $error,
                // ));

                // return (json_encode($response));

                $response = Controller::returnResponse(500, 'There IS Error Occurred', $error);
                return json_encode($response);
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
    function getGroupById($id)
    {
        return Group::find($id);
    }
    
}
