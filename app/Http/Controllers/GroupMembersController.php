<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group_member;
use Illuminate\Support\Facades\DB;
use Exception;

class GroupMembersController extends Controller
{
    //add row 
    function Insert($groupId, $userId, $type)
    {
        $data = [
            "group_id" => $groupId,
            "user_id" => $userId,
            "privileges" => $type
        ];
        Group_member::create($data);
      
    }
    //update row according to row id
    function Update($id)
    {

    }
    //delete row according to row id
    function Delete($id)
    {

    }
    function getGroupId($userId){
        return Group_member::select('group_id')->where('user_id', $userId)->get()->first();
    }
    function getGroupInfoByUserId($userId){
        
    }
    function getTeamMembersByGroupId($id)
    {
        try{
        $teamMembers=DB::table('group_members')
        ->leftjoin('freelancers','group_members.user_id','=','freelancers.user_id')
        ->leftjoin('users','group_members.user_id','=','users.id')->select("freelancers.user_id","users.first_name","users.last_name","users.email","freelancers.type","freelancers.image","freelancers.country","users.role","group_members.privileges")
        ->where('group_members.group_id','=',$id)
        ->get();
        return($teamMembers);
        }
        catch(Exception $error)
        {
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
            return ($member->privileges);
        }
    }
}   