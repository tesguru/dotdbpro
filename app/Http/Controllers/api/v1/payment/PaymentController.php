<?php

namespace App\Http\Controllers\api\v1\payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Utility\DodoPaymentService;
use App\Http\Requests\Payment\CreateSubscriptionRequest;
use App\Traits\JsonResponseTrait;
use App\Models\UserAccount;
use Illuminate\Support\Facades\Log;

use Exception;

class PaymentController extends Controller
{
    use JsonResponseTrait;

    protected $service;

    public function __construct()
    {
        $this->services = new DodoPaymentService();
    }

    public function testing()
    {
        return $this->successResponse(message: "API Testing");
    }

    public function listProducts(Request $request)
    {
        try {
            $getAllProducts = $this->services->listProducts();
            return $this->successDataResponse(data: $getAllProducts);
        } catch (Exception $ex) {
            return $this->errorResponse(message: $ex->getMessage());
        }
    }

public function createSubscription(String $product_id, Request $request)
{
    try {
        $user = $request->user();
            $user_id = $user->user_id;
     $getDomainDetails = UserAccount::where("user_id", $user_id)->first();

     $customer_id = $getDomainDetails->dodo_customer_id;

        $apiData = [
            'product_id' => $product_id,
            'quantity' => 1,
            'return_url' => "http://localhost:3000/login",
            'payment_link' => true,
            'allowed_payment_method_types' => ['credit'],
            'customer' => ['customer_id' => $customer_id],
            'billing' => [
                'city' => "Newyork",
                'country' => 'US',
                'state' => "NY",
                'street' => "123 Wall Street, Apt 4B",
                'zipcode' => "10005",
            ],
            'billing_currency' => null,
            'metadata' => (object)[],
            'addons' => [],
        ];

        $createSubscription = $this->services->createSubscription($apiData);
        return $this->successDataResponse(data: $createSubscription);

    } catch (Exception $ex) {
        return $this->errorResponse(message: $ex->getMessage());
    }
}

    public function handle(Request $request)
{
    $rawBody = $request->getContent();
    $payload = json_decode($rawBody, true);
    $this->services->save_webhook($payload);
    if (isset($response) && !$response->successful()) {
        Log::error('Failed to forward webhook', ['response' => $response->body()]);
    }
    return response()->json(['message' => 'Webhook processed'], 200);
}

public function getPaymentHistory(Request $request){
 try {
         $user = $request->user();
            $user_id = $user->user_id;
               $paymentHistory = $this->services->paymentHistory($user_id);
                return $this->successDataResponse(data: $paymentHistory);
        } catch (Exception $ex) {
            return $this->errorResponse(message: $ex->getMessage());
        }
}

public function getSubscriptionStatus(Request $request){
 try {
        $user = $request->user();
        $user_id = $user->user_id;
        $getSubscriptionStatus = $this->services->getSubscriptionStatus($user_id);
      return $this->successDataResponse(data:  $getSubscriptionStatus);
     }
     catch (Exception $ex) {
        return $this->errorResponse(message: $ex->getMessage());
        }
}

}
