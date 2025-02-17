<?php

namespace App\Actions\Contacts;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $name, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension, \Illuminate\Database\Eloquent\Model $contactFor)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $name, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension, \Illuminate\Database\Eloquent\Model $contactFor)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $name, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension, \Illuminate\Database\Eloquent\Model $contactFor)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $name, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension, \Illuminate\Database\Eloquent\Model $contactFor)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $name, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension, \Illuminate\Database\Eloquent\Model $contactFor)
 * @method static dispatchSync(string $name, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension, \Illuminate\Database\Eloquent\Model $contactFor)
 * @method static dispatchNow(string $name, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension, \Illuminate\Database\Eloquent\Model $contactFor)
 * @method static dispatchAfterResponse(string $name, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension, \Illuminate\Database\Eloquent\Model $contactFor)
 * @method static \App\Models\Contact run(string $name, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension, \Illuminate\Database\Eloquent\Model $contactFor)
 */
class CreateContact
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Contact $contact)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Contact $contact)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Contact $contact)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Contact $contact)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Contact $contact)
 * @method static dispatchSync(\App\Models\Contact $contact)
 * @method static dispatchNow(\App\Models\Contact $contact)
 * @method static dispatchAfterResponse(\App\Models\Contact $contact)
 * @method static \App\Models\Contact run(\App\Models\Contact $contact)
 */
class DeleteContact
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Contact $contact, string $name, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Contact $contact, string $name, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Contact $contact, string $name, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Contact $contact, string $name, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Contact $contact, string $name, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension)
 * @method static dispatchSync(\App\Models\Contact $contact, string $name, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension)
 * @method static dispatchNow(\App\Models\Contact $contact, string $name, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension)
 * @method static dispatchAfterResponse(\App\Models\Contact $contact, string $name, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension)
 * @method static \App\Models\Contact run(\App\Models\Contact $contact, string $name, ?string $title, ?string $email, ?string $mobile_phone, ?string $office_phone, ?string $office_phone_extension)
 */
class UpdateContact
{
}
namespace App\Actions\Customers;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $name)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $name)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $name)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $name)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $name)
 * @method static dispatchSync(string $name)
 * @method static dispatchNow(string $name)
 * @method static dispatchAfterResponse(string $name)
 * @method static \App\Models\Customers\Customer run(string $name)
 */
class CreateCustomer
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(int $customer_id, int $facility_id)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(int $customer_id, int $facility_id)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(int $customer_id, int $facility_id)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, int $customer_id, int $facility_id)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, int $customer_id, int $facility_id)
 * @method static dispatchSync(int $customer_id, int $facility_id)
 * @method static dispatchNow(int $customer_id, int $facility_id)
 * @method static dispatchAfterResponse(int $customer_id, int $facility_id)
 * @method static \App\Models\Facility run(int $customer_id, int $facility_id)
 */
class CreateCustomerFacility
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(int $customerId, int $facilityId)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(int $customerId, int $facilityId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(int $customerId, int $facilityId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, int $customerId, int $facilityId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, int $customerId, int $facilityId)
 * @method static dispatchSync(int $customerId, int $facilityId)
 * @method static dispatchNow(int $customerId, int $facilityId)
 * @method static dispatchAfterResponse(int $customerId, int $facilityId)
 * @method static \App\Models\Facility run(int $customerId, int $facilityId)
 */
class DeleteCustomerFacility
{
}
namespace App\Actions\Facilities;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $name, int $location_id)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $name, int $location_id)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $name, int $location_id)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $name, int $location_id)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $name, int $location_id)
 * @method static dispatchSync(string $name, int $location_id)
 * @method static dispatchNow(string $name, int $location_id)
 * @method static dispatchAfterResponse(string $name, int $location_id)
 * @method static \App\Models\Facility run(string $name, int $location_id)
 */
class CreateFacility
{
}
namespace App\Actions\Locations;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $address_line_1, string $address_city, string $address_state, string $address_zipcode, ?string $name = null, ?string $address_line_2 = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $address_line_1, string $address_city, string $address_state, string $address_zipcode, ?string $name = null, ?string $address_line_2 = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $address_line_1, string $address_city, string $address_state, string $address_zipcode, ?string $name = null, ?string $address_line_2 = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $address_line_1, string $address_city, string $address_state, string $address_zipcode, ?string $name = null, ?string $address_line_2 = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $address_line_1, string $address_city, string $address_state, string $address_zipcode, ?string $name = null, ?string $address_line_2 = null)
 * @method static dispatchSync(string $address_line_1, string $address_city, string $address_state, string $address_zipcode, ?string $name = null, ?string $address_line_2 = null)
 * @method static dispatchNow(string $address_line_1, string $address_city, string $address_state, string $address_zipcode, ?string $name = null, ?string $address_line_2 = null)
 * @method static dispatchAfterResponse(string $address_line_1, string $address_city, string $address_state, string $address_zipcode, ?string $name = null, ?string $address_line_2 = null)
 * @method static \App\Models\Location run(string $address_line_1, string $address_city, string $address_state, string $address_zipcode, ?string $name = null, ?string $address_line_2 = null)
 */
