<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invite_code;

use Illuminate\Support\Str;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FreeLancerController;
use App\Http\Controllers\ClientController;
use App\Models\Freelancer;
use Illuminate\Support\Arr;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendInvitation;
use App\Http\Controllers\GroupMembersController;


class InviteUsersController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
    }
    //update row according to row id
    function Update($id)
    {
    }
    //delete row according to row id
    function Delete($id)
    {
    }

    function sendInvitation(Request $req)
    {
        // dd($req);
        $rules=array(
            "user_id"=>"required|exists:group_members",
            "group_id"=>"required|exists:groups,id",
            "emails"=>"required|array"
        );
        $validator=Validator::make($req->all(),$rules);
        if($validator->fails())
        {
            $response=Controller::returnResponse(101,"Validation error",$validator->errors());
            return json_encode($response);
        }
        $groupObj = new GroupController;
        $groupData = $groupObj->getGroupById($req->group_id);
        // dd(gettype($groupData->verified));
        // if ($groupData->verified != 1) {
        //     $response = Controller::returnResponse(422, 'group not verified', $groupData);
        //     return json_encode($response);
        // }
        $arr['user_id'] = $req->user_id;
        $arr['group_id'] = $req->group_id;
        $invite_code = array();
        $emails = $req->emails;
        foreach ($req->emails as $key => $value) {
            $inviteEmails = array();
            $inviteEmails['email'] = $value;
            $inviteEmails['code'] = $this->generateCode();
            $inviteEmails['link_token'] = $this->generateLinkToken();
            $inviteEmails['user_id'] = $arr['user_id'];
            $inviteEmails['group_id'] = $arr['group_id'];
            $invite_code[$key]['id'] = Invite_code::create($inviteEmails);
            $details = [
                'link' => env('APP_URL') . '/r/?r=' . $inviteEmails['link_token'],
                'code' => $inviteEmails['code']
            ];
            Mail::to($value)->send(new SendInvitation($details));
        }
        $response = Controller::returnResponse(200, 'users invited successfully', $invite_code);
        return json_encode($response);
        // // return $invite_code;
        // $url = env('APP_URL') . "api/" . $this->generateLinkToken();
        // // dd($arr, $invite_code, $dd);
        // /*
        // send email 
        // */
    }
    function getDataByToken($token)
    {
        $groupObj = new GroupController;
        try {
            $group = Invite_code::where('link_token', $token)->get()->first();
            if ($group->status == 0 && $group->expired == 0) {
                $groupData = $groupObj->getGroupById($group->group_id);

                $response = Controller::returnResponse(200, 'data found', $groupData);
                return json_encode($response);
            } else {
                $response = Controller::returnResponse(101, 'Invitation expired', array());
                return json_encode($response);
            }
        } catch (\Exception $error) {
            $response = Controller::returnResponse(101, 'Invitation expired', $error);
            return json_encode($response);
        }
    }
    function updateInvitation(Request $req)
    {
        // dd($req);
        $status = $req->accept; // must be 1 (approved) or 2 (denied)
        $rules = array(
            "accept" => "required|gt:0|lt:3",
            "link_token" => "required|exists:invite_codes,link_token",
            "user_id" => "required|exists:users,id"
        );
        $userObj = new UserController;
        $group_member_obj=new GroupMembersController();
        $userInfo = $userObj->getUserById($req->user_id);
        $userType = $userInfo->type;

        if ($userType == 1) {
            $userTypeObj = new FreeLancerController();
        } elseif ($userType == 2) {
            $userTypeObj = new ClientController();
        } else {
            $responseData = array(
                "error" => 'User type not right'
            );
            $response = Controller::returnResponse(500, 'User type not right', $responseData);
            return json_encode($response);
        }
        $validator = Validator::make($req->all(), $rules);
        // dd($validator->fails());
        if ($validator->fails()) {
            $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
            return json_encode($response);
        } else {
            // $userInfo = $userObj->getUserById($req->user_id);
            $group = Invite_code::where('link_token', $req->link_token)->get()->first();

            if ($group->status == 0 && $group->expired == 0) {
                Invite_code::where('link_token', $req->link_token)->update(['status' => $status, 'expired' => 1]);
        
                $group_member_obj->Insert($group->group_id,$req->user_id,2);
                //change freelancer type depend on group type agencu or team of freelancers
                $response = Controller::returnResponse(200, 'user joined the team', array());
                return json_encode($response);
            } else {
                $response = Controller::returnResponse(500, 'Invitation expired', array());
                return json_encode($response);
            }
        }
        $response = Controller::returnResponse(200, 'user joined the team', array());
        return json_encode($response);
    }

    function joinGroupByCode(Request $req)
    {
        $status = 1; // must be 1 (approved) or 2 (denied)
        $rules = array(
            "code" => "required|exists:invite_codes,code",
            "user_id" => "required|exists:users,id"
        );
        $userObj = new UserController;
        $validator = Validator::make($req->all(), $rules);
        // dd($validator->fails());
        if ($validator->fails()) {
            $response = Controller::returnResponse(101, 'Validation Error', $validator->errors());
            return json_encode($response);
        } 
        try {
            $userInfo = $userObj->getUserById($req->user_id);
            $userType = $userInfo->type;
            $group_member_obj=new GroupMembersController();
            $group_obj=new GroupController();
          
            if ($userType == 1) {
                $userTypeObj = new FreeLancerController();
            } elseif ($userType == 2) {
                $userTypeObj = new ClientController();
            } else {
                $responseData = array(
                    "error" => 'User type not right'
                );
                $response = Controller::returnResponse(500, 'User type not right', $responseData);
                return json_encode($response);
            }
            // $userInfo = $userObj->getUserById($req->user_id);
            $group = Invite_code::where('code', $req->code)->get()->first();
            print_r($group->group_id);
            $group_info =  $group_obj->getGroupById($group->group_id);
           
            
            if ($group->status == 0 && $group->expired == 0) {
                Invite_code::where('code', $req->link_token)->update(['status' => $status, 'expired' => 1]);
                $group_member_obj->Insert($group->group_id,$req->user_id,2);
                $userTypeObj->updateType($req->user_id, $group_info->type);
                //change freelancer type depend on group type agencu or team of freelancers
                $response = Controller::returnResponse(200, 'user joined the team', array());
                return json_encode($response);
            } else {
                $response = Controller::returnResponse(422, 'Invitation expired', array());
                return json_encode($response);
            }
            $response = Controller::returnResponse(200, 'user joind the team', array());
            return json_encode($response);
        }catch (\Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error);
            return json_encode($response);
        }
        
    }

    private function generateCode()
    {
        $code = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10 / strlen($x)))), 1, 10);
        if (Invite_code::get()->contains('code', $code)) {
            $code = $this->generateCode();
        }
        return $code;
    }
    private function generateLinkToken()
    {
        $linkToken = Str::random(32);
        if (Invite_code::get()->contains('link_token ', $linkToken)) {
            $linkToken = $this->generateLinkToken();
        }
        return $linkToken;
    }

     //check if the user has been invited by email
     function check_invite($email)
     {
     
        $result=Invite_code::where('email', $email)->first();
        $expired=$result->expired;
        if ($result === null) {
            return (0);
        } else {
            return(1);
        }
      

     }
}
