<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Freelancer;
use App\Models\Client;

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
use App\Http\Controllers\FreeLancerController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InviteUsersController;
use App\Http\Controllers\GroupMembersController;
use Illuminate\Support\Facades\Http;
use App\Models\country;
use App\Models\User_link;
use Newsletter;


// user types 1,2 1:freelancer 2:client
class UserController extends Controller
{
    function internal_login($email, $password)
    {
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            $responseData = array();
            $response = Controller::returnResponse(422, 'Unauthorized', $responseData);
            return json_encode($response);
        }

        $user = User::where('email', $email)->first();
        if (!Hash::check($password, $user->password)) {
            $responseData = array();
            $response = Controller::returnResponse(422, 'The Password does not match', $responseData);
            return json_encode($response);
        }
        $tokenResult = $user->createToken('authToken')->plainTextToken;
        $user->token = $tokenResult;
        $user_type = $user->type;
        $user->save();


        $response = array(
            "user_id" => $user->id,
            "user_type" => $user_type,
            "userToken" => $tokenResult,
            "tokenType" => "Bearer",
            "privileges" => "0",
        );
        return ($response);
    }
    function Register(Request $req)
    {
        $rules = array(
            "first_name" => "required|max:255",
            "last_name" => "required|max:255",
            "email" => "email|required|max:255|unique:users",
            "password" => "required|min:8|max:255",
            "type" => "required|max:1|gt:0|lt:3",
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $responseData = $validator->errors();
            $response = Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        } else {
            try {
                $user = User::create($req->all() + ['name' => $req->first_name . " " . $req->last_name, 'terms' => 1]);
                $array = array("user_id" => $user->id, 'type_freelancer' => (int)$req->type);
                if ($req->type == 1) {
                    $freelancer = Freelancer::create($array);
                    $mailchimpUserType = 'agency-member';
                } elseif ($req->type == 2) {
                    $freelancer = Client::create($array);
                    $mailchimpUserType = 'company-member';
                }
                if ($req->hasFile('image')) {
                    $destPath = 'images/users';
                    // $ext = $req->file('image')->getClientOriginalExtension();
                    // $imageName = "user-image-" . $userId . "." . $ext;
                    // $imageName = now() . "-" . $req->file('image')->getClientOriginalName();
                    $imageName = time() . "-" . $req->file('image')->getUserOriginalName();
                    // $imageName = $req->file('image') . "user-image-" . $userId . "." . $ext;

                    $img = $req->image;

                    $img->move(public_path($destPath), $imageName);
                    $this->updateFiles($user->id, $imageName, 'image');
                }
                //Newsletter::subscribeOrUpdate($req->email, ['FNAME'=>$req->first_name, 'LNAME'=>$req->last_name,'ROLE'=>$req->role, "UTYPE"=>$mailchimpUserType, 'ADMIN'=>'not admin'], 'Tapflow');
                // dd(Newsletter::getLastError());
                $responseData = $this->internal_login($req->email, $req->password);
                $response = Controller::returnResponse(200, "user added successfully", $responseData);
                return (json_encode($response));
            } catch (\Exception $error) {
                $responseData = $error->getMessage();
                $response = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
                return (json_encode($response));
            }
        }
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
            "type" => "required|max:1|gt:0|lt:3",
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {

            $responseData = $validator->errors();
            $response = Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        } else {
            try {
                $user = User::create($req->all());
                $responseData = $this->internal_login($req->email, $req->password);
                $response = Controller::returnResponse(200, "user added successfully", $responseData);
                return (json_encode($response));
            } catch (\Exception $error) {
                $responseData = $error;
                $response = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
                return (json_encode($response));
            }
        }
    }

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
                $responseData = $validator->errors();
                $response = Controller::returnResponse(101, "Validation Error", $responseData);

                return (json_encode($response));
            }
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                $responseData = array();
                $response = Controller::returnResponse(422, "Unauthorized Error", $responseData);
                return (json_encode($response));
            }
            $user = User::where('email', $req->email)->first();
            if (!Hash::check($req->password, $user->password)) {

                $responseData = array();
                $response = Controller::returnResponse(422, "The Password does not match", $responseData);
                return (json_encode($response));
            }
            $tokenResult = $user->createToken('authToken')->plainTextToken;
            $user->token = $tokenResult;
            $user_type = $user->type;
            $user->save();

            $member = new GroupMembersController;
            $check_member = $member->checkIfExists($user->id);

            //check the user info is filed or not 
            if ($user_type == 1) {
                $freelancer = new FreeLancerController;
                $check = $freelancer->checkIfExists($user->id);
            } elseif ($user_type == 2) {
                $client = new ClientController;
                $check = $client->checkIfExists($user->id);
            }

            $responseData =  array(
                "user_id" => $user->id,
                "userToken" => $tokenResult,
                "tokenType" => "Bearer",
                "user_type" => $user_type,
                "completed" => $check,
                "privileges" => $check_member,

            );
            $response = Controller::returnResponse(200, "login successfully", $responseData);
            return (json_encode($response));
        } catch (Exception $error) {
            $responseData = array("error" => $error->getMessage(),);
            $response = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
            return (json_encode($response));
        }
    }
    function signout(Request $req)
    {

        try {
            $user = User::find($req->user_id);
            $user->token = "Null";
            $user->save();
            $token = DB::table('personal_access_tokens')->where('tokenable_id', $req->user_id)->delete();

            $response = Controller::returnResponse(200, 'Logout successfully', array());
            return json_encode($response);
        } catch (Exception $error) {

            $response = Controller::returnResponse(500, 'something wrong', $error);
            return json_encode($response);
        }
    }

    //get client info by id

    function getAllUsers()
    {
        return User::all();
    }
    //update row according to row id
    function UpdateUserInfo(Request $req)
    {
        try {
            $user = User::where('id', $req->user_id)->update(["first_name" => $req->first_name, "last_name" => $req->last_name, 'role' => $req->role]);

            $response = Controller::returnResponse(200, 'successfully', []);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'something wrong', $error);
            return json_encode($response);
        }
    }
    //delete row according to row id
    function Delete($id)
    {
    }

    function getUserById($id)
    {
        return User::find($id);
    }

    function newRegister(Request $req)
    {

        // dd($req);
        $rules = array(
            "first_name" => "required|max:255",
            "last_name" => "required|max:255",
            "email" => "email|required|max:255|unique:users",
            "password" => "required|min:8|max:255",
            "type" => "required|max:1|gt:0|lt:3",
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {

            $responseData = $validator->errors();
            $response = Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        }

        try {
            $user = User::create($req->all());
            $tokenResult = $user->createToken('myapptoken')->plainTextToken;
            $responseData = array(
                "userId" => $user->id,
                "token" => $tokenResult
            );
            return $responseData;
            // $response = Controller::returnResponse(200, "user added successfully", $responseData);
            // return (json_encode($response));
        } catch (\Exception $error) {
            $responseData = $error;
            $response = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
            return (json_encode($response));
        }
    }

    function newLogout(Request $req)
    {
        try {
            $req->user()->currentAccessToken()->delete();
            // $responseData = array(
            //     "msg" => "loged out"
            // );
            $response = Controller::returnResponse(200, 'user logged out', []);
            return json_encode($response);
        } catch (\Exception $error) {
            $responseData = $error;
            $response = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
            return (json_encode($response));
        }
    }

    function newLogin(Request $req)
    {
        $rules = array(
            "email" => "email|required|max:255",
            "password" => "required|min:8|max:255"
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {

            $responseData = $validator->errors();
            $response = Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        }
        $password = $req->password;
        $user = User::where('email', $req->email)->first();

        if (!$user || !Hash::check($req->password, $user->password)) {
            $responseData = array(
                "msg" => "unauthrized"
            );
            return $responseData;
        }
        $tokenResult = $user->createToken('myapptoken')->plainTextToken;
        $responseData = array(
            "userId" => $user->id,
            "token" => $tokenResult
        );
        return $responseData;
    }

    public function getUserOriginalName()
    {
        return $this->originalName;
    }
    function updateFiles($userId, $imageName, $filedName)
    {
        Freelancer::where('user_id', $userId)->update(array($filedName => $imageName));
    }
    function updaterole(Request $req)
    {

        $rules = array(
            "user_id" => "required|exists:users,id",
            "role" => "required|max:255"
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {

            $responseData = $validator->errors();
            $response = Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        } else {
            try {
                Freelancer::where('user_id', $req->user_id)->update(['role' => $req->role]);
                $response = Controller::returnResponse(200, 'successfully', []);
                return json_encode($response);
            } catch (Exception $error) {
                $response = Controller::returnResponse(500, "There IS Error Occurred", $error);
                return (json_encode($response));
            }
        }
    }
    function checkTokenExpiration(Request $req)
    {
        $response = Controller::returnResponse(200, 'Valid Token', []);
        return (json_encode($response));
    }
    function updateTerms(Request $req)
    {
        $user = $req->user();
        try {
            $user->terms = 1;
            $user->save();
            $response = Controller::returnResponse(200, 'Terms and Condition updated successfully', []);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "There IS Error Occurred", $error);
            return (json_encode($response));
        }
    }
    function clientSignUpProcess(Request $req)
    {
        $cats = gettype($req->project['categories']);
        // $categoryArr = array();
        // if (isset($cats)) {
        //     foreach ($cats as $key => $value) {
        //         foreach ($value->subCat as $keySub => $subValue) {
        //             $categoryArr[$keySub]['project_id'] = 11;
        //             $categoryArr[$keySub]['category_id'] = $value->catId;
        //             $categoryArr[$keySub]['sub_category_id'] = $subValue;
        //         }
               
        //     }
        // }
        $response = Controller::returnResponse(200, "user added successfully", $cats);
        return $response;
        $groupObj = new GroupController;
        $teamObj = new CompanyController;
        $projectObj = new ProjectController;
        $userArr = array(
            'first_name' => $req->user['first_name'],
            'last_name' => $req->user['last_name'],
            'email' => $req->user['email'],
            'password' => $req->user['password'],
            'type' => 2,
        );
        $userResponse = $this->registerClient($userArr);
        if ($userResponse['error']) {
            return json_encode($userResponse['error']);
        }
        $admin = $userResponse['user'];
        $teamArr =  array(
            'admin_id' => $admin['id'],
            'name' => $req->user['company_name'],
            'country' => $req->user['country'],
            'field' => $req->user['field'],
            'sector' => $req->user['sector'],
        );
        $groupResponse = $groupObj->addCompany($teamArr);
        if ($groupResponse['error']) {
            return json_encode($groupResponse['error']);
        }
        $company = $groupResponse['company'];

        $teamId = $company['id'];
        if ($req->hasFile('image')) {
            $destPath = 'images/companies';
            $imageName = time() . "-" . $req->file('image')->getClientOriginalName();
            $img = $req->image;
            $img->move(public_path($destPath), $imageName);
            $teamObj->updateFiles($teamId, $imageName, 'image');
        }
        $projectArr = $req->project;
        $projectArr['user_id'] = $admin['id'];
        $projectResponse = $projectObj->addProjectSignUp($projectArr);
        if ($projectResponse['error']) {
            return json_encode($projectResponse['error']);
        }
        $project = $projectResponse['project'];
        $responseData = $this->clientInternalLogin($admin['email'], $req->user['password']);
        $response = Controller::returnResponse(200, "user added successfully", $responseData);
        return $response;
    }

    function registerClient($arr)
    {
        $returnData['error'] = [];
        $returnData['user'] = [];
        $rules = array(
            "first_name" => "required|max:255",
            "last_name" => "required|max:255",
            "email" => "email|required|max:255|unique:users",
            "password" => "required|min:8|max:255",
        );
        $validator = Validator::make($arr, $rules);
        if ($validator->fails()) {
            $responseData = $validator->errors();
            $response['error'] = Controller::returnResponse(101, "Validation Error", $responseData);
            return $response;
        }
        // return $returnData;
        try {
            // $user = User::find(156);
            $user = User::create($arr + ['name' => $arr['first_name'] . " " . $arr['last_name'], 'terms' => 1]);
            $array = array("user_id" => $user->id, 'type_freelancer' => (int)$arr['type']);
            $freelancer = Client::create($array);
            $mailchimpUserType = 'company-member';
            // Newsletter::subscribeOrUpdate($req->email, ['FNAME'=>$req->first_name, 'LNAME'=>$req->last_name,'ROLE'=>$req->role, "UTYPE"=>$mailchimpUserType, 'ADMIN'=>'admin'], 'Tapflow');
            // dd(Newsletter::getLastError());
            // $responseData = $this->internal_login($req->email, $req->password);
            // $returnData = Controller::returnResponse(200, "user added successfully", $responseData);
            $returnData['user'] = $user->toArray();
            return $returnData;
        } catch (Exception $error) {
            $responseData = $error->getMessage();
            $response['error']  = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
            return $response;
        }
    }
    function clientInternalLogin($email, $password)
    {
        $credentials = array(
            'email' => $email,
            'password' => $password
        );
        if (!Auth::attempt($credentials)) {
            $responseData = array();
            $response = Controller::returnResponse(422, 'Unauthorized', $responseData);
            return json_encode($response);
        }

        $user = User::where('email', $email)->first();
        if (!Hash::check($password, $user->password)) {
            $responseData = array();
            $response = Controller::returnResponse(422, 'The Password does not match', $responseData);
            return json_encode($response);
        }
        $tokenResult = $user->createToken('authToken')->plainTextToken;
        $user->token = $tokenResult;
        $user_type = $user->type;
        $user->save();


        $response = array(
            "user_id" => $user->id,
            "user_type" => $user_type,
            "userToken" => $tokenResult,
            "tokenType" => "Bearer",
            "privileges" => 2,
        );
        return ($response);
    }
}
