<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group_member;


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
}
