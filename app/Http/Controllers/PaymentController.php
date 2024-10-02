<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Stripe\Exception\CardException;
use Stripe\Stripe;

class PaymentController extends Controller
{
    public function call(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'stripeToken' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Stripe token is required'], 400);
        }

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $charge = \Stripe\Charge::create([
                'amount' => 100, // Amount in cents (e.g., $10.00)
                'currency' => 'usd',
                'source' => $request->stripeToken,
                'description' => 'Charge for Order #12345',
                 'metadata' => [
                 'order_id' => '12345',
                 'customer_name' => 'Talha Rehman',
              ],
                 'receipt_email' => 'talha@example.com',
            ]);

            return response()->json(['success' => 'Payment successful', 'charge' => $charge]);
        } catch (CardException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment failed'], 500);
        }
    }
}
