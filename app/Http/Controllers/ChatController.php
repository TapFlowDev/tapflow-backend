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

class ChatController extends Controller
{
    //add row 
    function sendMessage(Request $req)
    {
        // try {
            $rules = array(
                "body" => "required",
                "user_id" => "required|exists:user,id",
                "room_id" => "required|exists:rooms,id",
            );
            $validator = Validator::make($req->all(), $rules);
            if ($validator->fails()) {
                $responseData = $validator->errors();
                $response = Controller::returnResponse(101, "Validation Error", $responseData);
                return json_encode($response);
            } else {
                $firebaseObj = new FireBaseNotificationsController;
                $roomMembers = RoomMembers::where('room_id', $req->room_id)->select('user_id')->pluck('user_id')->all()->toArray();
                $fcmTokens = DB::table('users')
                    ->whereIn('id', $roomMembers)
                    ->select('fcm_token')
                    ->pluck('fcm_token')
                    ->toArray();
                $userName = User::where('id', $req->user_id)->select('first_name', 'last_name')->first();
                $msgTitle = $userName->first_name . ' ' . $userName->last_name;
                $data = array('FcmToken' => $fcmTokens, 'title' => $msgTitle, 'body' => $req->body);
                $firebaseMsg = $firebaseObj->sendFireBaseNotification($data);
                if ($firebaseMsg['code'] != 200) {
                    $response = Controller::returnResponse(500, "send message failed", $firebaseMsg['msg']);
                    return json_encode($response);
                }
                Messages::create($req->all());
                $response = Controller::returnResponse(200, "send message successful", []);
                return json_encode($response);
            }
        // } catch (Exception $error) {
        //     $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
        //     return json_encode($response);
        // }
    }
    function messageSeen($id)
    {
        Messages::where('id', $id)->update(['seen' => 1]);
    }

    function getRoomMessages(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            if (RoomMembers::where('user_id', '=', $userData['user_id'])->exists()) {
                $response = Controller::returnResponse(422, "failed request you are not a member of this room", []);
                return json_encode($response);
            } else {
                $allMessages = Messages::where('room_id', $req->room_id)->select('*')->get();
                $response = Controller::returnResponse(200, "successful", $allMessages);
                return json_encode($response);
            }
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return json_encode($response);
        }
    }
}
