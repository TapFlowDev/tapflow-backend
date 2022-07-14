<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\notification_settings;
use Exception;

class NotificationSettingsController extends Controller
{
    //add row 
    function Insert($user_id)
    {
        try {
            for ($i = 1; $i <= 4; $i++) {
                $data = array(
                    "user_id" => $user_id,
                    "notification_type" => $i
                );
                notification_settings::create($data);
            }
            $response = Controller::returnResponse(200, "successful", []);
            return (json_encode($response));
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function emailNotification(Request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            notification_settings::where('user_id', $userData['user_id'])
                ->where('notification_type', $req->type)
                ->update(['email' => $req->show]);
            $response = Controller::returnResponse(200, "successful", []);
            return (json_encode($response));
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
    function pushNotification(request $req)
    {
        try {
            $userData = Controller::checkUser($req);
            notification_settings::where('user_id', $userData['user_id'])
                ->where('notification_type', $req->type)
                ->update(['notification' => $req->show]);
            $response = Controller::returnResponse(200, "successful", []);
            return (json_encode($response));
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, "something went wrong", $error->getMessage());
            return (json_encode($response));
        }
    }
}
