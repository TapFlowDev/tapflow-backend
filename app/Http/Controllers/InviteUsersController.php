<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invite_code;
use Illuminate\Support\Str;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\FreeLancerController;
use App\Models\Freelancer;
use Illuminate\Support\Arr;
use Exception;
use Illuminate\Support\Facades\Validator;


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
        }
        $response = Controller::returnResponse(200, 'users invited successfully', $invite_code);
        return json_encode($response);
        // return $invite_code;
        $url = env('APP_URL') . "api/" . $this->generateLinkToken();
        // dd($arr, $invite_code, $dd);
        /*
        send email 
        */
    }
    function getDataByToken($token)
    {
        $groupObj = new GroupController;
        try {
            $group = Invite_code::where('link_token', $token)->get()->first();
            if ($group->status == 0 && $group->expired == 0) {
                $groupData = $groupObj->getGroupById($group->group_id);
                return $groupData;
            } else {
                return 'Invitation expired';
            }
        } catch (\Exception $error) {
            return $error;
        }
    }
    function updateInvitation(Request $req)
    {
        // dd($req);
        $userObj = new FreeLancerController;
        $status = $req->accept; // must be 1 (approved) or 2 (denied)
        $rules = array(
            "accept" => "required|gt:0|lt:3",
            "link_token" => "required|exists:invite_codes",
            "user_id" => "required|exists:users,id"
        );
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
                $userObj->updateTeamId($req->user_id, $group->group_id);

                $response = Controller::returnResponse(200, 'user joind the team', array());
                return json_encode($response);
            } else {
                $response = Controller::returnResponse(500, 'Invitation expired', array());
                return json_encode($response);
            }
        }

        return $req;
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
