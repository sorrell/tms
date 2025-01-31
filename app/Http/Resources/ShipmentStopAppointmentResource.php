<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Shipments\ShipmentStop
 */
class ShipmentStopAppointmentResource extends JsonResource
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
            'appointment_at' => $this->appointment_at,
            'appointment_end_at' => $this->appointment_end_at,
            'appointment_type' => $this->appointment_type,
        ];
    }
}

