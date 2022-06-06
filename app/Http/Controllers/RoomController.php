<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Messages;
use Illuminate\Http\Request;
use App\Models\Rooms;
use App\Models\RoomMembers;
use Exception;
use Illuminate\Support\Facades\Validator;
use Money\Exchange;
use App\Models\Group_member;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    //add row 
    function createRoom($data)
    {
        try {
            Rooms::create($data);
            return ['code' => 200, 'msg' => 'successful'];
        } catch (Exception $error) {
            return ['code' => 500, 'msg' => 'can not create room'];
        }
    }

    function addMember(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            if ($userData['privileges'] != 1) {
                $response = Controller::returnResponse(422, "unauthorized this action for admins", []);
                return json_encode($response);
            }
            $rules = array(
                "user_id" => "requires|exists:users,id",
                'room_id' => "requires|exists:rooms,id",
            );
            $validator = Validator::make($req->all(), $rules);
            if ($validator->fails()) {
                $responseData = $validator->errors();
                $response = Controller::returnResponse(101, "Validation Error", $responseData);
                return json_encode($response);
            } else {
                RoomMembers::create($req);
                $response = Controller::returnResponse(200, "successful", []);
                return json_encode($response);
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something wen wrong", $error->getMessage());
            return json_encode($response);
        }
    }
    //room type 1 individual 2 group
    function getRooms(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            $user_id = $userData['user_id'];
            if ($userData['user_id'] != $user_id) {
                $response = Controller::returnResponse(422, "unauthorized action ", []);
                return json_encode($response);
            }
            $rooms_ids = RoomMembers::where('user_id', $user_id)->select('room_id')->pluck('room_id')->toArray();
            $rooms = array();

            foreach ($rooms_ids as $room_id) {
                $roomType = 1;
                $room = array();
                $name = Rooms::where('id', $room_id)->select('name')->first()->name;
                $membersCount = RoomMembers::where('room_id', $room_id)->count();
                if ($membersCount > 2) {
                    $roomType = 2;
                }
                $lastMessage = Messages::where('room_id', $room_id)->select('body')->distinct()->latest();
                $room = array(
                    'name' => $name,
                    'roomType' => $roomType,
                    'lastMessage' => $lastMessage
                );
                array_push($rooms, $room);
            }
            $response = Controller::returnResponse(200, "successful", $rooms);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something wen wrong", $error->getMessage());
            return json_encode($response);
        }
    }

    function searchForUsers(Request $req)
    {
        $userData = Controller::checkUser($req);
        if ($userData['user_id'] == $req->user_id) {
            $group_id = $userData['group_id'];
            $groupMembersIds = Group_member::where('group_id', $group_id)->select('user_id')->pluck('user_id')->toArray();
            if ($userData['type'] == 1) {
            } else {
            }
        } else {
            $response = Controller::returnResponse(422, "unauthorized action ", []);
            return json_encode($response);
        }
    }
    function allmembers()
    {
        // try {
            $roomMembers =DB::table('users')->all();

          return $roomMembers;
    }
}
