<?php

namespace App\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class MortgagePlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name ?? null,
            'duration_months' => $this->duration_months ?? null,
            'interest_rate' =>  $this->interest_rate ?? null,
            'plan_id' =>  $this->plan_id ?? null,
        ];
    }
}
