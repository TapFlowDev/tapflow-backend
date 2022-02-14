<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Freelancer;
use App\Models\User;
use App\Models\User_link;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UserCategoriesController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\GroupMembersController;
use App\Models\Category;
use PhpParser\Node\Expr\Isset_;
use Symfony\Component\VarDumper\Cloner\Data;

class FreeLancerController extends Controller
{
    //add row 
    function Insert_freelancer(Request $req)
    {
        $userCategoryObj = new UserCategoriesController;
        $rules = array(
            "user_id" => "required|exists:users,id",
            "bio" => "required",
            "hourly_rate" => "required",
            "country" => "required",
            "gender" => "max:1",
            "role" => "max:100",
            "experience" => "gt:0|lt:100",
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
            return json_encode($response);
        }
        try {
            $data = $req->except(['gender', 'dob', 'category']);
            $userId = $req->user_id;
            // $userId = 4;

            // print_r($data);

            // $tools = serialize($req->tools);
            if (isset($req->tools)) {
                $tools = serialize($req->tools);
            } else {
                $tools = serialize(array());
            }
            $freelancer = Freelancer::create($req->except(['gender', 'dob', 'role', 'tools', 'image']) + ['tools' => $tools]);
            // $freelancer = Freelancer::create($req->except(['gender', 'dob', 'role', 'image']) );

            $user = User::find($req->user_id);
            $user->gender = $req->gender;
            $user->dob = $req->dob;
            $user->role = $req->role;
            $user->save();
            $cats = json_decode($req->categories);
            if (isset($cats)) {
                foreach ($cats as $key => $value) {
                    $categoryArr = array();
                    foreach ($value->subCat as $keySub => $subValue) {
                        $categoryArr[$keySub]['user_id'] = $req->user_id;
                        $categoryArr[$keySub]['category_id'] = $value->catId;
                        $categoryArr[$keySub]['sub_category_id'] = $subValue;
                    }
                    $userCategoryObj->addMultiRows($categoryArr);
                }
            }

            if ($req->hasFile('image')) {
                $destPath = 'images/users';
                // $ext = $req->file('image')->getClientOriginalExtension();
                // $imageName = "user-image-" . $userId . "." . $ext;
                // $imageName = now() . "-" . $req->file('image')->getClientOriginalName();
                $imageName = time() . "-" . $req->file('image')->getClientOriginalName();
                // $imageName = $req->file('image') . "user-image-" . $userId . "." . $ext;

                $img = $req->image;

                $img->move(public_path($destPath), $imageName);
                $this->updateFiles($userId, $imageName, 'image');
            }
            /*
            if ($req->hasFile('attachment')) {
                // dd($req);
                $destPath = 'images/users';
                DB::table('user_attachments')->where('user_id', $userId)->delete();
                // dd($req->attachment);
                foreach ($req->attachment as $keyAttach => $valAttach) {
                    $ext = $valAttach->extension();
                    
                    // $attachName = "user-attachment-" . $userId . "-" . $keyAttach . "." . $ext;
                    $attachName = mt_rand(100000,999999) . "-" . $valAttach->getClientOriginalName();
                    $attach = $valAttach;
                    $attach->move(public_path($destPath), $attachName);
                    DB::table('user_attachments')->insert([
                        'user_id' => $userId,
                        'attachment' => $attachName
                    ]);

                }
            }
            */

            if (isset($req->links) && count($req->links) > 0) {
                DB::table('user_links')->where('user_id', $userId)->delete();

                foreach ($req->links as $keyLink => $valLink) {
                    DB::table('user_links')->insert([
                        'user_id' => $userId,
                        'link' => $valLink
                    ]);
                }
            }
            $responseData = array(
                "user_id" => $req->user_id,
            );
            $response = Controller::returnResponse(200, 'user information added successfully', $responseData);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There Is Error Occurred', $error);
            return json_encode($response);
        }
    }
    //get free lancer info by id
    function get_freelancer_info($id)
    {
        try {
            $user = DB::table('users')
                ->leftJoin('freelancers', 'users.id', '=', 'freelancers.user_id')
                ->where('users.id', $id)
                ->select('users.id','users.first_name','users.last_name','users.email','users.role','users.dob', 'users.gender','users.type','users.token',
                'freelancers.type_freelancer','freelancers.bio','freelancers.country','freelancers.image','freelancers.tools',)
                ->get();
               

            $user = $this->getUserInfo($user)->first();
            $response = Controller::returnResponse(200, 'data found', $user);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error);
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

    function checkIfExists($id)
    {
        $freelancer = Freelancer::where('user_id', '=', $id)->first();


        if ($freelancer === null) {
            return (0);
        } else {
            return (1);
        }
    }

    function updateType($userId, $type)
    {
        Freelancer::where('user_id', $userId)->update(array('type' => $type));
    }

    function updateFiles($userId, $imageName, $filedName)
    {
        Freelancer::where('user_id', $userId)->update(array($filedName => $imageName));
    }

    private function getUserInfo($users)
    {
        $userCategoryObj = new UserCategoriesController;
        $categoryObj = new CategoriesController;
        $membersObj = new GroupMembersController;
        foreach ($users as $keyUser => &$user) {
            $categories = $userCategoryObj->getUserCategoriesByUserId($user->id);
            $user->categories = $categories;
            $user->tools = unserialize($user->tools);
            $links = User_link::select('link')->where('user_id', $user->id)->get();
            if (count($links) > 0) {
                // $user->links = $links;
                $user->links = array_column($links->toArray(), 'link');
                // $user->linksType = gettype($links);
            } else {
                $user->links = [];
            }
            if ($user->image != '') {
                $image = asset('images/users/' . $user->image);
                $user->image = $image;
            }else{
                $user->image = asset('images/profile-pic.jpg');
            }
            $groupId = $membersObj->getGroupId($user->id);
            if ($groupId != '') {
                $user->team_id = $groupId->group_id;
                $privileges = $membersObj->getUserPrivileges($user->id);
                $user->privileges=$privileges;
            } else {
                $user->team_id =  null;
            }
        }
        return $users;
    }
    function update_Bio(Request $req)
    {
        try {
            $freelancer = Freelancer::where('user_id', $req->user_id)->update(['bio' => $req->bio]);
            $response = Controller::returnResponse(200, 'User information updated successfully', array());
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error);
            return json_encode($response);
        }
    }
    function update_tools(Request $req)
    {
        try {
            $user_id = $req->user_id;
            $freelancer = Freelancer::where("user_id", $user_id)->update(['tools' => serialize($req->tools)]);
            $response = Controller::returnResponse(200, 'User tools updated successfully', array());
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error);
            return json_encode($response);
        }
    }
    function updateFreelancerImage(Request $req)
    {
        // ini_set('memory_limit','256M');
        $rules = array(
            "user_id" => "required|exists:users,id",
            "image" => "required|mimes:png,jpg,jpeg|max:5000"
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
            return json_encode($response);
        } 
            $id = $req->user_id;
            $user_image = Freelancer::where('user_id', $id)->select('image')->first()->image;
            $image_path = "images/users/" . $user_image;
             File::delete(public_path($image_path));
            if ($req->hasFile('image')) {
                $destPath = 'images/users';
                $ext = $req->file('image')->extension();
                $imageName = time() . "-" . $req->file('image')->getClientOriginalName();
                // $imageName = $req->file('image') . "user-image-" . $userId . "." . $ext;
                $img = $req->image;
                $img->move(public_path($destPath), $imageName);
                $this->updateFiles($id, $imageName, 'image');
                $response=Controller::returnResponse(200,'successful',[]);
                return json_encode($response);
            }
        
    }
}