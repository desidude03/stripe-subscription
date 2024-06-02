<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use App\Models\Subscription;
use App\Events\SubscriptionRenewed;
use App\Jobs\ProcessSubscriptionEvent;
use Stripe\Event;

class WebhookController extends Controller
{
    // public function handleWebhook(Request $request)
    // {
    //     $payload = $request->getContent();
    //     $sigHeader = $request->header('Stripe-Signature');

    //     try {
    //         $event = Webhook::constructEvent($payload, $sigHeader, config('services.stripe.webhook_secret'));

    //         // Handle the event asynchronously using a queued job
    //         if ($event) {
    //             ProcessSubscriptionEvent::dispatch($event);
    //         }

    //         return response()->json(['success' => true]);
    //     } catch (SignatureVerificationException $e) {
    //         return response()->json(['error' => 'Invalid signature'], 400);
    //     }
    // }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret'); // Make sure to set this in your .env file

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Dispatch the job
        ProcessSubscriptionEvent::dispatch($event);

        return response()->json(['status' => 'success']);
    }
}
