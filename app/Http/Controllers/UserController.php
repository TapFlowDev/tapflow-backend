<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Freelancer;
use App\Models\Client;  
use Illuminate\Support\Facades\Validator;

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
            "password" => "required |min:8|max:255",
            "type"=>"required",
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

                // $user_db = User::create($req->all());
                // $user_id = $user_db->id;

                // $user = User::find($req->user_id);
                // $user->type = $req->type;
                // $user->save();
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
            $response = array("data" => array(
                "message" => "Validation Error",
                "status" => "101",
                "error" => $validator->errors()
            ));
            return (json_encode($response));
        }
        try {
            $data = $req->except(['gender', 'dob', 'category']);

            // print_r($data);
            $freelancer = Freelancer::create($req->except(['gender', 'dob']));
            
            $user = User::find($req->user_id);
            $user->gender = $req->gender;
            $user->dob = $req->dob;
            
            $user->save();
            foreach($req->category as $key => $value){
                // dd($value);
                $userCategoryObj->Insert($value, $req->user_id);

            }
            $response = array("data" => array(
                "message" => "user information added successfully",
                "status" => "200",
                "user_id" => $req->user_id,
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
    // add company
    function Insert_client(Request $req)
    {
        
        $rules = array(
            "user_id" => "required",
            "bio" => "required",
            "role" => "required",
            "attachment" => "required",
            "image" => "required",
            "country" => "required",

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
        try {
            $data = $req->except(['gender', 'dob']);
            $client = Client::create($data);
            $user = User::find($req->user_id);
            $user->dob = $req->dob;
            $user->gender = $req->gender;
         
            $user->save();

            $response = array("data" => array(
                "message" => "user information added successfully",
                "status" => "200",
                "user_id" => $req->user_id,
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
    function logout(Request $req)
    { 
        
        // $token=$req->header("Authorization");
        
        // $token=substr($token,7);
       
        
        // $user = User::where("token",$token)->first();


        // $user->token="NULL";

        // $user->save();
        // $req->user()->currentAccessToken()->delete();
        dd($req);
        
        // $user = request()->user();
    //   /  $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        // $response = array("data" => array(
        //     "message" => "Logout successfully", 
        //     "status" => "200",
        // ));

        // return (json_encode($response));
       
        // auth()->user()->tokens()->delete();
    
    }
    //get free lancer info by id
    function get_freelancer_info($id)
    {   try{
        $user=User::where('id',$id)->get();
        $freelancer=Freelancer::where('user_id',$id)->get();
        $response = array("data" => array(
            "user" => $user,
            "info" =>$freelancer,
            "status" => "200",
        ));
        return (json_encode($response));
    }
    catch(Exception $error)
    {
        $response = array("data" => array(
            "message" => "There IS Error Occurred",
            "status" => "500",
            "error" => $error,
        ));

        return (json_encode($response));   
    }
    }
    //get client info by id
    function get_client_info($id)
    {
        try{
            $user=User::where('id',$id)->get();
            $client=Client::where('user_id',$id)->get();
            $response = array("data" => array(
                "user" => $user,
                "info" =>$client,
                "status" => "200",
            ));
            return (json_encode($response));
        }
        catch(Exception $error)
        {
            $response = array("data" => array(
                "message" => "There IS Error Occurred",
                "status" => "500",
                "error" => $error,
            ));
    
            return (json_encode($response));   
        }
    }
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
}
