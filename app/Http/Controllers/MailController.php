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

    function testEmailWithPDF(){
        // $data["email"] = "test@gmail.com";
        // $data["title"] = "How To Generate PDF And Send Email In Laravel 8 - Websolutionstuff";
        // $data["body"] = "How To Generate PDF And Send Email In Laravel 8";
        // $pdf = PDF::loadView('mail.initial-proposal-actions');
        $data = array(
            'subject' => 'test pdf',
            'projectName' => ' test project',
            'clientEmail' => 'shajrawi98@gmail.com',
            'status' => 1,
            'pdf_file'=> public_path('images/invoices/1652019226-10.pdf')
        );
        // dd($data);
        // Mail::to($admin->email)->send(new WalletActions($details));
        // dd($details);
        Mail::mailer('smtp2')->to('hamzahshajrawi@gmail.com')->send(new draftMail($data));
        return 'done';
    }
}
