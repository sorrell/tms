<?php

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
class CreateLocationa
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
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(array $shipperIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(array $shipperIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(array $shipperIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, array $shipperIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, array $shipperIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static dispatchSync(array $shipperIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static dispatchNow(array $shipperIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static dispatchAfterResponse(array $shipperIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \App\Models\Shipments\Shipment run(array $shipperIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?int $trailerSizeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
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
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment, array $shipperIds)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment, array $shipperIds)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment, array $shipperIds)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment, array $shipperIds)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment, array $shipperIds)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment, array $shipperIds)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment, array $shipperIds)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment, array $shipperIds)
 * @method static \App\Models\Shipments\Shipment run(\App\Models\Shipments\Shipment $shipment, array $shipperIds)
 */
class UpdateShipmentShippers
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