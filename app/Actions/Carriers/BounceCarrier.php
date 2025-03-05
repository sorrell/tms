<?php

namespace App\Actions\Carriers;

use App\Enums\Carriers\BounceType;
use App\Events\Shipments\ShipmentCarrierBounced;
use App\Http\Resources\Carriers\CarrierBounceResource;
use App\Models\Carriers\CarrierBounce;
use App\Models\Shipments\Shipment;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class BounceCarrier
{
    use AsAction;

    public function handle(
        Shipment $shipment,
        BounceType|string $bounceType,
        ?string $reason = null,
    ): CarrierBounce
    {
        $bounce = CarrierBounce::create([
            'shipment_id' => $shipment->id,
            'carrier_id' => $shipment->carrier_id,
            'bounce_type' => $bounceType,
            'reason' => $reason,
        ]);

        $shipment->update([
            'carrier_id' => null,
        ]);

        event(new ShipmentCarrierBounced($shipment));


        return $bounce;
    }

    public function asController(ActionRequest $request, Shipment $shipment): CarrierBounce
    {
        return $this->handle(
            shipment: $shipment,
            bounceType: $request->validated('bounce_type'),
            reason: $request->validated('reason'),
        );
    }

    public function jsonResponse(CarrierBounce $bounce)
    {
        return CarrierBounceResource::make($bounce);
    }

    public function htmlResponse(CarrierBounce $bounce)
    {
        return redirect()->back()->with('success', 'Carrier bounced');
    }

    public function rules(): array
    {
        return [
            'bounce_type' => ['required', 'string'],
            'reason' => ['required', 'string'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::SHIPMENT_EDIT);
    }
}   
