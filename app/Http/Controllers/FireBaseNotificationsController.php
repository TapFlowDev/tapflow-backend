<?php

namespace App\Http\Controllers;
  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\User;

class FireBaseNotificationsController extends Controller
{
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
  
    public function sendFireBaseNotification($request)
    {
        try{
            $response = Controller::returnResponse(101, "Validation Error", $request);
            return json_encode($response);
        $url = 'https://fcm.googleapis.com/fcm/send';
        // $FcmToken = User::whereNotNull('fcm_token')->pluck('fcm_token')->all();
        // $FcmToken=User::where('id',$request->to_id)->select('fcm_token')->pluck('fcm_token')->toArray();
    //    array_push($FcmToken,$FcmToken[0]);
        $serverKey = env('FCM_SERVER_KEY');
        
        $data = array(
            "registration_ids" => $request->FcmToken,
            "notification" => array(
                "title" => $request->title,
                "body" => $request->body,  
            )
            );
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
            $response=['res'=>'false','data'=>$result];
            die('Curl failed: ' . curl_error($ch));
        }        

        // Close connection
        $response=['res'=>'true','data'=>$result];
        curl_close($ch);

        // FCM response 
       return ['code'=>200,'msg'=>'successful'];
    }catch(Exception $error)
    {
        return ['code'=>500,'msg'=>$error->getMessage()];
    }
    }
}
