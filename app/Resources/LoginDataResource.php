<?php

namespace App\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class LoginDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'username' => $this->username ?? null,
            'email_address' => $this->email_address ?? null,
            'verify_status' =>  $this->verify_status ?? null,    
        ];
    }
}
