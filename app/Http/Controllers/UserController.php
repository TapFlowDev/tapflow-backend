<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
 

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use ApiResponser;
use App\Models\Rate;
use Exception;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\Auth;

use function PHPSTORM_META\type;
use function PHPUnit\Framework\isEmpty;
use App\Http\Controllers\UserCategoriesController;


// user types 1,2 1:freelancer 2:client
class UserController extends Controller
{
    //add freelancer
    function add_user(Request $req)
    {
        // dd($req);
        $rules = array(
            "first_name" => "required|max:255",
            "last_name" => "required|max:255",
            "email" => "email|required|max:255|unique:users",
            "password" => "required|min:8|max:255",
            "type"=>"required|max:1|between:1,2",
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $response = array("data" => array(
                "message" => "Validation Error",
                "status" => "101",
                "error" => $validator->errors()
            ));
            return (json_encode($response));
        } else {

            $user = new User;

            try {

                $user->first_name=$req->first_name;
                $user->last_name=$req->last_name;
                $user->email =$req->email ;
                $user->	password=$req->	password;
                $user->	type=$req->	type;
                $user->save();
                $user_id = $user->id;



                $response = array("data" => array(
                    "message" => "user added successfully",
                    "status" => "200",
                    "user_id" => $user_id,
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

   
   
    //login dunction using Sanctum auth token
    function login(Request $req)
    {

        try {
            $rules = array(
                "email" => "email|required",
                "password" => "required",
            );
            $validator = Validator::make($req->all(), $rules);
            if ($validator->fails()) {
                $response = array("data" => array(
                    "message" => "Validation Error",
                    "status" => "101",
                    "error" => $validator->errors()
                ));

                return (json_encode($response));
            }
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                $response = array("data" => array(
                    "message" => "Unauthorized",
                    "status" => "422",
                ));

                return (json_encode($response));
            }

            $user = User::where('email', $req->email)->first();
            if (!Hash::check($req->password, $user->password)) {
                $response = array("data" => array(
                    "message" => "The Password does not match",
                    "status" => "422",
                ));

                return (json_encode($response));
            }
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            $user->token = $tokenResult;
            $user_type=$user->type;
            $user->save();
            
            $response = array("data" => array(
                "message" => "login successfully",
                "status" => "200",
                "user_id"=>$user->id,
                "userToken" => $tokenResult,
                "tokenType" => "Bearer",
                "user_type"=>$user_type,
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
    function signout(Request $req)
    { 
        
        try{
        $user= User::find($req->user_id);
        $user->token="Null";
        $user->save();
        $token=DB::table('personal_access_tokens')->where('tokenable_id', $req->user_id)->delete();
        $response = array("data" => array(
            "message" => "Logout successfully", 
            "status" => "200",
        ));

        return (json_encode($response));
       
    }
    catch(Exception $error)
    {
        $response = array("data" => array(
            "message" => "something wrong", 
            "status" => "500",
            "error"=>$error,
        ));

        return (json_encode($response));

    }
    
    }
  
    //get client info by id
  
    function getAllUsers(){
        return User::all();
    }
    //update row according to row id
    function Update($id)
    {
        
    }
    //delete row according to row id
    function Delete($id)
    {
        
    } 
    
    function getUserById($id){
        return User::find($id)->first();
    }

    function getUserById($id){
        return User::find($id)->first();
    }
}
