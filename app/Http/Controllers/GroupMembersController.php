<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Models\Group_member;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use BaconQrCode\Renderer\Color\Gray;
use Illuminate\Support\Facades\Validator;
use Exception;

class GroupMembersController extends Controller
{
    //add row 
    function Insert($groupId, $userId, $type)
    {
        try {
            $data = [
                "group_id" => $groupId,
                "user_id" => $userId,
                "privileges" => $type
            ];
            $groupMember = Group_member::create($data);
            return 200;
        } catch (Exception $error) {
            return 500;
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
    function getGroupId($userId)
    {
        return Group_member::select('group_id')->where('user_id', $userId)->get()->first();
    }
    function getTeamMembersByGroupId($id)
    {
        try {
            $teamMembers = DB::table('group_members')
                ->leftjoin('freelancers', 'group_members.user_id', '=', 'freelancers.user_id')
                ->leftjoin('users', 'group_members.user_id', '=', 'users.id')->select("freelancers.user_id", "users.first_name", "users.last_name", "users.email", "freelancers.type_freelancer", "freelancers.image", "freelancers.country", "users.role", "group_members.privileges")
                ->where('group_members.group_id', '=', $id)
                ->get();
            $teamMembers = $this->getUserImages($teamMembers);
            return ($teamMembers);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error);
            return json_encode($response);
        }
    }
    function checkIfExists($id)
    {
        $member = Group_member::where('user_id', '=', $id)->first();
        if ($member === null) {
            return (0);
        } else {
            return ((int)$member->privileges);
        }
    }
    function getUserPrivileges($user_id)
    {
        $privileges = Group_member::select('privileges')->where('user_id', $user_id)->get()->first();
        return $privileges;
    }

    private function getUserImages($users)
    {
        foreach ($users as $keyUser => &$user) {
            if ($user->image != '') {
                $image = asset('images/users/' . $user->image);
                $user->image = $image;
            } else {
                $user->image = asset('images/profile-pic.jpg');
            }
        }
        return $users;
    }

    function removeUserFromGroup(Request $req)
    {

        $rules = array(
            "admin_id" => "required|exists:group_members,user_id",
            "user_id" => "required|exists:group_members,user_id"
        );
        $validator = Validator::make($req->all(), $rules);
        if ($validator->fails()) {
            $responseData = $validator->errors();
            $response = Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        }
        $adminId = $req->admin_id;
        $userId = $req->user_id;
        try {
            $adminInfo = Group_member::where("user_id", $adminId)->get()->first();
            if ($adminInfo->privileges != 1) {
                $response = Controller::returnResponse(422, 'user not admin ', []);
                return json_encode($response);
            }
            $userInfo = Group_member::where("user_id", $userId)
                ->where('group_id', $adminInfo->group_id)->get()->first();
            if (isset($userInfo) && $userInfo != '') {
                //remove
                Group_member::where('user_id', $userId)->where('group_id', $adminInfo->group_id)->delete();
                $user = User::find($userId);
                $user->tokens()->delete();
                $user->save();
                $response = Controller::returnResponse(200, 'user deleted successfully', []);
                return json_encode($response);
            } else {
                $response = Controller::returnResponse(422, 'user not in same group as admin ', []);
                return json_encode($response);
            }
        } catch (\Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error);
            return json_encode($response);
        }
    }
    function getMemberInfoByUserId($userId)
    {
        return Group_member::where('user_id', '=', $userId)->first();
    }

    function getCompanyMembersByGroupId($id)
    {
        try {
            $teamMembers = DB::table('group_members')
                ->leftjoin('clients', 'group_members.user_id', '=', 'clients.user_id')
                ->leftjoin('users', 'group_members.user_id', '=', 'users.id')->select("clients.user_id", "users.first_name", "users.last_name", "users.email", "clients.image", "clients.country", "users.role", "group_members.privileges")
                ->where('group_members.group_id', '=', $id)
                ->get();
            $teamMembers = $this->getUserImages($teamMembers);
            return ($teamMembers);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'There IS Error Occurred', $error);
            return json_encode($response);
        }
    }
    function getTeamAdminByGroupId($id)
    {
        $teamMembers = DB::table('group_members')
            ->leftJoin('freelancers', 'group_members.user_id', '=', 'freelancers.user_id')
            ->leftJoin('users', 'group_members.user_id', '=', 'users.id')
            ->select(
                "freelancers.user_id",
                "users.first_name",
                "users.last_name",
                "users.email",
                "freelancers.type_freelancer",
                "freelancers.country",
                "users.role",
                "group_members.privileges"
            )
            ->where('group_members.group_id', '=', $id)
            ->where('group_members.privileges', '=', 1)
            ->first();
        return ($teamMembers);
    }
    function getCompanyAdminByGroupId($id)
    {
        $teamMembers = DB::table('group_members')
            ->leftJoin('clients', 'group_members.user_id', '=', 'clients.user_id')
            ->leftJoin('users', 'group_members.user_id', '=', 'users.id')
            ->select(
                "clients.user_id",
                "users.first_name",
                "users.last_name",
                "users.email",
                "clients.country",
                "users.role",
                "group_members.privileges"
            )
            ->where('group_members.group_id', '=', $id)
            ->where('group_members.privileges', '=', 1)
            ->first();
        return ($teamMembers);
    }
    function getGroupAdminsIds($id)
    {
        return  DB::table('group_members')
            ->leftJoin('users', 'group_members.user_id', '=', 'users.id')
            ->select('group_members.user_id','users.fcm_token')
            ->where('group_members.group_id', '=', $id)
            ->where('group_members.privileges', '=', 1)
            ->get();
    }
    function getGroupType ($id)
    {
        return Group::where('id',$id)->select('type')->first()->type;
    }
    
}
