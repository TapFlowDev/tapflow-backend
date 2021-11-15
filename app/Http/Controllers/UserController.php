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
    function internal_login($email,$password)
    {
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            $responseData = array();
            $response=Controller::returnResponse(422, 'Unauthorized', $responseData);
            return json_encode($response);
        }

        $user = User::where('email',$email)->first();
        if (!Hash::check($password, $user->password)) {
            $responseData = array();
           $response=Controller::returnResponse(422, 'The Password does not match', $responseData);
            return json_encode($response);

        }
        $tokenResult = $user->createToken('authToken')->plainTextToken;
        $user->token = $tokenResult;
        $user_type=$user->type;
        $user->save();
        $response = array("data" => array(
            "user_id"=>$user->id,
            "user_type"=>$user_type,
            "token" => $tokenResult,
            "tokenType" => "Bearer",
        ));
        return($response);
    }
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
            $responseData=$validator->errors();
            $response=Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        } else {
            try {
                $user=User::create($req->all());
               $responseData= $this->internal_login($req->email,$req->password);
                $response=Controller::returnResponse( 200,"user added successfully", $responseData);
                return (json_encode($response));
            } catch (\Exception $error) {
                $responseData=$error;
                $response=Controller::returnResponse( 500,"There IS Error Occurred", $responseData);
                return (json_encode($response));
            }                                                           
        }
    }
    /*fgfdg*/

   
   
    //login function using Sanctum auth token
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
                $responseData=$validator->errors();
                $response=Controller::returnResponse( 101,"Validation Error", $responseData);

                return (json_encode($response));
            }
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                $responseData = array();
                $response=Controller::returnResponse( 422,"Unauthorized Error", $responseData);
                return (json_encode($response));
            }

            $user = User::where('email', $req->email)->first();
            if (!Hash::check($req->password, $user->password)) {
                $responseData = array();
                $response=Controller::returnResponse( 422,"The Password does not match", $responseData);
                return (json_encode($response));
            }
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            $user->token = $tokenResult;
            $user_type=$user->type;
            $user->save();
            //check the user info is filed or not 
            
            $responseData = array("data" => array(
                "user_id"=>$user->id,
                "userToken" => $tokenResult,
                "tokenType" => "Bearer",
                "user_type"=>$user_type,
            ));
            $response=Controller::returnResponse( 200,"login successfully", $responseData);
            return (json_encode($response));
        } catch (Exception $error) {
            $responseData = array("error" => $error,);
            $response=Controller::returnResponse( 500,"There IS Error Occurred", $responseData);
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

   
}
