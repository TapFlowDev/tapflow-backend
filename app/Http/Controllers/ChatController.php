<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Messages;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Http\Controllers\FireBaseNotificationsController;
use App\Models\User;
use App\Models\RoomMembers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\RoomController;

class ChatController extends Controller
{
    //add row 
    function sendMessage(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            
            $rules = array(
                "body" => "required",
                "room_id" => "required|exists:rooms,id",
            );
            $validator = Validator::make($req->all(), $rules);
            if ($validator->fails()) {
                $responseData = $validator->errors();
                $response = Controller::returnResponse(101, "Validation Error", $responseData);
                return json_encode($response);
            } else {
                $groupObj = new GroupController;
                $roomObj = new RoomController;
                $group_type=$groupObj->getGroupType($userData['group_id']);
                if($group_type==1)
                {
                    $link="/Client-user/main/chat#/".$req->room_id;
                }
                elseif($group_type ==2 )
                {
                    $link="/a-user/main/chat#/".$req->room_id;
                }
                $userName = User::where('id', $userData['user_id'])->select('first_name', 'last_name')->first();
                $msgTitle = $userName->first_name . ' ' . $userName->last_name;
                $fcmTokens=$roomObj->getRoomMembersTokens($userData['user_id'],$req->room_id);

                Messages::create(['body' => $req->body, 'user_id' => $userData['user_id'], 'room_id' => $req->room_id]);
                Controller::sendNotification($fcmTokens,$msgTitle,$req->body,$link,1,'chat','');
                

                $response = Controller::returnResponse(200, "send message successful", []);
                return json_encode($response);
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return json_encode($response);
        }
    }

    function getRoomMessages(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            $roomObj = new RoomController;
            $isMember = $roomObj->IsRoomMember($userData['user_id'], $req->room_id);
            if ($isMember == 1) {
                $allMessages = Messages::where('room_id', $req->room_id)->select('*')->get();
                $messages = $this->getSenderInfo($allMessages);
                $response = Controller::returnResponse(200, "successful", $messages);
                return json_encode($response);
            } else {
                $response = Controller::returnResponse(422, "failed request you are not a member of this room", []);
                return json_encode($response);
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return json_encode($response);
        }
    }
    function getRoomLastMessage($room_id)
    {
        $lastMessage = Messages::where('room_id', $room_id)->select('body','created_at')->distinct()->latest()
            ->offset(1)->limit(1)->first();
            if ($lastMessage === null)
            {
                return 'send your first message';
               
            }
            else
            {
                return $lastMessage->body;
            }
       
    }
    function getSenderInfo($allMessages)
    {
        foreach($allMessages as $message)
        {
           
            $userInfo = User::where('id', $message->user_id)->select('first_name', 'last_name', 'type')->first();
            $userName = $userInfo->first_name . ' ' . $userInfo->last_name;
    
            if ($userInfo->type == 1) {
                $userImage = DB::table('freelancers')
                    ->where('user_id', '=', $message->user_id)
                    ->select('image')
                    ->first();
    
                if ($userImage->image != '' || $userImage->image != null) {
                    $image = asset('images/users/' . $userImage->image);
                } else {
                    $image = asset('images/profile-pic.jpg');
                }
            } elseif ($userInfo->type == 2) {
                $userImage = DB::table('clients')
                    ->where('user_id', '=', $message->user_id)
                    ->select('image')
                    ->first();
                if ($userImage->image != '' || $userImage->image != null) {
                    $image = asset('images/users/' . $userImage->image);
                } else {
                    $image = asset('images/profile-pic.jpg');
                }
            }
            $message->senderName=$userName;
            $message->senderImage=$image;
        }
       
    
        return $allMessages;
    }
    
}
