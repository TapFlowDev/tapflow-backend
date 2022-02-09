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
use App\Http\Controllers\AgencyTargetsController;
use Illuminate\Support\Facades\DB;
use App\Models\Group_member;


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
            $targetObj = new AgencyTargetsController;


            try {

                $group = Group::create($req->only(['name', 'admin_id']) + ['type' => $type]);
                $group_id = $group->id;
                $userId = $req->admin_id;
                $member = $membersObj->Insert($group_id, $userId, 1);

                $targets = $req->targets;

                if ($member == 500) {
                    $delGroup = Group::where('id', $group_id)->delete();
                    $response = Controller::returnResponse(500, 'Add group member error', []);
                    return json_encode($response);
                }
                if (isset($req->local)) {

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
                    // if (isset($targets) && count($targets) > 0) {
                    //     $targetArray = array();
                    //     foreach ($targets as $keyTarget => $target) {
                    //         $targetArray[$keyTarget]['group_id'] = $group_id;
                    //         $targetArray[$keyTarget]['category_id'] = $target;
                    //     }
                    //     $successTarget = $targetObj->addMultiRows($targetArray);
                    //     if ($successTarget == 500) {
                    //         // $delGroupTarget = Group::where('id', $group_id)->delete();
                    //         $response = Controller::returnResponse(500, 'add cast error', []);
                    //         return json_encode($response);
                    //     }
                    // }
                } else {
                    $cats = json_decode($req->categories);
                    if (isset($cats)) {

                        foreach ($cats as $key => $value) {
                            $categoryArr = array();
                            foreach ($value->subCat as $keySub => $subValue) {
                                $categoryArr[$keySub]['group_id'] = $group_id;
                                $categoryArr[$keySub]['category_id'] = $value->catId;
                                $categoryArr[$keySub]['sub_category_id'] = $subValue;
                            }
                            $add_cat = $groupCategoryObj->addMultiRows($categoryArr);
                            if ($add_cat == 500) {
                                $delGroup = Group::where('id', $group_id)->delete();
                                $response = Controller::returnResponse(500, 'add cast error', []);
                                return json_encode($response);
                            }
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
                // if (isset($req->links) && count($req->links) > 0) {
                //     DB::table('groups_links')->where('group_id', $teamId)->delete();

                //     foreach ($req->links as $keyLink => $valLink) {
                //         DB::table('groups_links')->insert([
                //             'group_id' => $teamId,
                //             'link' => $valLink
                //         ]);
                //     }
                // }
                if (isset($req->targets) && count($req->targets) > 0) {
                    // $targetArray = array();
                    // foreach ($targets as $keyTarget => $target) {
                    //     $targetArray[$keyTarget]['group_id'] = $group_id;
                    //     $targetArray[$keyTarget]['category_id'] = $target;
                    // }
                    // $successTarget = $targetObj->addMultiRows($targetArray);
                    // if ($successTarget == 500) {
                    //     // $delGroupTarget = Group::where('id', $group_id)->delete();
                    //     $response = Controller::returnResponse(500, 'add cast error', []);
                    //     return json_encode($response);
                    // }
                    // DB::table('agency_targets')->where('group_id', $teamId)->delete();

                    foreach ($req->targets as $keyLink => $valTarget) {
                        DB::table('agency_targets')->insert([
                            'group_id' => (int)$teamId,
                            'category_id' => (int)$valTarget
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
                $response = Controller::returnResponse(500, 'There IS Error Occurred', $error->getMessage());
                return json_encode($response);
            }
        }
    }
    function add_group_company(Request $req)
    {
        $rules = array(
            "name" => "required|max:255",
            "admin_id" => "required|unique:group_members,user_id|exists:clients,user_id",
            "name" => "required",
            "link" => "required",
            // "image" => "mimes:jpeg,png,jpg|max:15000"
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {

            $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
            return json_encode($response);
        }
        // $group = new Group;
        $type = 2;
        $teamObj = new CompanyController;
        $userObj = new ClientController;
        $membersObj = new GroupMembersController;

        try {
            $group = Group::create($req->only(['name', 'admin_id']) + ['type' => $type]);

            $group_id = $group->id;
            $userId = $req->admin_id;


            $teamArr = array();
            $teamArr['group_id'] = $group_id;
            $teamArr['bio'] = $req->bio;
            $teamArr['link'] = $req->link;
            $teamArr['country'] = $req->country;
            $teamArr['employees_number'] = $req->employees_number;
            $teamArr['field'] = $req->field;
            $teamArr['sector'] = $req->sector;

            $teamInfo = $teamObj->Insert($teamArr);
            $membersObj->Insert($group_id, $userId, 1);
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

            //         $attachName = time() . "-" .  $valAttach->getClientOriginalName();
            //         $attach = $valAttach;
            //         $attach->move(public_path($destPath), $attachName);
            //         DB::table('groups_attachments')->insert([
            //             'group_id' => $teamId,
            //             'attachment' => $attachName
            //         ]);
            //     }
            // }
            // if (isset($req->links) && count($req->links) > 0) {
            //     DB::table('groups_links')->where('group_id', $teamId)->delete();

            //     foreach ($req->links as $keyLink => $valLink) {
            //         DB::table('groups_links')->insert([
            //             'group_id' => $teamId,
            //             'link' => $valLink
            //         ]);
            //     }
            // }

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

            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error->getMessage());
            return json_encode($response);
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
    function getGroupIdByUserId($user_id)
    {
        $group_id = Group_member::where("user_id", $user_id)->select('group_id')
            ->first()->group_id;
        return $group_id;
    }
}
