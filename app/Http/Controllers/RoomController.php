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
use App\Models\Company;
use App\Models\Team;



class RoomController extends Controller
{
    //add row 
    function createRoom($data)
    {
        try {


            $exist = $this->checkIfRoomExist($data['agencyAdmin'], $data['companyAdmin']);
            if ($exist == 1) {
                return ['code' => 200, 'msg' => 'alredy exist'];
            }
            $data['name'] = $this->roomName($data['agencyAdmin'], $data['companyAdmin']);
            $room = Rooms::create(['name' => $data['name']]);
            $room_id = $room->id;
            $groupMembersObj = new GroupMembersController;

            RoomMembers::create(['room_id' => $room_id, 'user_id' => $data['agencyAdmin']]);
            RoomMembers::create(['room_id' => $room_id, 'user_id' => $data['companyAdmin']]);

            // $teamAdmins = $groupMembersObj->getGroupAdminsIds($data['team_id'])->pluck('user_id')->toArray();

            // $companyAdmins = $groupMembersObj->getGroupAdminsIds($data['company_id'])->pluck('user_id')->toArray();

            // $users = array_merge($teamAdmins, $companyAdmins);

            // foreach ($users as $user_id) {

            //     RoomMembers::create(['room_id' => $room_id, 'user_id' => $user_id]);
            // }
            // $fenLink = "/a-user/main/chat#/" . $room_id;
            $agencyToken = DB::table('users')
                ->where('id', '=', $data['agencyAdmin'])
                ->select('*')
                ->where('fcm_token', '!=', null)
                ->pluck('fcm_token')
                ->toArray();
            $companyToken = DB::table('users')
                ->where('id', '=', $data['companyAdmin'])
                ->select('*')
                ->where('fcm_token', '!=', null)
                ->pluck('fcm_token')
                ->toArray();
            Controller::sendNotification($agencyToken, '', 'A new group chat is created', "/a-user/main/chat#/" . $room_id, 2, 'rooms', $room_id);
            Controller::sendNotification($companyToken, '', 'A new group chat is created', "/Client-user/main/chat#/" . $room_id, 2, 'rooms', $room_id);
            return ['code' => 200, 'msg' => $room_id];
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
    // function getRooms(Request $req)
    // {
    //     try {
    //         $userData = Controller::checkUser($req);
    //         $user_id = $userData['user_id'];
    //         if ($userData['user_id'] != $user_id) {
    //             $response = Controller::returnResponse(422, "unauthorized action ", []);
    //             return json_encode($response);
    //         }
    //         $rooms_ids = RoomMembers::where('user_id', $user_id)->select('room_id')->distinct()->pluck('room_id')->toArray();
    //         $rooms = array();

    //         foreach ($rooms_ids as $room_id) {
    //             $roomType = 1;
    //             $room = array();
    //             $name = Rooms::where('id', $room_id)->select('name')->first()->name;
    //             $membersCount = RoomMembers::where('room_id', $room_id)->select('room_id')->count();
    //             // $membersCount=$members->count();
    //             if ($membersCount > 2) {
    //                 $roomType = 2;
    //             }
    //             $chatObj = new ChatController;

    //             $lastMessage = $chatObj->getRoomLastMessage($room_id);
    //             if ($lastMessage === null) {
    //                 $msg = 'send first messages';
    //             } else {
    //                 $msg = $lastMessage->body;
    //             }
    //             $room = array(
    //                 'room_id' => $room_id,
    //                 'name' => $name,
    //                 'roomType' => $roomType,
    //                 'lastMessage' => $msg,
    //                 // 'seen'=>$members->seen,
    //             );
    //             array_push($rooms, $room);
    //         }
    //         $response = Controller::returnResponse(200, "successful", $rooms);
    //         return json_encode($response);
    //     } catch (Exception $error) {
    //         $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
    //         return json_encode($response);
    //     }
    // }

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
                    ->where('group_members.group_id', '=', $userData['group_id'])
                    ->where('group_members.user_id', '<>', $userData['user_id'])
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
                    ->where('group_members.group_id', '=', $userData['group_id'])
                    ->where('group_members.user_id', '<>', $userData['user_id'])
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
    function roomName($agencyAdmin, $CompanyAdmin)
    {
        $TeamObj = new TeamController;
        $CompanyObj = new CompanyController;
        $groupObj = new GroupController;
        $team_id = $groupObj->getGroupIdByUserId($agencyAdmin);
        $company_id = $groupObj->getGroupIdByUserId($CompanyAdmin);
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
    function seenRoomMessages(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            $roomMember = $this->IsRoomMember($userData['user_id'], $req->room_id);
            if ($roomMember == 0) {
                $response = Controller::returnResponse(422, "unauthorized action ", []);
                return json_encode($response);
            }
            RoomMembers::where('user_id', $userData['user_id'])->where('room_id', $req->room_id)->update(['seen' => 1]);
            $response = Controller::returnResponse(200, "successful ", []);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", []);
            return json_encode($response);
        }
    }
    function getChatNot(Request $req, $offset, $limit)
    {
        try {
            $userData = Controller::checkUser($req);
            // return $userData;
            // $user_id = $userData['user_id'];
            // if ($userData['user_id'] != $user_id) {
            //     $response = Controller::returnResponse(422, "unauthorized action ", []);
            //     return json_encode($response);
            // }
            $page = ($offset - 1) * $limit;
            $ids = RoomMembers::where('user_id',  $userData['user_id'])
                ->where('seen', 0)
                ->select('room_id', 'updated_at')
                ->distinct()->orderBy('updated_at')
                ->offset($page)->limit($limit)
                ->get();
            $count = RoomMembers::where('user_id',  $userData['user_id'])
                ->where('seen', 0)
                ->select('room_id', 'updated_at')
                ->distinct()->orderBy('updated_at')
                ->count();
            $rooms = array();
            $rooms2 = array();
            foreach ($ids as $id) {
                $roomType = 1;
                $room = array();
                $room2 = array();
                $name = Rooms::where('id', $id->room_id)->select('name')->first()->name;
                $membersCount = RoomMembers::where('room_id',  $id->room_id)->select('seen')->count();
                $seen = RoomMembers::where('room_id',  $id->room_id)->where('user_id', $userData['user_id'])->select('seen')->first()->seen;
                if ($membersCount > 2) {
                    $roomType = 2;
                }
                $chatObj = new ChatController;


                $lastMessage = $chatObj->getRoomLastMessage($id->room_id, $userData['user_id']);



                $image = $this->getRoomImage($id->room_id, $userData['user_id'],$userData['type']);
                if ($lastMessage === null) {
                    $room2 = array(
                        'room_id' =>  $id->room_id,
                        'name' => $name,
                        'roomType' => $roomType,
                        'lastMessage' => 'first msg',
                        'date' => '',
                        'seen' => $seen,
                        'image' => $image,
                    );
                    array_push($rooms2, $room2);
                } else {
                    $room = array(
                        'room_id' =>  $id->room_id,
                        'name' => $name,
                        'roomType' => $roomType,
                        'lastMessage' => $lastMessage->body,
                        'date' => $lastMessage->created_at,
                        'seen' => $seen,
                        'image' => $image,
                    );
                    array_push($rooms, $room);
                    $dates = array_column($rooms, 'date');
                    array_multisort($dates, SORT_DESC, $rooms);
                }
            }
            $all = array_merge($rooms, $rooms2);
            // $all['count']=$count;
            $response = Controller::returnResponse(200, "successful", ['chatNotifications' => $all, 'count' => $count]);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return json_encode($response);
        }
    }
    //room type 1 individual 2 group
    function roomsInfo($rooms, $offset, $limit)
    {
        $page = ($offset - 1) * $limit;
        $Rooms = array();
        foreach ($rooms as $room) {
            $roomType = 1;
            $membersCount = RoomMembers::where('room_id', $room)->count();
            if ($membersCount > 2) {
                $roomType = 2;
            }
            $room1 = DB::table('messages')
                ->Join('rooms', 'messages.room_id', '=', 'rooms.id')
                ->Join('room_members', 'messages.room_id', '=', 'room_members.room_id')
                ->select('messages.room_id', 'rooms.name', 'messages.body', 'messages.created_at', 'room_members.seen')
                ->where('messages.room_id', '=', $room)
                // ->latest()
                ->orderBy('messages.created_at', 'desc')

                ->offset($page)->limit($limit)
                ->first();
            //    $room1->type=$roomType;
            array_push($Rooms, $room1);
        }
        return $Rooms;
    }
    //room type 1 individual 2 group
    function getRooms(Request $req, $offset, $limit)
    {
        try {
            $userData = Controller::checkUser($req);
            return $userData;
            $user_id = $userData['user_id'];
            if ($userData['user_id'] != $user_id) {
                $response = Controller::returnResponse(422, "unauthorized action ", []);
                return json_encode($response);
            }
            $page = ($offset - 1) * $limit;
            $ids = RoomMembers::where('user_id', $user_id)->select('room_id', 'updated_at')->distinct()->orderBy('updated_at')->offset($page)->limit($limit)->get();
            $rooms = array();
            $rooms2 = array();


            foreach ($ids as $id) {
                $roomType = 1;
                $room = array();
                $room2 = array();
                $name = Rooms::where('id', $id->room_id)->select('name')->first()->name;
                $membersCount = RoomMembers::where('room_id',  $id->room_id)->select('seen')->count();
                $seen = RoomMembers::where('room_id',  $id->room_id)->where('user_id', $user_id)->select('seen')->first()->seen;
                if ($membersCount > 2) {
                    $roomType = 2;
                }
                $chatObj = new ChatController;


                $lastMessage = $chatObj->getRoomLastMessage($id->room_id, $userData['user_id']);



                $image = $this->getRoomImage($id->room_id, $userData['user_id'],$userData['type']);
                if ($lastMessage === null) {
                    $room2 = array(
                        'room_id' =>  $id->room_id,
                        'name' => $name,
                        'roomType' => $roomType,
                        'lastMessage' => 'first msg',
                        'date' => '',
                        'seen' => $seen,
                        'image' => $image,
                    );
                    array_push($rooms2, $room2);
                } else {
                    $room = array(
                        'room_id' =>  $id->room_id,
                        'name' => $name,
                        'roomType' => $roomType,
                        'lastMessage' => $lastMessage->body,
                        'date' => $lastMessage->created_at,
                        'seen' => $seen,
                        'image' => $image,
                    );
                    array_push($rooms, $room);
                    $dates = array_column($rooms, 'date');
                    array_multisort($dates, SORT_DESC, $rooms);
                }
            }
            $all = array_merge($rooms, $rooms2);
            $response = Controller::returnResponse(200, "successful", $all);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return json_encode($response);
        }
    }
    function checkIfRoomExist($agencyAdmin, $companyAdmin)
    {
        $agencyAdminRooms = RoomMembers::where('user_id', $agencyAdmin)->select('room_id')->pluck('room_id')->toArray();
        $clientAdminRooms = RoomMembers::where('user_id', $companyAdmin)->select('room_id')->pluck('room_id')->toArray();
        $d = array();
        foreach ($agencyAdminRooms as $room_id) {

            if (in_array($room_id, $clientAdminRooms)) {
                return 1;
            } else {
                continue;
            }
        }
    }
    function updateSeen($user_id, $room_id)
    {
        $roomMembers = DB::table('room_members')
            ->where('room_id', $room_id)
            ->where('user_id', '!=', $user_id)
            ->update(['seen' => 0]);
    }
    function getRoomImage($room_id, $user_id, $type)
    {
        $groupObj = new GroupController;
        $groupAdminId = RoomMembers::where('room_id',  $room_id)->where('user_id', '<>', $user_id)->select('user_id')->first()->user_id;
        $group_id = $groupObj->getGroupIdByUserId($groupAdminId);
        if ($type == 2) {
            $image = Team::where('group_id', $group_id)->select('image')->first()->image;
            if (!$image) {
                $image = asset('images/profile-pic.jpg');
            } else {
                $image =  asset('images/companies/' . $image);
            }
        } else {
            $image = Company::where('group_id', $group_id)->select('image')->first()->image;
            if (!$image) {
                $image = asset('images/profile-pic.jpg');
            } else {
                $image =  asset('images/companies/' . $image);
            }
        }
        return $image;
    }
    function getRoom(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            $isMember = $this->IsRoomMember($userData['user_id'], $req->room_id);
            if (!($userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['verified'] == 1 && $isMember == 1)) {
                $response = Controller::returnResponse(401, "unauthorized", []);
                return (json_encode($response));
            } else {
                $chatObj = new ChatController;

                $name = Rooms::where('id', $req->room_id)->select('name')->first()->name;
                $lastMessage = $chatObj->getRoomLastMessage($req->room_id, $userData['user_id']);
                $image = $this->getRoomImage($req->room_id, $userData['user_id'],$userData['type']);
                $roomType = 1;
                $membersCount = RoomMembers::where('room_id',  $req->room_id)->select('seen')->count();
                if ($membersCount > 2) {
                    $roomType = 2;
                }
                $seen = RoomMembers::where('room_id',  $req->room_id)->where('user_id', $userData['user_id'])->select('seen')->first()->seen;
                if ($lastMessage === null) {
                    $room2 = array(
                        'room_id' =>  $req->room_id,
                        'name' => $name,
                        'roomType' => $roomType,
                        'lastMessage' => 'first msg',
                        'date' => '',
                        'seen' => $seen,
                        'image' => $image,
                    );
                    array_push($rooms2, $room2);
                } else {
                    $room = array(
                        'room_id' =>  $req->room_id,
                        'name' => $name,
                        'roomType' => $roomType,
                        'lastMessage' => $lastMessage->body,
                        'date' => $lastMessage->created_at,
                        'seen' => $seen,
                        'image' => $image,
                    );
                    array_push($rooms, $room);
                    $dates = array_column($rooms, 'date');
                    array_multisort($dates, SORT_DESC, $rooms);
                }
                $response = Controller::returnResponse(200, "successful", $room);
                return json_encode($response);
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return json_encode($response);
        }
    }
    function getRoomIdByAgencyAndCompanyAdmins(Request $req){
        $userData = Controller::checkUser($req);
        if (!($userData['exist'] == 1 && $userData['privileges'] == 1 )) {
            $response = Controller::returnResponse(401, "unauthorized", []);
            return (json_encode($response));
        }
        else{
            $admins=[$userData['user_id'],$req->admin_id];
            $room_id=RoomMembers::select('room_id')->whereIn('user_id', $admins)->first()->room_id;
            if($room_id === null )
            {
                $response = Controller::returnResponse(422, "room does not exist", []);
                return json_encode($response);
            }
            $response = Controller::returnResponse(200, "successful", ['room_id'=>$room_id]);
            return json_encode($response);
        }
    }
}
