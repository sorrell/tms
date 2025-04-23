<?php

namespace App\Actions\CheckCalls;

use App\Enums\Permission;
use App\Models\CheckCalls\CheckCall;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\ShipmentStop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCheckCall
{
    use AsAction;

    public function handle(
        int $shipmentId,
        ?int $stopId = null,
        ?string $eta = null,
        ?float $reportedTrailerTemp = null,
        ?string $contactName = null,
        ?string $contactMethod = null,
        ?string $contactMethodDetail = null,
        ?bool $isLate = null,
        ?array $details = null,
    ): CheckCall
    {
        $shipment = Shipment::findOrFail($shipmentId);
        $carrierId = $shipment->carrier_id;
        
        // Use current stop if none provided
        if (!$stopId && $shipment->getCurrentStopAttribute()) {
            $stopId = $shipment->getCurrentStopAttribute()->id;
        }
        
        $checkCall = CheckCall::create([
            'organization_id' => Auth::user()->current_organization_id,
            'carrier_id' => $carrierId,
            'shipment_id' => $shipmentId,
            'stop_id' => $stopId,
            'eta' => $eta,
            'reported_trailer_temp' => $reportedTrailerTemp,
            'contact_name' => $contactName,
            'contact_method' => $contactMethod,
            'contact_method_detail' => $contactMethodDetail,
            'is_late' => $isLate,
            'details' => $details,
        ]);
        
        // TODO - update fields from details
        
        return $checkCall;
    }

    public function asController(ActionRequest $request, Shipment $shipment): CheckCall
    {
        $validatedData = $request->validated();
        
        return $this->handle(
            $shipment->id,
            $validatedData['stop_id'] ?? null,
            $validatedData['eta'] ?? null,
            $validatedData['reported_trailer_temp'] ?? null,
            $validatedData['contact_name'] ?? null,
            $validatedData['contact_method'] ?? null,
            $validatedData['contact_method_detail'] ?? null,
            $validatedData['is_late'] ?? null,
            $validatedData['details'] ?? null,
        );
    }

    public function rules(): array
    {
        return [
            'stop_id' => ['nullable', 'exists:shipment_stops,id'],
            'eta' => ['nullable', 'datetime'],
            'reported_trailer_temp' => ['nullable', 'numeric'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_method' => ['nullable', 'string', 'max:255'],
            'contact_method_detail' => ['nullable', 'string', 'max:255'],
            'is_late' => ['nullable', 'boolean'],
            'details' => ['nullable', 'array'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return Auth::user()->can(Permission::CHECK_CALL_EDIT);
    }

    public function jsonResponse(CheckCall $checkCall)
    {
        return response()->json([
            'message' => 'Check call created successfully',
            'check_call' => new \App\Http\Resources\CheckCallResource($checkCall),
        ]);
    }

    public function htmlResponse(CheckCall $checkCall)
    {
        return redirect()->back()->with('success', 'Check call created successfully');
    }
} 