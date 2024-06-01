<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\Stripe;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function testMonthlySubscription()
    {
        $response = $this->post('/subscribe', [
            'email' => 'test@example.com',
            'stripeToken' => 'tok_visa',
            'plan' => 'Pro_monthly_49',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('subscriptions', [
            'customer_email' => 'test@example.com',
            'plan' => 'Pro_monthly_49',
        ]);
    }

    public function testYearlySubscription()
    {
        $response = $this->post('/subscribe', [
            'email' => 'test@example.com',
            'stripeToken' => 'tok_visa',
            'plan' => 'Pro_annual_249',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('subscriptions', [
            'customer_email' => 'test@example.com',
            'plan' => 'Pro_annual_249',
        ]);
    }

    public function testWebhook()
    {
        $payload = json_decode(file_get_contents(__DIR__.'/webhook.json'), true);

        $response = $this->post('/webhook', $payload, [
            'Stripe-Signature' => 'test_signature'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('subscriptions', [
            'stripe_id' => $payload['data']['object']['subscription'],
        ]);
    }
}
