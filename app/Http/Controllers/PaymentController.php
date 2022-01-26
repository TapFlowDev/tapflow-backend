<?php

namespace App\Http\Controllers;

use Stripe;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class PaymentController extends Controller
{
    function checkout(Request $req)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $paymentIntent =  Stripe\PaymentIntent::create([
            "amount" => 500 * 100,
            "currency" => 'usd',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);
        $output = [
            'clientSecret' => $paymentIntent->client_secret,
        ];

        echo json_encode($output);
    }
}
