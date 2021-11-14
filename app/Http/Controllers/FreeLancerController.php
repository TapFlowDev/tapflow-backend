<?php 

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Freelancer;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\DB;
class FreeLancerController extends Controller
{
    //add row 
    function Insert_freelancer(Request $req)
    {
        $userCategoryObj = new UserCategoriesController;

        $rules = array(
            "user_id" => "required",
            "bio" => "required",
            "hourly_rate" => "required",
            "attachment" => "required",
            "image" => "required",
            "country" => "required",
            "gender" => "max:1",
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            // $response = array("data" => array(
            //     "message" => "Validation Error",
            //     "status" => "101",
            //     "error" => $validator->errors()
            // ));
            // return (json_encode($response));
            $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
            return json_encode($response);
        }
        try {
            $data = $req->except(['gender', 'dob', 'category']);

            // print_r($data);
            $freelancer = Freelancer::create($req->except(['gender', 'dob']));

            $user = User::find($req->user_id);
            $user->gender = $req->gender;
            $user->dob = $req->dob;

            $user->save();
            // foreach($req->category as $key => $value){
            //     // dd($value);
            //     $userCategoryObj->Insert($value, $req->user_id);

            // }
            foreach ($req->category as $key => $value) {
                $categoryArr = array();
                foreach ($value['subId'] as $keySub => $subValue) {
                    $categoryArr[$keySub]['user_id'] = $req->user_id;
                    $categoryArr[$keySub]['category_id'] = $value['catId'];
                    $categoryArr[$keySub]['sub_category_id'] = $subValue;
                }
                $userCategoryObj->addMultiRows($categoryArr);
            }
            // $response = array("data" => array(
            //     "message" => "user information added successfully",
            //     "status" => "200",
            //     "user_id" => $req->user_id,
            // ));
            // return (json_encode($response));
            $responseData = array(
                "user_id" => $req->user_id,
            );
            $response = Controller::returnResponse(200, 'user information added successfully', $responseData);
            return json_encode($response);
        } catch (Exception $error) {

            // $response = array("data" => array(
            //     "message" => "There IS Error Occurred",
            //     "status" => "500",
            //     "error" => $error,
            // ));
            // return (json_encode($response));
            $response = Controller::returnResponse(200, 'There Is Error Occurred', $error);
            return json_encode($response);
        }
    }
    //get free lancer info by id
    function get_freelancer_info($id)
    {
        try {
            // $user = User::where('id', $id)->get();
            // $freelancer = Freelancer::where('user_id', $id)->get();
            $user =DB::table('users')
            ->join('freelancers','users.id','=','freelancers.user_id')
            ->where('users.id',$id)
            ->get();

            $response = array("data" => array(
                "user"=>$user,
                "status" => "200",
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


    //update row according to row id
    function Update($id)
    {
    }
    //delete row according to row id
    function Delete($id)
    {
    }

    function updateTeamId($userId, $teamId)
    {
        Freelancer::where('user_id', $userId)->update(['team_id' => $teamId]);
    }
}