class CreateLocation
{
}
namespace App\Actions\Notes;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $note, string $notableType, int $notableId, ?int $userId = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $note, string $notableType, int $notableId, ?int $userId = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $note, string $notableType, int $notableId, ?int $userId = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $note, string $notableType, int $notableId, ?int $userId = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $note, string $notableType, int $notableId, ?int $userId = null)
 * @method static dispatchSync(string $note, string $notableType, int $notableId, ?int $userId = null)
 * @method static dispatchNow(string $note, string $notableType, int $notableId, ?int $userId = null)
 * @method static dispatchAfterResponse(string $note, string $notableType, int $notableId, ?int $userId = null)
 * @method static \App\Models\Note run(string $note, string $notableType, int $notableId, ?int $userId = null)
 */
class CreateNote
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $notableType, int $notableId)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $notableType, int $notableId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $notableType, int $notableId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $notableType, int $notableId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $notableType, int $notableId)
 * @method static dispatchSync(string $notableType, int $notableId)
 * @method static dispatchNow(string $notableType, int $notableId)
 * @method static dispatchAfterResponse(string $notableType, int $notableId)
 * @method static \Illuminate\Database\Eloquent\Collection run(string $notableType, int $notableId)
 */
class GetNotes
{
}
namespace App\Actions\Organizations;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(string $email, \App\Models\Organizations\Organization $organization)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(string $email, \App\Models\Organizations\Organization $organization)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(string $email, \App\Models\Organizations\Organization $organization)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, string $email, \App\Models\Organizations\Organization $organization)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, string $email, \App\Models\Organizations\Organization $organization)
 * @method static dispatchSync(string $email, \App\Models\Organizations\Organization $organization)
 * @method static dispatchNow(string $email, \App\Models\Organizations\Organization $organization)
 * @method static dispatchAfterResponse(string $email, \App\Models\Organizations\Organization $organization)
 * @method static \App\Models\Organizations\OrganizationInvite run(string $email, \App\Models\Organizations\Organization $organization)
 */
class SendInvite
{
}
namespace App\Actions\Shipments;

/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(array $customerIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(array $customerIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(array $customerIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, array $customerIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, array $customerIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static dispatchSync(array $customerIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static dispatchNow(array $customerIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static dispatchAfterResponse(array $customerIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \App\Models\Shipments\Shipment run(array $customerIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 */
class CreateShipment
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment, int $carrierId)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment, int $carrierId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment, int $carrierId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment, int $carrierId)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment, int $carrierId)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment, int $carrierId)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment, int $carrierId)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment, int $carrierId)
 * @method static \App\Models\Shipments\Shipment run(\App\Models\Shipments\Shipment $shipment, int $carrierId)
 */
class UpdateShipmentCarrierDetails
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment, array $customerIds)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment, array $customerIds)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment, array $customerIds)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment, array $customerIds)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment, array $customerIds)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment, array $customerIds)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment, array $customerIds)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment, array $customerIds)
 * @method static \App\Models\Shipments\Shipment run(\App\Models\Shipments\Shipment $shipment, array $customerIds)
 */
class UpdateShipmentCustomers
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = null)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = null)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = null)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = null)
 * @method static \App\Models\Shipments\Shipment run(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = null)
 */
class UpdateShipmentGeneral
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment, ?string $shipmentNumber = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment, ?string $shipmentNumber = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment, ?string $shipmentNumber = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment, ?string $shipmentNumber = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment, ?string $shipmentNumber = null)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment, ?string $shipmentNumber = null)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment, ?string $shipmentNumber = null)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment, ?string $shipmentNumber = null)
 * @method static \App\Models\Shipments\Shipment run(\App\Models\Shipments\Shipment $shipment, ?string $shipmentNumber = null)
 */
class UpdateShipmentNumber
{
}
/**
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment, array $stops)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment, array $stops)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment, array $stops)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment, array $stops)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment, array $stops)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment, array $stops)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment, array $stops)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment, array $stops)
 * @method static \App\Models\Shipments\Shipment run(\App\Models\Shipments\Shipment $shipment, array $stops)
 */
class UpdateShipmentStops
{
}
namespace Lorisleiva\Actions\Concerns;

/**
 * @method void asController()
 */
trait AsController
{
}
/**
 * @method void asListener()
 */
trait AsListener
{
}
/**
 * @method void asJob()
 */
trait AsJob
{
}
/**
 * @method void asCommand(\Illuminate\Console\Command $command)
 */
trait AsCommand
{
}