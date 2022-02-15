<?php

namespace App\Http\Controllers\AdminTool;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\CustomMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;
use Exception;

class EmailController extends Controller
{
    function show($id)
    {
        $user = User::find($id);
        return view('AdminTool.Email.sendEmail', ['user' => $user]);
    }
    function send(Request $req)
    {

        try {
            $details = [
                "subject" => $req->subject,
                "content" => $req->content
            ];
            Mail::to($req->email)->send(new CustomMail($details));
            // Mail::mailer('smtp2')->to($req->email)->send(new CustomMail($details));
            $req->session()->flash('success', 'email sent successfully');
        } catch (\Exception $error) {
            $req->session()->flash('error', 'email was not sent due to and error');
        }
        // Mail::to($req->email)->send(new CustomMail($details));
        return redirect()->back();
    }
}
