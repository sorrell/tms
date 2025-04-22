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
            'stop_id' => $this->stop_id,
            'created_by' => $this->created_by,
            'eta' => $this->eta,
            'reported_trailer_temp' => $this->reported_trailer_temp,
            'contact_name' => $this->contact_name,
            'contact_method' => $this->contact_method,
            'contact_method_detail' => $this->contact_method_detail,
            'arrived_at' => $this->arrived_at,
            'left_at' => $this->left_at,
            'loaded_unloaded_at' => $this->loaded_unloaded_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'carrier' => $this->whenLoaded('carrier'),
            'shipment' => $this->whenLoaded('shipment'),
            'stop' => $this->whenLoaded('stop'),
            'creator' => $this->whenLoaded('creator'),
        ];
    }
} 