<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Messages;
use Illuminate\Http\Request;
use App\Models\Rooms;
use App\Models\RoomMembers;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Validator;
use Money\Exchange;
use App\Models\Group_member;
use Illuminate\Support\Facades\DB;
use  App\Http\Controllers\GroupMembersController;
use  App\Http\Controllers\TeamController;
use  App\Http\Controllers\CompanyController;



class RoomController extends Controller
{
    //add row 
    function createRoom($data)
    {
        try {


            $data['name'] = $this->roomName($data['team_id'], $data['company_id']);
            $room = Rooms::create(['name' => $data['name']]);
            $room_id = $room->id;
            $groupMembersObj = new GroupMembersController;

            // $teamAdmins = $groupMembersObj->getGroupAdminsIds($data['team_id'])->pluck('user_id')->toArray();
            $teamAdmins = $groupMembersObj->getGroupAdminsIds($data['team_id']);
            ['code' => 500, 'msg' =>  $teamAdmins];
            $companyAdmins = $groupMembersObj->getGroupAdminsIds($data['company_id'])->pluck('user_id')->toArray();

            $users = array_merge($teamAdmins, $companyAdmins);

            foreach ($users as $user_id) {

                RoomMembers::create(['room_id' => $room_id, 'user_id' => $user_id->user_id]);
            }

            return ['code' => 200, 'msg' => 'successful'];
        } catch (Exception $error) {
            return ['code' => 500, 'msg' => $error->getMessage()];
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
                RoomMembers::create($req->all());
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
                $chatObj = new ChatController;

                $lastMessage = $chatObj->getRoomLastMessage($room_id);
                $room = array(
                    'room_id' => $room_id,
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

    function searchForUsers(Request $req, $name)
    {
        try {
            $userData = Controller::checkUser($req);
            $group_id = $userData['group_id'];
            $groupMembersIds = Group_member::where('group_id', $group_id)->select('*')->pluck('user_id')->toArray();
            $teamMembers = DB::table('users')
                ->select(
                    "users.id",
                    "users.first_name",
                    "users.last_name",
                    "users.email",
                )
                ->whereIn('id', $groupMembersIds)
                ->where('name', 'LIKE', '%' . $name . '%')
                ->get();
            $response = Controller::returnResponse(200, "successful", $teamMembers);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something wen wrong", $error->getMessage());
            return json_encode($response);
        }
    }
    function roomName($team_id, $company_id)
    {
        $TeamObj = new TeamController;
        $CompanyObj = new CompanyController;

        $team_name = $TeamObj->get_team_info($team_id)->name;
        $company_name = $CompanyObj->get_company_info($company_id)->name;
        return $team_name . ' & ' . $company_name;
    }
    function updateRoomName(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            $isMember =  $this->IsRoomMember($userData['user_id'], $req->room_id);
            if ($isMember == 1) {
                Rooms::where('id', $req->room_id)->update(['name' => $req->name]);
            } else {
                $response = Controller::returnResponse(422, "unauthorized action ", []);
                return json_encode($response);
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong ", $error->getMessage());
            return json_encode($response);
        }
    }
    function IsRoomMember($user_id, $room_id)
    {
        $member = DB::table('room_members')
            ->where('user_id', '=', $user_id)
            ->where('room_id', '=', $room_id)
            ->select('*')
            ->first();

        if ($member === null) {
            return 0;
        } else {
            return 1;
        }
    }
}
