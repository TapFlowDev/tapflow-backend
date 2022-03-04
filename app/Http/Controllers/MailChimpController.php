<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MailChimpController extends Controller
{
    //add row 
    function Insert(Request $req)
    {
    }
    //update row according to row id
    function Update($id)
    {
    }
    //delete row according to row id
    function Delete($id)
    {   
    }
    function test()
    {
        $mailchimp = new \MailchimpMarketing\ApiClient();

        $mailchimp->setConfig([
            'apiKey' => config('services.mailchimp.key'),
            'server' => 'us20'
        ]);

        $response = $mailchimp->customerJourneys->trigger(7622, 31074, [
            "email_address" => "hamzahshajrawi@gmail.com",
        ]);

        // $response = $mailchimp->ping->get();
        dd($response);
    }
}
