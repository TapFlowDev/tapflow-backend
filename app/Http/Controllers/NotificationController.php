<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RoomMembers;
use Illuminate\Http\Request;
use App\Models\System_Notification;
use Exception;
use Illuminate\Support\Facades\DB;

use function GuzzleHttp\Promise\each;

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
}
