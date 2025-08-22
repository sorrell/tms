<?php

namespace App\Listeners\Events;

use App\Events\Core\TmsEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MetricsListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(TmsEvent $event): void
    {
        $this->updateEventMetrics($event);
        $this->updateBusinessMetrics($event);
    }

    protected function updateEventMetrics(TmsEvent $event): void
    {
        // Increment event counter
        $eventCountKey = "metrics:events:{$event->organizationId}:{$event->getEventType()}:count";
        Cache::increment($eventCountKey);

        // Track event velocity (events per hour)
        $velocityKey = "metrics:events:{$event->organizationId}:{$event->getEventType()}:velocity:" . now()->format('Y-m-d-H');
        Cache::increment($velocityKey);
        Cache::expire($velocityKey, 86400); // Keep for 24 hours

        // Update daily metrics
        $this->updateDailyMetrics($event);
    }

    protected function updateBusinessMetrics(TmsEvent $event): void
    {
        switch ($event->getEventType()) {
            case 'shipment.created':
                $this->incrementShipmentMetrics($event);
                break;
                
            case 'shipment.state_changed':
                $this->updateShipmentStateMetrics($event);
                break;
                
            case 'carrier.assigned':
                $this->updateCarrierMetrics($event);
                break;
                
            case 'payable.created':
            case 'receivable.created':
                $this->updateFinancialMetrics($event);
                break;
        }
    }

    protected function updateDailyMetrics(TmsEvent $event): void
    {
        $date = now()->format('Y-m-d');
        $metricsKey = "metrics:daily:{$event->organizationId}:{$date}";
        
        // Use hash to store different metric types
        Cache::hIncrBy($metricsKey, 'total_events', 1);
        Cache::hIncrBy($metricsKey, $event->getEventType(), 1);
        Cache::expire($metricsKey, 604800); // Keep for 7 days
    }

    protected function incrementShipmentMetrics(TmsEvent $event): void
    {
        $data = $event->getEventData();
        $organizationId = $event->organizationId;
        
        // Increment shipment counters
        Cache::increment("metrics:shipments:{$organizationId}:total");
        Cache::increment("metrics:shipments:{$organizationId}:" . now()->format('Y-m'));
        
        // Track by carrier if assigned
        if (!empty($data['carrier_id'])) {
            Cache::increment("metrics:carriers:{$organizationId}:{$data['carrier_id']}:shipments");
        }
    }

    protected function updateShipmentStateMetrics(TmsEvent $event): void
    {
        $data = $event->getEventData();
        $organizationId = $event->organizationId;
        
        // Track state transitions
        if (isset($data['previous_state']) && isset($data['current_state'])) {
            $transitionKey = "metrics:shipments:{$organizationId}:transitions:{$data['previous_state']}:{$data['current_state']}";
            Cache::increment($transitionKey);
            
            // Calculate average time in state
            if ($data['current_state'] === 'delivered') {
                $this->calculateDeliveryMetrics($event);
            }
        }
    }

    protected function updateCarrierMetrics(TmsEvent $event): void
    {
        $data = $event->getEventData();
        $organizationId = $event->organizationId;
        
        Cache::increment("metrics:carriers:{$organizationId}:{$data['carrier_id']}:assignments");
        Cache::set("metrics:carriers:{$organizationId}:{$data['carrier_id']}:last_assignment", now());
    }

    protected function updateFinancialMetrics(TmsEvent $event): void
    {
        $data = $event->getEventData();
        $organizationId = $event->organizationId;
        $type = str_contains($event->getEventType(), 'payable') ? 'payables' : 'receivables';
        
        // Update financial metrics
        if (isset($data['amount'])) {
            $monthKey = "metrics:financial:{$organizationId}:{$type}:" . now()->format('Y-m');
            Cache::increment($monthKey . ':count');
            Cache::increment($monthKey . ':total', $data['amount']);
        }
    }

    protected function calculateDeliveryMetrics(TmsEvent $event): void
    {
        // Calculate delivery time metrics
        DB::table('shipment_delivery_metrics')->insert([
            'organization_id' => $event->organizationId,
            'shipment_id' => $event->getEventData()['shipment_id'],
            'delivered_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}