<?php

namespace App\Services\Utility;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\UserAccount;
class DodoPaymentService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
          $this->baseUrl = config('services.dodo_payment.base_url');
        $this->apiKey  = config('services.dodo_payment.api_key');


    }

    protected function request($method, $endpoint, $data = [])
    {
        return Http::withToken($this->apiKey)
            ->$method("{$this->baseUrl}/{$endpoint}", $data)
            ->json();
    }

    public  function listProducts(array $data = [])
    {
        return $this->request('get', 'products', $data);
    }

    public function createCustomer( array $data)
    {

         $newdata['email'] = $data["email_address"];
            $newdata['name'] = $data["username"];
              $newdata['email'] = $data["email_address"];
       return $this->request('post', 'customers', $newdata);
    }

       public function createSubscription( array $data)
    {

         return $this->request('post', 'subscriptions', $data);
    }

public function save_webhook($data)
{
    Log::info('Dodo webhook received', $data);
    $payload = $data['data'] ?? [];

    DB::transaction(function () use ($data, $payload) {
        if (($payload['payload_type'] ?? null) === 'Subscription') {
            switch ($data['type']) {
                case 'subscription.active':
                case 'subscription.renewed':
                    Subscription::updateOrCreate(
                        ['subscription_id' => $payload['subscription_id']],
                        [
                            'user_id' => UserAccount::where('email_address', $payload['customer']['email'] ?? null)->value('user_id'),
                            'status' => "pending",
                            'product_id' => $payload['product_id'] ?? null,
                            'currency' => $payload['currency'] ?? null,
                            'amount' => $payload['recurring_pre_tax_amount'] ?? null,
                            'payment_frequency_count' => $payload['payment_frequency_count'] ?? null,
                            'payment_frequency_interval' => $payload['payment_frequency_interval'] ?? null,
                            'subscription_period_count' => $payload['subscription_period_count'] ?? null,
                            'subscription_period_interval' => $payload['subscription_period_interval'] ?? null,
                            'next_billing_date' => $payload['next_billing_date'] ?? null,
                            'previous_billing_date' => $payload['previous_billing_date'] ?? null,
                            'expires_at' => $payload['expires_at'] ?? null,
                            'raw_payload' => json_encode($payload),
                        ]
                    );
                    Log::info('Subscription saved', $payload);
                    break;

                case 'subscription.cancelled':
                    Subscription::where('subscription_id', $payload['subscription_id'])
                        ->update([
                            'status' => 'cancelled',
                            'raw_payload' => json_encode($payload),
                        ]);
                    Log::info('Subscription cancelled', $payload);
                    break;
            }
        }

        if (($payload['payload_type'] ?? null) === 'Payment') {
            if ($data['type'] === 'payment.succeeded') {
                $customer = $payload['customer'] ?? [];

                Payment::updateOrCreate(
                    ['payment_id' => $payload['payment_id']],
                    [
                        'user_id' => UserAccount::where('email_address', $customer['email'] ?? null)->value('user_id'),
                        'subscription_id' => $payload['subscription_id'] ?? null,
                        'business_id' => $payload['business_id'] ?? null,
                        'status' => $payload['status'] ?? null,
                        'total_amount' => $payload['total_amount'] ?? null,
                        'currency' => $payload['currency'] ?? null,
                        'payment_method' => $payload['payment_method'] ?? null,
                        'card_last_four' => $payload['card_last_four'] ?? null,
                        'card_type' => $payload['card_type'] ?? null,
                        'card_network' => $payload['card_network'] ?? null,
                        'customer_id' => $customer['customer_id'] ?? null,
                        'customer_name' => $customer['name'] ?? null,
                        'customer_email' => $customer['email'] ?? null,
                        'raw_payload' => json_encode($payload),
                    ]
                );
                  Subscription::updateOrCreate(
                        ['subscription_id' => $payload['subscription_id']],
                        [
                            'status' => "active",
                        ]
                    );
                Log::info('Payment succeeded and saved', $payload);
            }
        }
    });
    return response()->json(['status' => 'ok']);
}

public function paymentHistory($user_id){
return Payment::where('user_id', $user_id)->get();
}

public function getSubscriptionStatus($user_id){
return Subscription::where('user_id', $user_id)->get();
}
}
