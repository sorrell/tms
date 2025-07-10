<?php

namespace App\Actions\Audit;

use App\Models\Carriers\Carrier;
use App\Models\Contact;
use App\Models\Customers\Customer;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetAuditLinkedData
{
    use AsAction;

    public function handle(string $type, int $id)
    {
        $model = $this->getModel($type, $id);
        
        if (!$model) {
            throw new \Exception('Record not found');
        }

        return [
            'id' => $model->id,
            'type' => $this->getDisplayType($type),
            'title' => $this->getDisplayTitle($model, $type),
            'data' => $this->getDisplayData($model, $type),
            'view_url' => $this->getViewUrl($model, $type),
        ];
    }

    public function asController(ActionRequest $request, string $type, int $id)
    {
        try {
            $data = $this->handle($type, $id);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function authorize(ActionRequest $request): bool
    {
        // Basic authorization - user must be logged in
        // Individual models can have more specific permissions if needed
        return $request->user() !== null;
    }

    private function getModel(string $type, int $id): ?Model
    {
        return match ($type) {
            'location' => Location::find($id),
            'contact' => Contact::find($id),
            'carrier' => Carrier::find($id),
            'customer' => Customer::find($id),
            'user' => User::find($id),
            default => null,
        };
    }

    private function getDisplayType(string $type): string
    {
        return match ($type) {
            'location' => 'Location',
            'contact' => 'Contact',
            'carrier' => 'Carrier',
            'customer' => 'Customer',
            'user' => 'User',
            default => ucfirst($type),
        };
    }

    private function getDisplayTitle(Model $model, string $type): string
    {
        return match ($type) {
            'location' => $this->formatLocationTitle($model),
            'contact' => $model->name ?? 'Contact #' . $model->id,
            'carrier' => $model->name ?? 'Carrier #' . $model->id,
            'customer' => $model->name ?? 'Customer #' . $model->id,
            'user' => $model->name ?? 'User #' . $model->id,
            default => ucfirst($type) . ' #' . $model->id,
        };
    }

    private function formatLocationTitle(Model $location): string
    {
        $parts = array_filter([
            $location->name,
            $location->address_line_1,
            $location->address_city,
            $location->address_state,
        ]);

        return implode(', ', $parts) ?: 'Location #' . $location->id;
    }

    private function getDisplayData(Model $model, string $type): array
    {
        return match ($type) {
            'location' => $this->getLocationData($model),
            'contact' => $this->getContactData($model),
            'carrier' => $this->getCarrierData($model),
            'customer' => $this->getCustomerData($model),
            'user' => $this->getUserData($model),
            default => $model->toArray(),
        };
    }

    private function getLocationData(Model $location): array
    {
        return [
            'name' => $location->name,
            'address_line_1' => $location->address_line_1,
            'address_line_2' => $location->address_line_2,
            'city' => $location->address_city,
            'state' => $location->address_state,
            'zipcode' => $location->address_zipcode,
            'country' => $location->address_country,
            'phone' => $location->phone,
            'created_at' => $location->created_at?->format('M j, Y g:i A'),
        ];
    }

    private function getContactData(Model $contact): array
    {
        return [
            'name' => $contact->name,
            'email' => $contact->email,
            'phone' => $contact->phone,
            'title' => $contact->title,
            'notes' => $contact->notes,
            'created_at' => $contact->created_at?->format('M j, Y g:i A'),
        ];
    }

    private function getCarrierData(Model $carrier): array
    {
        return [
            'name' => $carrier->name,
            'mc_number' => $carrier->mc_number,
            'dot_number' => $carrier->dot_number,
            'contact_email' => $carrier->contact_email,
            'contact_phone' => $carrier->contact_phone,
            'billing_email' => $carrier->billing_email,
            'billing_phone' => $carrier->billing_phone,
            'created_at' => $carrier->created_at?->format('M j, Y g:i A'),
        ];
    }

    private function getCustomerData(Model $customer): array
    {
        return [
            'name' => $customer->name,
            'dba_name' => $customer->dba_name,
            'net_pay_days' => $customer->net_pay_days,
            'invoice_number_schema' => $customer->invoice_number_schema,
            'created_at' => $customer->created_at?->format('M j, Y g:i A'),
        ];
    }

    private function getUserData(Model $user): array
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at?->format('M j, Y g:i A'),
        ];
    }

    private function getViewUrl(Model $model, string $type): ?string
    {
        try {
            return match ($type) {
                'carrier' => route('carriers.show', $model->id),
                'customer' => route('customers.show', $model->id),
                'location' => null, // Locations don't have individual view pages
                'contact' => null, // Contacts don't have individual view pages
                'user' => null, // Users don't have individual view pages typically
                default => null,
            };
        } catch (\Exception $e) {
            return null;
        }
    }
}