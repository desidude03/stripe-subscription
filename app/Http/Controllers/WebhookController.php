<?php

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\Subscription;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type == 'invoice.payment_succeeded') {
            $subscription = Subscription::where('stripe_id', $event->data->object->subscription)->first();
            if ($subscription) {
                $subscription->ends_at = $subscription->plan === 'Pro_monthly_49' ? now()->addMonth() : now()->addYear();
                $subscription->save();
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}

