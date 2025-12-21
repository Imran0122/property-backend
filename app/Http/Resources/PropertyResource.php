<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'price'       => $this->price,
            'location'    => $this->location,
            'city'        => $this->city->name ?? null,
            'type'        => $this->propertyType->name ?? $this->type,
            'status'      => $this->status,
            'is_featured' => $this->is_featured,
            'images'      => $this->images->pluck('image_path'),
            'agent'       => [
                'id'    => $this->user->id ?? null,
                'name'  => $this->user->name ?? null,
                'email' => $this->user->email ?? null,
            ],
            'amenities'   => $this->amenities->pluck('name'),
            'created_at'  => $this->created_at->format('d M Y'),
        ];
    }
}
