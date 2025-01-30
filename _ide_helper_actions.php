<?php

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
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(array $shipperIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(array $shipperIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(array $shipperIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, array $shipperIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, array $shipperIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static dispatchSync(array $shipperIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static dispatchNow(array $shipperIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static dispatchAfterResponse(array $shipperIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
 * @method static \App\Models\Shipments\Shipment run(array $shipperIds, int $carrierId, array $stops, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?bool $trailerTemperatureRange = false, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?string $shipmentNumber = null)
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
 * @method static \Lorisleiva\Actions\Decorators\JobDecorator|\Lorisleiva\Actions\Decorators\UniqueJobDecorator makeJob(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = null)
 * @method static \Lorisleiva\Actions\Decorators\UniqueJobDecorator makeUniqueJob(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch dispatch(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchIf(bool $boolean, \App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Support\Fluent dispatchUnless(bool $boolean, \App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = null)
 * @method static dispatchSync(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = null)
 * @method static dispatchNow(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = null)
 * @method static dispatchAfterResponse(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = null)
 * @method static \App\Models\Shipments\Shipment run(\App\Models\Shipments\Shipment $shipment, ?float $weight = null, ?float $tripDistance = null, ?int $trailerTypeId = null, ?float $trailerTemperature = null, ?float $trailerTemperatureMaximum = null, ?bool $trailerTemperatureRange = null)
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