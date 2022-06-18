<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\SendInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\InitialProposalActions;
use App\Mail\draftMail;
// use PDF;

class MailController extends Controller
{
    public function sendInvitation($link, $code, $email){
        $details = [
            'link'=> $link,
            'code' => $code
        ];
        Mail::to($email)->send(new SendInvitation($details));
    }

    function testEmailWithPDF($Att,$email_body,$subject,$agency_name,$client_email,$client_name){
        // $data["email"] = "test@gmail.com";
        // $data["title"] = "How To Generate PDF And Send Email In Laravel 8 - Websolutionstuff";
        // $data["body"] = "How To Generate PDF And Send Email In Laravel 8";
        // $pdf = PDF::loadView('mail.initial-proposal-actions');
        $data = array(
            'subject' => $subject,
            'AgencyName' => $agency_name,
            'ClientName' => $client_name,
            'email_body' => $email_body,
            'pdf_file'=> public_path($Att)
        );
        // dd($data);
        // Mail::to($admin->email)->send(new WalletActions($details));
        // dd($details);
        Mail::mailer('smtp2')->to('barbarawiahmad07@gmail.com')->send(new draftMail($data));
        return 'done';
    }
}
