<?php

namespace App\Providers;

use App\Events\Carriers\CarrierAssigned;
use App\Events\Carriers\CarrierCreated;
use App\Events\Carriers\CarrierStatusChanged;
use App\Events\Carriers\CarrierUnassigned;
use App\Events\Core\TmsEvent;
use App\Events\Shipments\ShipmentCreated;
use App\Events\Shipments\ShipmentDeleted;
use App\Events\Shipments\ShipmentRestored;
use App\Events\Shipments\ShipmentUpdated;
use App\Events\Shipments\ShipmentCarrierBounced;
use App\Events\Shipments\ShipmentStateChanged;
use App\Events\Shipments\ShipmentStateChangedTms;
use App\Listeners\Shipments\RelayShipmentStateChanged;
use App\Listeners\Events\AuditListener;
use App\Listeners\Events\MetricsListener;
use App\Listeners\Events\NotificationListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        // Shipment Events
        ShipmentCreated::class => [
            AuditListener::class,
            MetricsListener::class,
            NotificationListener::class,
        ],
        ShipmentUpdated::class => [
            AuditListener::class,
            MetricsListener::class,
        ],
        ShipmentDeleted::class => [
            AuditListener::class,
            MetricsListener::class,
        ],
        ShipmentRestored::class => [
            AuditListener::class,
            MetricsListener::class,
        ],

        // Shipment state changed (Spatie event relayed to TMS event)
        ShipmentStateChanged::class => [
            RelayShipmentStateChanged::class,
        ],
        ShipmentStateChangedTms::class => [
            AuditListener::class,
            MetricsListener::class,
            NotificationListener::class,
        ],
        
        // Carrier Events
        CarrierAssigned::class => [
            AuditListener::class,
            MetricsListener::class,
            NotificationListener::class,
        ],
        CarrierUnassigned::class => [
            AuditListener::class,
            MetricsListener::class,
            NotificationListener::class,
        ],
        CarrierCreated::class => [
            AuditListener::class,
            MetricsListener::class,
        ],
        CarrierStatusChanged::class => [
            AuditListener::class,
            MetricsListener::class,
            NotificationListener::class,
        ],
        ShipmentCarrierBounced::class => [
            AuditListener::class,
            MetricsListener::class,
            NotificationListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}