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
        $groupObj = new GroupController;
        $groupData = $groupObj->getGroupById($req->group_id);
        // dd(gettype($groupData->verified));
        if ($groupData->verified != 1) {
            $response = Controller::returnResponse(500, 'group not verified', $groupData);
            return json_encode($response);
        }
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
                $userTypeObj->updateTeamId($req->user_id, $group->group_id);

                $response = Controller::returnResponse(200, 'user joind the team', array());
                return json_encode($response);
            } else {
                $response = Controller::returnResponse(500, 'Invitation expired', array());
                return json_encode($response);
            }
        }
        $response = Controller::returnResponse(200, 'user joind the team', array());
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
            // dd($group, $userTypeObj, $userInfo);

            if ($group->status == 0 && $group->expired == 0) {
                Invite_code::where('code', $req->link_token)->update(['status' => $status, 'expired' => 1]);
                $userTypeObj->updateTeamId($req->user_id, $group->group_id);

                $response = Controller::returnResponse(200, 'user joind the team', array());
                return json_encode($response);
            } else {
                $response = Controller::returnResponse(500, 'Invitation expired', array());
                return json_encode($response);
            }
            $response = Controller::returnResponse(200, 'user joind the team', array());
            return json_encode($response);
        }catch (\Exception $error) {
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
}
