<?php

namespace App\Actions\Accounting;

use App\Http\Resources\Accounting\ReceivableResource;
use App\Models\Accounting\Receivable;
use App\Models\Shipments\Shipment;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SaveReceivables 
{
    use AsAction;

    public function handle(Shipment $shipment, array $receivables) : Collection 
    {
        // Extract IDs from the rates array
        $receivableIds = collect($receivables)
            ->pluck('id')
            ->filter()
            ->toArray();
        $toDelete = $shipment->receivables()->whereNotIn('id', $receivableIds)->get();
        // Delete rates that are no longer in the request
        foreach ($toDelete as $receivable) {
            $receivable->delete();
        }
        
        // Create or update rates
        $updatedReceivables = collect();
        foreach ($receivables as $receivableData) {

            $calculatedTotal = $receivableData['quantity'] * $receivableData['rate'];   
            // If there's a discrepancy, adjust the rate to match the total
            if (abs($calculatedTotal - $receivableData['total']) > 0.01) {
                $receivableData['rate'] = $receivableData['total'] / $receivableData['quantity'];
            }

            if (isset($receivableData['id'])) {
                $receivable = Receivable::where('id', $receivableData['id'])->where('shipment_id', $shipment->id)->first();
                if ($receivable) {
                    $receivable->update($receivableData);
                    $updatedReceivables->push($receivable->fresh());
                    continue;   // continue, we created and are g2g
                }
            }

            $receivable = new Receivable($receivableData);
            $receivable->shipment_id = $shipment->id;
            $receivable->save();
            $updatedReceivables->push($receivable);
        }
        
        return $updatedReceivables;

    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        return $this->handle(
            $shipment,
            $request->validated('receivables', [])
        );
    }

    public function htmlResponse(Collection $receivables)
    {
        return redirect()->back();
    }

    public function jsonResponse(Collection $receivables)
    {
        return ReceivableResource::collection($receivables);
    }

    public function rules() 
    {
        return [
            'receivables.*.id' => 'nullable',
            'receivables.*.payer_id' => 'required',
            'receivables.*.payer_type' => 'required|string',
            'receivables.*' => 'required|exists:receivables,id,payer_type,payer_id',
            'receivables.*.rate' => 'required|numeric',
            'receivables.*.quantity' => 'required|numeric',
            'receivables.*.total' => 'required|numeric',
            'receivables.*.rate_type_id' => 'required|exists:rate_types,id',
            'receivables.*.currency_id' => 'required|exists:currencies,id'
        ];
    }
} 