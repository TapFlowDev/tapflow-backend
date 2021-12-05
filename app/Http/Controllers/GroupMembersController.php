<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group_member;
use Illuminate\Support\Facades\DB;


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
       
        $teamMembers=DB::table('group_members')
        ->join('freelancers','group_members.user_id','=','freelancers.user_id')
        ->where('group_members.group_id','=',$id)
        ->get();
        return($teamMembers);
    }
}