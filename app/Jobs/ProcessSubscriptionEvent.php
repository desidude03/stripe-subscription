<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Stripe\Event;
use App\Models\Subscription;

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
        // Handle the event based on its type
        switch ($this->event->type) {
            case 'invoice.paid':
                // Handle paid invoice event
                $subscriptionId = $this->event->data->object->subscription;
                $subscription = Subscription::where('stripe_id', $subscriptionId)->first();
                if ($subscription) {
                    // Update subscription status or perform other actions
                    $subscription->update(['status' => 'active']);
                }
                break;
            case 'invoice.payment_failed':
                // Handle failed invoice payment event
                return redirect()->back()->with('error', 'Subscription failed: ');
                break;
            // Add more cases for other types of events
            default:
                // Handle other events or ignore them
                break;
        }
    }
}