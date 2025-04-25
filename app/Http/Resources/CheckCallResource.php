<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\CheckCalls\CheckCall
 */
class CheckCallResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'carrier_id' => $this->carrier_id,
            'shipment_id' => $this->shipment_id,
            'created_by' => $this->created_by,
            'is_late' => $this->is_late,
            'eta' => $this->eta,
            'reported_trailer_temp' => $this->reported_trailer_temp,
            'contact_name' => $this->contact_name,
            'contact_method' => $this->contact_method,
            'contact_method_detail' => $this->contact_method_detail,
            'details' => $this->details,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'carrier' => $this->whenLoaded('carrier'),
            'shipment' => $this->whenLoaded('shipment'),
            'creator' => $this->whenLoaded('creator'),
        ];
    }
} 