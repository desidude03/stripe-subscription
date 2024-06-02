<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Stripe\Event;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;

class ProcessSubscriptionEvent implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function handle()
    {
        try {
            // Handle the event based on its type
            switch ($this->event->type) {
                case 'invoice.paid':
                    $this->handleInvoicePaid();
                    break;
                case 'invoice.payment_failed':
                    $this->handleInvoicePaymentFailed();
                    break;
                // Add more cases for other types of events
                default:
                    Log::info("Received unhandled event type: {$this->event->type}");
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Error processing Stripe event: ' . $e->getMessage());
        }
    }

    protected function handleInvoicePaid()
    {
        $subscriptionId = $this->event->data->object->subscription;
        $subscription = Subscription::where('stripe_id', $subscriptionId)->first();
        if ($subscription) {
            $subscription->update(['status' => 'active']);
            Log::info("Subscription {$subscriptionId} marked as active.");
        } else {
            Log::warning("Subscription with ID {$subscriptionId} not found.");
        }
    }

    protected function handleInvoicePaymentFailed()
    {
        $subscriptionId = $this->event->data->object->subscription;
        $subscription = Subscription::where('stripe_id', $subscriptionId)->first();
        if ($subscription) {
            $subscription->update(['status' => 'past_due']);
            Log::warning("Subscription {$subscriptionId} marked as past_due due to payment failure.");
        } else {
            Log::warning("Subscription with ID {$subscriptionId} not found.");
        }
    }
}
