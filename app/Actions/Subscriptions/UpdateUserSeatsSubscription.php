<?php

namespace App\Actions\Subscriptions;

use App\Enums\Subscriptions\SubscriptionType;
use App\Models\Shipments\Shipment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Laravel\Cashier\Subscription;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateUserSeatsSubscription
{
    use AsAction;

    public function handle(
        int $quantity = 0
    )
    {
        $organization = current_organization();

        if($organization->subscribed(SubscriptionType::USER_SEAT)) {
            $organization
                ->subscription(SubscriptionType::USER_SEAT)
                ->updateQuantity($quantity);
        } else {
            // create new sub
            return $organization->newSubscription(
                SubscriptionType::USER_SEAT,
                config('subscriptions.user_seats.monthly_price_id')
            )
                ->trialDays(7)
                ->quantity($quantity, config('subscriptions.user_seats.monthly_price_id'))
                ->checkout();
        }
    }

    // public function asController(ActionRequest $request) : Shipment 
    // {
    //     return $this->handle(
    //         customerIds: $request->validated('customer_ids'),
    //         carrierId: $request->validated('carrier_id'),
    //         stops: $request->validated('stops'),
    //         weight: $request->validated('weight'),
    //         tripDistance: $request->validated('trip_distance'),
    //         trailerTypeId: $request->validated('trailer_type_id'),
    //         trailerSizeId: $request->validated('trailer_size_id'),
    //         trailerTemperatureRange: $request->validated('trailer_temperature_range'),
    //         trailerTemperature: $request->validated('trailer_temperature'),
    //         trailerTemperatureMaximum: $request->validated('trailer_temperature_maximum'),
    //         shipmentNumber: $request->validated('shipment_number'),
    //     );
    // }

    // public function htmlResponse(Shipment $shipment)
    // {
    //     return redirect()->route('shipments.show', $shipment);
    // }

    // public function jsonResponse(Shipment $shipment)
    // {
    //     return response()->json(ShipmentResource::make($shipment));
    // }

    public function rules()
    {
        return [
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return true; // todo check org billing perm
    }
}
