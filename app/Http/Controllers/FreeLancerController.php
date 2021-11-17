<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Freelancer;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\UserCategoriesController;
use App\Http\Controllers\CategoriesController;
use App\Models\Category;

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
            "role" => "required|max:100",
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
            return json_encode($response);
        }
        try {
            $data = $req->except(['gender', 'dob', 'category']);
            $userId = $req->user_id;
            // print_r($data);
            $freelancer = Freelancer::create($req->except(['gender', 'dob', 'role']));

            $user = User::find($req->user_id);
            $user->gender = $req->gender;
            $user->dob = $req->dob;
            $user->role = $req->role;

            $user->save();
            foreach ($req->category as $key => $value) {
                $categoryArr = array();
                foreach ($value['subId'] as $keySub => $subValue) {
                    $categoryArr[$keySub]['user_id'] = $req->user_id;
                    $categoryArr[$keySub]['category_id'] = $value['catId'];
                    $categoryArr[$keySub]['sub_category_id'] = $subValue;
                }
                $userCategoryObj->addMultiRows($categoryArr);
            }
            if ($req->hasFile('image')) {
                $destPath = 'images/companies';
                $ext = $req->file('image')->extension();
                $imageName = "company-image-" . $userId . "." . $ext;
                $image = $req->image;
                $image->move(public_path($destPath), $imageName);
                $this->updateFiles($userId, $imageName, 'image');
            }
            if ($req->hasFile('attachment')) {
                $destPath = 'images/companies';
                $ext = $req->file('attachment')->extension();
                $attachName = "company-attachment-" . $userId . "." . $ext;
                $attach = $req->attachment;
                $attach->move(public_path($destPath), $attachName);
                $this->updateFiles($userId, $attachName, 'attachment');
            }
            $responseData = array(
                "user_id" => $req->user_id,
            );
            $response = Controller::returnResponse(200, 'user information added successfully', $responseData);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(200, 'There Is Error Occurred', $error);
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
       $freelancer= Freelancer::where('user_id', '=', $id)->first();

      
       if($freelancer === null)
       {
        return(0);
       }
       else
       {
        return(1);
       }
        
    }
  
    function updateTeamId($userId, $teamId)
    {
        Freelancer::where('user_id', $userId)->update(['team_id' => $teamId]);
    }

    function updateFiles($userId, $imageName, $filedName)
    {
        Freelancer::where('user_id', $userId)->update(array($filedName => $imageName));
    }

    private function getUserInfo($users){
        $userCategoryObj = new UserCategoriesController;
        $categoryObj = new CategoriesController;
        foreach($users as $keyUser => &$user){
            $categories = $userCategoryObj->getUserCategoriesByUserId($user->id);
            $user->categories = $categories;
        }
        return $users;

    }
}
