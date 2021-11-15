<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\DB;
class ClientController extends Controller
{
    function Insert_client(Request $req)
    {
     
        
        $rules = array(
            "user_id" => "required|exists:users,id",
            "bio" => "required",
            "role" => "required",
            "country" => "required",
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
            $data = $req->except(['gender', 'dob']);
            $client = Client::create($data);
            $user = User::find($req->user_id);
            $user->dob = $req->dob;
            $user->gender = $req->gender;
            $user->save();
            $userId = $req->user_id;

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
    //add row 
    function get_client_info($id)
    {
        try{
            $user= $user =DB::table('users')
            ->leftJoin('clients','users.id','=','clients.user_id')
            ->where('users.id',$id)
            ->get();
            // $response = array("data" => array(
            //     "user" => $user,
            //     "status" => "200",
            // ));
            // return (json_encode($response));


            $response = Controller::returnResponse(200, 'user information found', $user);
            return json_encode($response);
        }
        catch(Exception $error)
        {
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
       $freelancer= Client::where('user_id', '=', $id)->first();

      
       if($freelancer === null)
       {
        return(0);
       }
       else
       {
        return(1);
       }
        
    }
    function updateTeamId($userId, $teamId){
        Client::where('user_id', $userId)->update(['company_id'=>$teamId]);
    }
    function updateFiles($userId, $imageName, $filedName)
    {
        Client::where('user_id', $userId)->update(array($filedName => $imageName));
    }
}
