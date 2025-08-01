<?php

namespace App\Http\Resources;

use App\Actions\Utilities\FormatPhoneForCountry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Contact
 */
class ContactResource extends JsonResource
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
            'contact_type' => $this->contact_type,
            'title' => $this->title,
            'name' => $this->name,
            'email' => $this->email,
            'mobile_phone' => FormatPhoneForCountry::handle($this->mobile_phone),
            'office_phone' => FormatPhoneForCountry::handle($this->office_phone),
            'office_phone_extension' => $this->office_phone_extension,
            'selectable_label' => $this->selectable_label,
            'contact_for' => $this->whenLoaded('contactFor', fn () => $this->contactFor->toArray()),
            'contact_for_type' => $this->contact_for_type,
            'contact_for_id' => $this->contact_for_id,
        ];
    }
}

