<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;

class SubscriptionSeeder extends Seeder
{
    public function run()
    {
        // Set your Stripe API key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Create Pro Plan Monthly Product
        $proMonthlyProduct = Product::create([
            'name' => 'Pro Plan Monthly',
            'type' => 'service',
        ]);

        // Create Pro Plan Monthly Price
        Price::create([
            'product' => $proMonthlyProduct->id,
            'unit_amount' => 4900, // Amount in cents (INR 49)
            'currency' => 'inr',
            'recurring' => ['interval' => 'month'],
            'nickname' => 'Pro_monthly_49',
        ]);

        // Create Pro Plan Annual Product
        $proAnnualProduct = Product::create([
            'name' => 'Pro Plan Annual',
            'type' => 'service',
        ]);

        // Create Pro Plan Annual Price
        Price::create([
            'product' => $proAnnualProduct->id,
            'unit_amount' => 24900, // Amount in cents (INR 249)
            'currency' => 'inr',
            'recurring' => ['interval' => 'year'],
            'nickname' => 'Pro_annual_249',
        ]);
    }
}

