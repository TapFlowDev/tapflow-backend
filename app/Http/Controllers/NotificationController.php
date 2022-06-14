<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\System_Notification;
use Exception;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    function getUserNotification(Request $req, $offset, $limit)
    {
        try {
            $userData = Controller::checkUser($req);
            // return($userData);
            $page = ($offset - 1) * $limit;
            $userNotifications = DB::table('system_notifications')
                ->select('*')
                ->where('receiver_id', '=', $userData['user_id'])
                ->where('seen', '<>', 1)
                ->distinct()
                ->latest()->offset($page)->limit($limit)
                ->get();
            $response = Controller::returnResponse(200, "successful", $userNotifications);
            return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return json_encode($response);
        }
    }
    function notificationSeen(Request $req)
    {
        try {
            $ids = $req->ids;
            System_Notification::whereIn('id', $ids)->update(['seen' => 1]);
            $response = Controller::returnResponse(200, "successful", []);
            return json_encode($response);
        } catch (Exception $error) {
        }
    }
    function ChatNotifications(Request $req)
    {
        try {
            $userData=Controller::checkUser($req);
            $roomObj=new RoomController;
           $rooms=json_decode($roomObj->getRooms($req));
           $response = Controller::returnResponse(200, "successful",$rooms);
           return json_encode($response);
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return json_encode($response);
        }
    }
}
