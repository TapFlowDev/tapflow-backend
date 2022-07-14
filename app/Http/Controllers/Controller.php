<?php

namespace App\Http\Controllers;

use App\Mail\WalletActions;
use App\Models\Group;
use App\Models\Group_member;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Models\System_Notification;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public $NotificationsType=array(
        "final_proposals"=>3,
    );
    public function returnResponse($requestStatus = 200, $requestMessage = 'successfully', $array)
    {
        $response = array(
            "status" => array(
                "message" => $requestMessage,
                "code" => $requestStatus
            ),
            "data" => $array
        );
        return $response;
    }

    // this function to check if the user belong to a team or not and what is the privileges for this user
    public function checkUser($req)
    {
        try {
            $userData = $req->user();
            $member = DB::table('group_members')
                ->where('user_id', '=', $userData->id)
                ->first();
            if ($member === null) {
                return ['exist' => 0];
            } else {
                $verified = DB::table('groups')
                    ->where('id', '=', $member->group_id)
                    ->select('verified')
                    ->first();

                return ['exist' => 1, 'user_id' => $member->user_id, 'group_id' => $member->group_id, 'privileges' => $member->privileges, "type" => $userData['type'], 'verified' => $verified->verified];
            }
        } catch (Exception  $error) {
            $response = Controller::returnResponse(500, "check user error", $error->getMessage());
            return (json_encode($response));
        }
    }
    public function notifyAdminWalletAction($groupId, $transactionType, $amount, $currentBalance)
    {
        $groupObj = new GroupMembersController;
        $groupType = Group::select('type')->where('id', '=', $groupId)->get()->first();
        if ($groupType->type == 1) {
            $admin = $groupObj->getTeamAdminByGroupId($groupId);
        } elseif ($groupType->type == 2) {
            $groupMember = Group_member::where('group_id', '=', $groupId)
                ->where('privileges', '=', 1)
                ->get()
                ->first();
            $admin = User::where('id', $groupMember->user_id)->get()->first();
        }
        $details = array(
            'subject' => 'Wallet Transaction',
            'transactionType' => $transactionType,
            'amount' => $amount,
            'currentAmount' => $currentBalance
        );
        return Mail::mailer('smtp')->to($admin->email)->send(new WalletActions($details));
        // dd($details);
        //return Mail::mailer('smtp')->to('hamzahshajrawi@gmail.com')->send(new WalletActions($details));
    }
    /**
     * receiver id  == group id you want to send the notification for them
     * notification type 1 chat
     * notification type 2 actions
     */
    
    public function sendNotification($receiver_id, $title, $body,$link,$type,$action,$action_id)
    {
        try{
        	 $serverLink="https://tapflow.dev";
            //  $serverLink="https://testtest.tapflow.app";
            //  $serverLink="https://tapflow.app";
           
       
        $firebaseObj = new FireBaseNotificationsController;
        $groupMembersObj = new GroupMembersController;
      
        $actionLink=$serverLink.$link;
        if($type== 2){
        $actionType=$this->NotificationsType[$action];
        $groupAdmins = $groupMembersObj->filterAdminsAcceptNotifications($receiver_id, $actionType);
        $fcmTokens=$groupAdmins->pluck('fcm_token')->toArray();
        $admins=$groupAdmins->pluck('user_id')->toArray();
      foreach( $admins as $id)
      {
        System_Notification::create( ['title' => $title,'body'=>$body,'receiver_id'=>$id,"action"=>$action,"action_id"=>$action_id,"link"=>$link]);
      }
    }
        else{$fcmTokens=$receiver_id;}
       
        $data = array('FcmToken' => $fcmTokens, 'title' => $title, 'body' => $body,'link'=>$actionLink,'type'=>$type);
        $notify = $firebaseObj->sendFireBaseNotification($data);
        return $notify;
    }catch(Exception $error)
    {
      return  ['code'=>500,'msg'=>$error->getMessage()];
    }
}
}
