<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use App\Models\Subscription as LocalSubscription;

class SubscriptionController extends Controller
{
    public function checkout()
    {
        return view('checkout');
    }

    // public function subscribe(Request $request)
    // {
    //     return $request;
    //     Stripe::setApiKey(config('services.stripe.secret'));

    //     try {
    //         $customer = Customer::create([
    //             'email' => $request->email,
    //             'source' => $request->stripeToken,
    //         ]);

    //     //     $plans = \Stripe\Plan::all(['limit' => 10]); 
    //     //     return $plans;

    //         $subscription = Subscription::create([
    //             'customer' => $customer->id,
    //             'items' => [['plan' => $request->plan]],
    //         ]);

    //         // $plansDetails = $subscription;
    //         // return $plansDetails->plan->interval;

    //         $endsAt = $subscription->plan->interval === 'month' ? now()->addMonth() : now()->addYear();
    //         $planName = $subscription->plan->interval === 'month' ? 'Pro_monthly_49' : 'Pro_annual_249';

    //        $res = LocalSubscription::create([
    //             'stripe_id' => $subscription->id,
    //             'customer_email' => $request->email,
    //             'plan' => $request->plan,
    //             'planName' => $planName,
    //             'ends_at' => $endsAt,
    //             'updated_at' => now(),
    //             'created_at' => now(),
    //         ]);
    //        return $res;

    //         return redirect()->back()->with('success', 'Subscription successful!');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Subscription failed: ' . $e->getMessage());
    //     }
    // }

    public function subscribe(Request $request)
{
    Stripe::setApiKey(config('services.stripe.secret'));

    try {
        // Check if a customer with the given email already exists
        $existingCustomers = Customer::all(['email' => $request->email]);

        if (count($existingCustomers->data) > 0) {
            return redirect()->back()->with('error', 'Customer with this email already exists.');
        }

        // Create a new Stripe customer
        $customer = Customer::create([
            'email' => $request->email,
            'source' => $request->stripeToken,
        ]);

        // Create a new Stripe subscription
        $subscription = Subscription::create([
            'customer' => $customer->id,
            'items' => [['plan' => $request->plan]],
        ]);

        // Determine the subscription end date and plan name
        $endsAt = $subscription->plan->interval === 'month' ? now()->addMonth() : now()->addYear();
        $planName = $subscription->plan->interval === 'month' ? 'Pro_monthly_49' : 'Pro_annual_249';

        // Create a new local subscription record
        $res = LocalSubscription::create([
            'stripe_id' => $subscription->id,
            'customer_email' => $request->email,
            'plan' => $request->plan,
            'planName' => $planName,
            'ends_at' => $endsAt,
            'updated_at' => now(),
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Subscription successful!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Subscription failed: ' . $e->getMessage());
    }
}
}

