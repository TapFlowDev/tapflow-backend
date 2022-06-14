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


            $teamAdmins = $groupMembersObj->getGroupAdminsIds($data['team_id'])->pluck('user_id')->toArray();

            $companyAdmins = $groupMembersObj->getGroupAdminsIds($data['company_id'])->pluck('user_id')->toArray();

            $users = array_merge($teamAdmins, $companyAdmins);

            foreach ($users as $user_id) {

                RoomMembers::create(['room_id' => $room_id, 'user_id' => $user_id]);
            }
            // $fenLink = "/a-user/main/chat#/" . $room_id;
            Controller::sendNotification($data['team_id'], '', 'A new group chat is created', "/a-user/main/chat#/" . $room_id, 2,'rooms',$room_id);
            Controller::sendNotification($data['company_id'], '', 'A new group chat is created', "/Client-user/main/chat#/" . $room_id, 2,'rooms',$room_id);
            return ['code' => 200, 'msg' => 'successful'];
        } catch (Exception $error) {
            return ['code' => 500, 'msg' => $error->getMessage()];
        }
    }

    function addMember(Request $req)
    {
        try {
            $userData = $this->checkUser($req);
            if ($userData['privileges'] != 1) {
                $response = Controller::returnResponse(422, "unauthorized this action for admins", []);
                return json_encode($response);
            }
            $rules = array(
                'user_id' => "required|exists:users,id",
                'room_id' => "required|exists:rooms,id",
            );

            $validator = Validator::make($req->all(), $rules);
            if ($validator->fails()) {
                $responseData = $validator->errors();
                $response = Controller::returnResponse(101, "Validation Error", $responseData);
                return json_encode($response);
            } else {
                $isMember = $this->IsRoomMember($req->user_id, $req->room_id);
                if ($isMember == 1) {
                    $response = Controller::returnResponse(422, "this user already room member", []);
                    return json_encode($response);
                }
                RoomMembers::create($req->all());
                $response = Controller::returnResponse(200, "successful", []);
                return json_encode($response);
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
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
            $rooms_ids = RoomMembers::where('user_id', $user_id)->select('room_id')->distinct()->pluck('room_id')->toArray();
            $rooms = array();

            foreach ($rooms_ids as $room_id) {
                $roomType = 1;
                $room = array();
                $name = Rooms::where('id', $room_id)->select('name')->first()->name;
                $members = RoomMembers::where('room_id', $room_id)->select('room_id','seen')->get();
               
                $membersCount=$members->count();
                if ($membersCount > 2) {
                    $roomType = 2;
                }
                $chatObj = new ChatController;

                $lastMessage = $chatObj->getRoomLastMessage($room_id);
                $seen=$this->roomSeen($room_id,$user_id);
                $room = array(
                    'room_id' => $room_id,
                    'name' => $name,
                    'roomType' => $roomType,
                    'lastMessage' => $lastMessage,
                    'seen'=>$seen,
                );
                array_push($rooms, $room);
            }
            $response = Controller::returnResponse(200, "successful", $rooms);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return json_encode($response);
        }
    }

    function searchForUsers(Request $req, $name)
    {
        try {
            $userData = Controller::checkUser($req);
            $group_id = $userData['group_id'];
            $groupMembersObj = new GroupMembersController;
            $groupType = $groupMembersObj->getGroupType($group_id);
            // $groupMembersIds = Group_member::where('group_id', $group_id)->where('user_id','!=' ,$userData['user_id'])->select('*')->pluck('user_id')->toArray();
            if ($groupType == 1) {
                $teamMembers = DB::table('group_members')
                    ->leftJoin('freelancers', 'group_members.user_id', '=', 'freelancers.user_id')
                    ->leftJoin('users', 'group_members.user_id', '=', 'users.id')
                    ->select(
                        "users.id",
                        "users.first_name",
                        "users.last_name",
                        "users.email",
                        "freelancers.image",
                        "users.role"
                    )
                    ->where('group_members.group_id','=',$userData['group_id'])
                    ->where('group_members.user_id','<>',$userData['user_id'])
                    ->where('name', 'LIKE', '%' . $name . '%')
                    ->get();
                    foreach ($teamMembers as $keyUser => &$user) {
                        if ($user->image != '') {
                            $image = asset('images/users/' . $user->image);
                            $user->image = $image;
                        } else {
                            $user->image = asset('images/profile-pic.jpg');
                        }
                    }
            } elseif ($groupType == 2) {
                $teamMembers = DB::table('group_members')
                    ->leftjoin('clients', 'group_members.user_id', '=', 'clients.user_id')
                    ->leftjoin('users', 'group_members.user_id', '=', 'users.id')
                    ->select(
                        "users.id",
                        "users.first_name",
                        "users.last_name",
                        "users.email",
                        "clients.image",
                        "users.role"
                    ) 
                    ->where('group_members.group_id','=',$userData['group_id'])
                    ->where('group_members.user_id','<>',$userData['user_id'])
                    ->where('name', 'LIKE', '%' . $name . '%')
                    ->get();
                    foreach ($teamMembers as $keyUser => &$user) {
                        if ($user->image != '') {
                            $image = asset('images/users/' . $user->image);
                            $user->image = $image;
                        } else {
                            $user->image = asset('images/profile-pic.jpg');
                        }
                    }
            }
            // $teamMembers = DB::table('users')
            //     ->select(
            //         "users.id",
            //         "users.first_name",
            //         "users.last_name",
            //         "users.email",
            //     )
            //     ->whereIn('id', $groupMembersIds)
            //     ->where('name', 'LIKE', '%' . $name . '%')
            //     ->get();
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
    function getRoomMembersTokens($user_id, $room_id)
    {
        $roomMembers = DB::table('room_members')
            ->where('room_id', $room_id)
            ->where('user_id', '!=', $user_id)
            ->select('*')
            ->pluck('user_id')
            ->toArray();

        $fcmTokens = DB::table('users')
            ->whereIn('id', $roomMembers)
            ->select('*')
            ->where('fcm_token', '!=', null)
            ->pluck('fcm_token')
            ->toArray();
        return $fcmTokens;
    }
    private function roomSeen($room_id,$user_id){
        return RoomMembers::where('room_id', $room_id)->where('user_id',$user_id)->select('seen')->first()->seen;

    }
    function seenRoomMessages(Request $req)
    {
        try{
        $userData=Controller::checkUser($req);
        $roomMember=$this->IsRoomMember($userData['user_id'],$req->room_id);
        if($roomMember == 0)
        {
            $response = Controller::returnResponse(422, "unauthorized action ", []);
            return json_encode($response);
        }
        RoomMembers::where('user_id',$userData['user_id'])->where('room_id',$req->room_id)->update(['seen'=>1]);
        $response = Controller::returnResponse(200, "successful ", []);
        return json_encode($response);
    }catch(Exception $error)
    {
        $response = Controller::returnResponse(500, "something went wrong", []);
        return json_encode($response);
    }
    }

}