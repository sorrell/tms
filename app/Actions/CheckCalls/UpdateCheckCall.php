<?php

namespace App\Actions\CheckCalls;

use App\Enums\Permission;
use App\Models\CheckCalls\CheckCall;
use App\Models\Shipments\ShipmentStop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateCheckCall
{
    use AsAction;

    public function handle(
        CheckCall $checkCall,
        ?int $stopId = null,
        ?string $eta = null,
        ?float $reportedTrailerTemp = null,
        ?string $contactName = null,
        ?string $contactMethod = null,
        ?string $contactMethodDetail = null,
        ?string $arrivedAt = null,
        ?string $leftAt = null,
        ?string $loadedUnloadedAt = null,
    ): CheckCall
    {
        $data = [
            'stop_id' => $stopId,
            'eta' => $eta,
            'reported_trailer_temp' => $reportedTrailerTemp,
            'contact_name' => $contactName,
            'contact_method' => $contactMethod,
            'contact_method_detail' => $contactMethodDetail,
            'arrived_at' => $arrivedAt,
            'left_at' => $leftAt,
            'loaded_unloaded_at' => $loadedUnloadedAt,
        ];
        
        // Remove null values to avoid overwriting existing data
        $data = array_filter($data, fn($value) => $value !== null);
        
        $checkCall->update($data);
        
        // Update the stop with the check call information if a stop is associated
        if ($stopId) {
            $stop = ShipmentStop::find($stopId);
            if ($stop) {
                $updateData = [];
                
                if ($arrivedAt) {
                    $updateData['arrived_at'] = $arrivedAt;
                }
                
                if ($leftAt) {
                    $updateData['left_at'] = $leftAt;
                }
                
                if ($loadedUnloadedAt) {
                    $updateData['loaded_unloaded_at'] = $loadedUnloadedAt;
                }
                
                if (!empty($updateData)) {
                    $stop->update($updateData);
                }
            }
        }
        
        return $checkCall;
    }

    public function asController(ActionRequest $request, CheckCall $checkCall): CheckCall
    {
        $validatedData = $request->validated();
        
        return $this->handle(
            $checkCall,
            $validatedData['stop_id'] ?? null,
            $validatedData['eta'] ?? null,
            $validatedData['reported_trailer_temp'] ?? null,
            $validatedData['contact_name'] ?? null,
            $validatedData['contact_method'] ?? null,
            $validatedData['contact_method_detail'] ?? null,
            $validatedData['arrived_at'] ?? null,
            $validatedData['left_at'] ?? null,
            $validatedData['loaded_unloaded_at'] ?? null,
        );
    }

    public function rules(): array
    {
        return [
            'stop_id' => ['nullable', 'exists:shipment_stops,id'],
            'eta' => ['nullable', 'date'],
            'reported_trailer_temp' => ['nullable', 'numeric'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_method' => ['nullable', 'string', 'max:255'],
            'contact_method_detail' => ['nullable', 'string', 'max:255'],
            'arrived_at' => ['nullable', 'date'],
            'left_at' => ['nullable', 'date'],
            'loaded_unloaded_at' => ['nullable', 'date'],
        ];
    }

    public function authorize(ActionRequest $request, CheckCall $checkCall): bool
    {
        return Auth::user()->can(Permission::CHECK_CALL_EDIT);
    }

    public function jsonResponse(CheckCall $checkCall)
    {
        return response()->json([
            'message' => 'Check call updated successfully',
            'check_call' => new \App\Http\Resources\CheckCallResource($checkCall),
        ]);
    }

    public function htmlResponse(CheckCall $checkCall)
    {
        return redirect()->back()->with('success', 'Check call updated successfully');
    }
} 