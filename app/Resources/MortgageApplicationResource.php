<?php

namespace App\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class MortgageApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'location' => $this->propertyData->location ?? null,
            'plan_name' =>  $this->mortgagePlanData->name ?? null,
            'property_name' =>  $this->propertyData->title ?? null,
            'price' =>  $this->propertyData->price ?? null,
            'customer_name' =>  $this->customerData->name ?? null,
            'customer_phonenumber' =>  $this->customerData->phone_number ?? null,
        ];
    }
}
