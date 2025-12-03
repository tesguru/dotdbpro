<?php

namespace App\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title ?? null,
            'price' => $this->price ?? null,
            'location' =>  $this->location ?? null,
            'description' =>  $this->description ?? null,
            'property_id' => $this->property_id
        ];
    }
}
