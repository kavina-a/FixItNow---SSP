<?php

namespace App\Http\Controllers;

use Stripe\Charge;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    public function charge(Request $request)
    {
        // Set Stripe API key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Charge the customer
            $charge = Charge::create([
                'amount' => $request->amount * 100, // Amount in cents
                'currency' => 'usd',
                'source' => $request->token,
                'description' => 'Payment for services',
            ]);

            // Store payment info in the database (if needed)
            return response()->json(['status' => 'Payment successful', 'charge' => $charge]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'Payment failed', 'error' => $e->getMessage()], 500);
        }
    }


    public function createPaymentIntent(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount,
                'currency' => $request->currency,
            ]);

            return response()->json([
                'client_secret' => $paymentIntent->client_secret,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
