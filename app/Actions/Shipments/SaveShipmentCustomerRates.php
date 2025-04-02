<?php

namespace App\Actions\Shipments;

use App\Http\Resources\ShipmentCustomerRateResource;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\ShipmentCustomerRate;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SaveShipmentCustomerRates 
{
    use AsAction;

    public function handle(Shipment $shipment, array $rates) : Collection 
    {
        // Extract IDs from the rates array
        $rateIds = collect($rates)
            ->pluck('id')
            ->filter()
            ->toArray();
        $toDelete = $shipment->shipment_customer_rates()->whereNotIn('id', $rateIds)->get();
        // Delete rates that are no longer in the request
        foreach ($toDelete as $rate) {
            $rate->delete();
        }
        
        // Create or update rates
        $updatedRates = collect();
        foreach ($rates as $rateData) {

            $calculatedTotal = $rateData['quantity'] * $rateData['rate'];   
            // If there's a discrepancy, adjust the rate to match the total
            if (abs($calculatedTotal - $rateData['total']) > 0.01) {
                $rateData['rate'] = $rateData['total'] / $rateData['quantity'];
            }

            if (isset($rateData['id'])) {
                $rate = ShipmentCustomerRate::where('id', $rateData['id'])->where('shipment_id', $shipment->id)->first();
                if ($rate) {
                    $rate->update($rateData);
                    $updatedRates->push($rate->fresh());
                    continue;   // continue, we created and are g2g
                }
            }

            $rate = new ShipmentCustomerRate($rateData);
            $rate->shipment_id = $shipment->id;
            $rate->save();
            $updatedRates->push($rate);
        }
        
        return $updatedRates;

    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        return $this->handle(
            $shipment,
            $request->validated('rates', [])
        );
    }

    public function htmlResponse(Collection $rates)
    {
        return redirect()->back();
    }

    public function jsonResponse(Collection $rates)
    {
        return ShipmentCustomerRateResource::collection($rates);
    }

    public function rules() 
    {
        return [
            'rates.*.id' => 'nullable',
            'rates.*.rate' => 'required|numeric',
            'rates.*.quantity' => 'required|numeric',
            'rates.*.total' => 'required|numeric',
            'rates.*.customer_id' => 'required|exists:customers,id',
            'rates.*.customer_rate_type_id' => 'required|exists:customer_rate_types,id',
            'rates.*.currency_id' => 'required|exists:currencies,id'
        ];
    }


}