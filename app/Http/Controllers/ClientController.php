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
            "country" => "required",
            "experience" => "gt:0|lt:100",

        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
            return json_encode($response);
        
        }
        try {
            $data = $req->except(['gender', 'dob', 'role']);
            $client = Client::create($data);
            $user = User::find($req->user_id);
            $user->dob = $req->dob;
            $user->gender = $req->gender;
            $user->role = $req->role;
            $user->save();
            $userId = $req->user_id;

            if ($req->hasFile('image')) {
                $destPath = 'images/users';
                $ext = $req->file('image')->extension();
                $imageName =  mt_rand(100000,999999) . "-" . $req->file('image')->getClientOriginalName();
                $image = $req->image;
                $image->move(public_path($destPath), $imageName);
                $this->updateFiles($userId, $imageName, 'image');
            }
            if ($req->hasFile('attachment')) {
                $destPath = 'images/users';
                DB::table('user_attachments')->where('user_id', $userId)->delete();
                foreach ($req->attachment as $keyAttach => $valAttach) {
                    $ext = $valAttach->extension();

                    $attachName =  $attachName = mt_rand(100000,999999) . "-" . $valAttach->getClientOriginalName();
                    $attach = $valAttach;
                    $attach->move(public_path($destPath), $attachName);
                    DB::table('user_attachments')->insert([
                        'user_id' => $userId,
                        'attachment' => $attachName
                    ]);
                }
            }
            if (count($req->links) > 0) {
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
        } catch (\Exception $error) {
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

            $response = Controller::returnResponse(200, 'user information found', $user);
            return json_encode($response);
        }
        catch(Exception $error)
        {  

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
