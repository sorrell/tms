<?php

namespace App\Actions\Shipments;

use App\Http\Resources\ShipmentCarrierRateResource;
use App\Models\Shipments\Accessorial;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\ShipmentCarrierRate;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SaveAccessorials 
{
    use AsAction;

    public function handle(Shipment $shipment, array $accessorials) : Collection 
    {
        // Extract IDs from the rates array
        $accessorialIds = collect($accessorials)
            ->pluck('id')
            ->filter()
            ->toArray();
        $toDelete = $shipment->accessorials()->whereNotIn('id', $accessorialIds)->get();
        // Delete rates that are no longer in the request
        foreach ($toDelete as $accessorial) {
            $accessorial->delete();
        }
        
        // Create or update rates
        $updatedRates = collect();
        foreach ($accessorials as $rateData) {

            $calculatedTotal = $rateData['quantity'] * $rateData['rate'];   
            // If there's a discrepancy, adjust the rate to match the total
            if (abs($calculatedTotal - $rateData['total']) > 0.01) {
                $rateData['rate'] = $rateData['total'] / $rateData['quantity'];
            }

            if (isset($rateData['id'])) {
                $rate = Accessorial::where('id', $rateData['id'])->where('shipment_id', $shipment->id)->first();
                if ($rate) {
                    $rate->update($rateData);
                    $updatedRates->push($rate->fresh());
                    continue;   // continue, we created and are g2g
                }
            }

            $rate = new Accessorial($rateData);
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
            $request->validated('accessorials', [])
        );
    }

    public function htmlResponse(Collection $rates)
    {
        return redirect()->back();
    }

    public function jsonResponse(Collection $rates)
    {
        return ShipmentCarrierRateResource::collection($rates);
    }

    public function rules() 
    {
        return [
            'accessorials.*.id' => 'nullable',
            'accessorials.*.pay_carrier' => 'required|boolean',
            'accessorials.*.invoice_customer' => 'required|boolean',
            'accessorials.*.rate' => 'required|numeric',
            'accessorials.*.quantity' => 'required|numeric',
            'accessorials.*.total' => 'required|numeric',
            'accessorials.*.customer_id' => 'required|exists:customers,id',
            'accessorials.*.carrier_id' => 'required|exists:carriers,id',
            'accessorials.*.accessorial_type_id' => 'required|exists:accessorial_types,id',
            'accessorials.*.currency_id' => 'required|exists:currencies,id'
        ];
    }
} 