<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'websites' => $this->when(
                $this->resource->relationLoaded('websites'),
                $this->websites->pluck('website'),
                []
            ),
            'emails' => $this->when(
                $this->resource->relationLoaded('emails'),
                $this->emails->pluck('email'),
                []
            ),
            'email' => $this->when(
                $this->resource->relationLoaded('emails'),
                $this->emails->where('is_main', 1)->first()->email
            ),
            'country_id' => $this->country_id,
            'country' => CountryResource::make($this->whenLoaded('country')),
            'created_at' => $this->created_at,
        ];
    }
}
