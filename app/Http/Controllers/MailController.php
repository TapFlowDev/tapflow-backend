<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\SendInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class MailController extends Controller
{
    public function sendInvitation($link, $code, $email){
        $details = [
            'link'=> $link,
            'code' => $code
        ];
        Mail::to($email)->send(new SendInvitation($details));
    }
}
