<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;

class NotificationController extends Controller
{
   
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }
  
    public function storeToken(Request $request)
    {
        try{
        // auth()->user()->update(['fcm_token'=>$request->token]);
        User::where('id',$request->user_id)->update(['fcm_token'=>$request->token]);
        $response=Controller::returnResponse(200,'successful',['Token successfully stored.']);
        return (json_encode($response));
        }catch(Exception $error)
        {
            $response=Controller::returnResponse(200,'successful',$error->getMessage());
            return (json_encode($response));   
        }
    }
  
    public function sendNotification(Request $request)
    {
        try{
        $url = 'https://fcm.googleapis.com/fcm/send';
        // $FcmToken = User::whereNotNull('fcm_token')->pluck('fcm_token')->all();
        $FcmToken=User::where('id',$request->to_id)->select('fcm_token')->first()->fcm_token;
        $serverKey = env('FCM_SERVER_KEY');
  
        $data = [
            "registration_ids" => $FcmToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,  
            ]
        ];
        $encodedData = json_encode($data);
    
        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

        // Execute post
        $result = curl_exec($ch);

        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }        

        // Close connection
        curl_close($ch);

        // FCM response 
        $response=Controller::returnResponse(200,'successful',$result);
        return (json_encode($response));
    }catch(Exception $error)
    {
        $response=Controller::returnResponse(200,'successful',$error->getMessage());
        return (json_encode($response));   
    }
    }
    
}
