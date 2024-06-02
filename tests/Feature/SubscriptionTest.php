<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;
use Illuminate\Support\Str;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testMonthlySubscription()
    {
        $randomEmail = 'test+' . Str::random(10) . '@example.com';

        // Mock the Stripe Customer and Subscription
        $mockCustomer = Mockery::mock('overload:Stripe\Customer');
        $mockCustomer->shouldReceive('all')
            ->with(['email' => $randomEmail])
            ->once()
            ->andReturn((object) ['data' => []]);

        $mockCustomer->shouldReceive('create')
            ->once()
            ->andReturn((object) ['id' => 'cus_test']);

        $mockSubscription = Mockery::mock('overload:Stripe\Subscription');
        $mockSubscription->shouldReceive('create')
            ->once()
            ->andReturn((object) [
                'id' => 'sub_test',
                'plan' => (object) ['interval' => 'month']
            ]);

        // Make the POST request to subscribe
        $response = $this->post('/subscribe', [
            'email' => $randomEmail,
            'stripeToken' => 'tok_visa',
            'plan' => 'Pro_monthly_49',
        ]);

        // Assert the response status and the database has the new subscription
        $response->assertStatus(302);
        $this->assertDatabaseHas('subscriptions', [
            'customer_email' => $randomEmail,
            'plan' => 'Pro_monthly_49',
            'planName' => 'Pro_monthly_49',
        ]);
    }

    public function testYearlySubscription()
    {
        $randomEmail = 'test+' . Str::random(10) . '@example.com';

        // Mock the Stripe Customer and Subscription
        $mockCustomer = Mockery::mock('overload:Stripe\Customer');
        $mockCustomer->shouldReceive('all')
            ->with(['email' => $randomEmail])
            ->once()
            ->andReturn((object) ['data' => []]);

        $mockCustomer->shouldReceive('create')
            ->once()
            ->andReturn((object) ['id' => 'cus_test']);

        $mockSubscription = Mockery::mock('overload:Stripe\Subscription');
        $mockSubscription->shouldReceive('create')
            ->once()
            ->andReturn((object) [
                'id' => 'sub_test',
                'plan' => (object) ['interval' => 'year']
            ]);

        // Make the POST request to subscribe
        $response = $this->post('/subscribe', [
            'email' => $randomEmail,
            'stripeToken' => 'tok_visa',
            'plan' => 'Pro_annual_249',
        ]);

        // Assert the response status and the database has the new subscription
        $response->assertStatus(302);
        $this->assertDatabaseHas('subscriptions', [
            'customer_email' => $randomEmail,
            'plan' => 'Pro_annual_249',
            'planName' => 'Pro_annual_249',
        ]);
    }

    public function testHandleWebhook()
    {
        // Sample Stripe webhook payload for subscription created
        $payload = [
            "id" => "sub_1PNFssIjvJe8MwyW2J2ntzs0",
            "object" => "subscription",
            "application" => null,
            "application_fee_percent" => null,
            "automatic_tax" => ["enabled" => false, "liability" => null],
            "billing_cycle_anchor" => 1717340386,
            "billing_cycle_anchor_config" => null,
            "billing_thresholds" => null,
            "cancel_at" => null,
            "cancel_at_period_end" => false,
            "canceled_at" => null,
            "cancellation_details" => ["comment" => null, "feedback" => null, "reason" => null],
            "collection_method" => "charge_automatically",
            "created" => 1717340386,
            "currency" => "inr",
            "current_period_end" => 1719932386,
            "current_period_start" => 1717340386,
            "customer" => "cus_QDhHqYIBzylVg1",
            "days_until_due" => null,
            "default_payment_method" => null,
            "default_source" => null,
            "default_tax_rates" => [],
            "description" => null,
            "discount" => null,
            "discounts" => [],
            "ended_at" => null,
            "invoice_settings" => ["account_tax_ids" => null, "issuer" => ["type" => "self"]],
            "items" => [
                "object" => "list",
                "data" => [[
                    "id" => "si_QDhHwGMNj2zDY1",
                    "object" => "subscription_item",
                    "billing_thresholds" => null,
                    "created" => 1717340386,
                    "discounts" => [],
                    "metadata" => [],
                    "plan" => [
                        "id" => "price_1PMpQiIjvJe8MwyWXTCe1M4s",
                        "object" => "plan",
                        "active" => true,
                        "aggregate_usage" => null,
                        "amount" => 4900,
                        "amount_decimal" => "4900",
                        "billing_scheme" => "per_unit",
                        "created" => 1717238696,
                        "currency" => "inr",
                        "interval" => "month",
                        "interval_count" => 1,
                        "livemode" => false,
                        "metadata" => [],
                        "meter" => null,
                        "nickname" => null,
                        "product" => "prod_QDFwg2d3xFVpDC",
                        "tiers_mode" => null,
                        "transform_usage" => null,
                        "trial_period_days" => null,
                        "usage_type" => "licensed"
                    ],
                    "price" => [
                        "id" => "price_1PMpQiIjvJe8MwyWXTCe1M4s",
                        "object" => "price",
                        "active" => true,
                        "billing_scheme" => "per_unit",
                        "created" => 1717238696,
                        "currency" => "inr",
                        "custom_unit_amount" => null,
                        "livemode" => false,
                        "lookup_key" => null,
                        "metadata" => [],
                        "nickname" => null,
                        "product" => "prod_QDFwg2d3xFVpDC",
                        "recurring" => [
                            "aggregate_usage" => null,
                            "interval" => "month",
                            "interval_count" => 1,
                            "meter" => null,
                            "trial_period_days" => null,
                            "usage_type" => "licensed"
                        ],
                        "tax_behavior" => "unspecified",
                        "tiers_mode" => null,
                        "transform_quantity" => null,
                        "type" => "recurring",
                        "unit_amount" => 4900,
                        "unit_amount_decimal" => "4900"
                    ],
                    "quantity" => 1,
                    "subscription" => "sub_1PNFssIjvJe8MwyW2J2ntzs0",
                    "tax_rates" => []
                ]],
                "has_more" => false,
                "total_count" => 1,
                "url" => "/v1/subscription_items?subscription=sub_1PNFssIjvJe8MwyW2J2ntzs0"
            ],
            "latest_invoice" => "in_1PNFssIjvJe8MwyW658ZazBb",
            "livemode" => false,
            "metadata" => [],
            "next_pending_invoice_item_invoice" => null,
            "on_behalf_of" => null,
            "pause_collection" => null,
            "payment_settings" => ["payment_method_options" => null, "payment_method_types" => null, "save_default_payment_method" => "off"],
            "pending_invoice_item_interval" => null,
            "pending_setup_intent" => null,
            "pending_update" => null,
            "plan" => [
                "id" => "price_1PMpQiIjvJe8MwyWXTCe1M4s",
                "object" => "plan",
                "active" => true,
                "aggregate_usage" => null,
                "amount" => 4900,
                "amount_decimal" => "4900",
                "billing_scheme" => "per_unit",
                "created" => 1717238696,
                "currency" => "inr",
                "interval" => "month",
                "interval_count" => 1,
                "livemode" => false,
                "metadata" => [],
                "meter" => null,
                "nickname" => null,
                "product" => "prod_QDFwg2d3xFVpDC",
                "tiers_mode" => null,
                "transform_usage" => null,
                "trial_period_days" => null,
                "usage_type" => "licensed"
            ],
            "quantity" => 1,
            "schedule" => null,
            "start_date" => 1717340386,
            "status" => "active",
            "test_clock" => null,
            "transfer_data" => null,
            "trial_end" => null,
            "trial_settings" => ["end_behavior" => ["missing_payment_method" => "create_invoice"]],
            "trial_start" => null
        ];

        $timestamp = time();
        $secret = config('services.stripe.webhook_secret');
        $payloadJson = json_encode($payload);
        $signature = $this->generateStripeSignature($payloadJson, $timestamp, $secret);

        // Send POST request to webhook endpoint
        $response = $this->withHeaders([
            'Stripe-Signature' => "t={$timestamp},v1={$signature}",
        ])->postJson('/stripe/webhook', $payload);

        // Assert response status is 200 OK
        $response->assertStatus(200);
        // Add additional assertions as needed
    }

    /**
     * Generate a Stripe signature for testing.
     *
     * @param string $payload
     * @param int $timestamp
     * @param string $secret
     * @return string
     */
    private function generateStripeSignature($payload, $timestamp, $secret)
    {
        $signedPayload = "{$timestamp}.{$payload}";
        return hash_hmac('sha256', $signedPayload, $secret);
    }
}
