<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'client_id' => $this->client_id,
            'client' => ClientResource::make($this->whenLoaded('client')),
            'created_at' => $this->created_at->format('d.m.Y H:i:s'),
        ];
    }
}
