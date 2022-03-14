<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\User;


class ResetPasswordController extends Controller
{
    function sendLinkResetPassword(Request $request)
    {
        $userData=Controller::checkUser($request);
        try {
            $rules = array(
                "email" => "email|required|exists:users,email"
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $responseData = $validator->errors();
                $response = Controller::returnResponse(101, "Validation Error", $responseData);

                return json_encode($response);
            }
            $token = Str::random(60);
            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
            if($userData['type']==2)
            {
            $urlll="client/reset-password?t=";
            }
            elseif($userData['type']=1)
            {
                $urlll="reset-password?t=";
            }
            if (env('APP_ENV') == 'local') {
                $url = env('APP_URL') . $urlll . $token;
            } elseif (env('APP_ENV') == 'dev') {
                $url = "https://www.tapflow.dev/".$urlll. $token;
            } else {
                $url = "https://www.tapflow.app/".$urlll . $token;
            }
            $details = [
                'url' => $url
            ];
            Mail::to($request->email)->send(new ResetPassword($details));
            $response = Controller::returnResponse(200, 'email sent successfully', array());

            return json_encode($response);
        } catch (Exception $error) {
            $responseData = array("error" => $error->getMessage());
            $response = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
            return json_encode($response);
        }
    }
    function resetPasswordCheck(Request $request)
    {
        // dd($request->t);
        $rules = array(
            "password" => "required|min:8|max:255"
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {

            $responseData = $validator->errors();
            $response = Controller::returnResponse(101, "Validation Error", $responseData);
            return (json_encode($response));
        }
        try {
            $token = $request->t;
            $userData = DB::table('password_resets')->where('token', $token)->get()->first();
            if (empty($userData)) {
                $response = Controller::returnResponse(422, 'token does not exist', array());
                return json_encode($response);
            }
            $currentTime = Carbon::now();
            $timeDiff = $currentTime->diffInHours($userData->created_at);
            if ($timeDiff >= 24) {
                $response = Controller::returnResponse(422, 'token expired', array());
                return json_encode($response);
            } else {
                // $userObj = new User;
                $user = User::where('email', $userData->email)->get()->first();
                $user->password = $request->password;
                $user->tokens()->delete();
                $user->save();
                DB::table('password_resets')->where('email', $userData->email)->delete();
            }
            $response = Controller::returnResponse(200, "password changed successfully", array());
            return json_encode($response);
        } catch (Exception $error) {
            $responseData = array("error" => $error->getMessage());
            $response = Controller::returnResponse(500, "There IS Error Occurred", $responseData);
            return json_encode($response);
        }
    }
}
