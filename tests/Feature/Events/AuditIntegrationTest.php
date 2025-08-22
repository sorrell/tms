<?php

namespace Tests\Feature\Events;

use App\Actions\Carriers\CreateCarrier;
use App\Events\Carriers\CarrierCreated;
use App\Models\Organizations\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use OwenIt\Auditing\Models\Audit;
use Tests\TestCase;

class AuditIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->organization = Organization::factory()->create();
        $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
        $this->actingAs($this->user);
    }

    public function test_events_are_stored_as_custom_audit_entries()
    {
        // Create a carrier which triggers CarrierCreated event
        $carrier = CreateCarrier::run('Test Audit Carrier');

        // Wait a moment for async processing
        sleep(1);

        // Check that an audit entry was created for the TMS event
        $auditEntry = Audit::where('event', 'carrier.created')
            ->where('tags', 'like', '%tms-event%')
            ->where('auditable_type', get_class($carrier))
            ->where('auditable_id', $carrier->id)
            ->first();

        $this->assertNotNull($auditEntry, 'TMS event should be stored as audit entry');
        $this->assertEquals('carrier.created', $auditEntry->event);
        $this->assertEquals($this->user->id, $auditEntry->user_id);
        $this->assertEquals(get_class($this->user), $auditEntry->user_type);
        $this->assertStringContainsString('tms-event', $auditEntry->tags);
        $this->assertStringContainsString('carrier', $auditEntry->tags);
    }

    public function test_audit_entry_contains_event_metadata()
    {
        // Create a carrier
        $carrier = CreateCarrier::run('Metadata Test Carrier');

        // Wait for processing
        sleep(1);

        // Get the audit entry
        $auditEntry = Audit::where('event', 'carrier.created')
            ->where('auditable_id', $carrier->id)
            ->first();

        $this->assertNotNull($auditEntry);
        
        // Check that event metadata is stored in new_values
        $newValues = $auditEntry->new_values;
        $this->assertArrayHasKey('event_id', $newValues);
        $this->assertArrayHasKey('organization_id', $newValues);
        $this->assertArrayHasKey('occurred_at', $newValues);
        $this->assertArrayHasKey('metadata', $newValues);
        $this->assertArrayHasKey('carrier_id', $newValues);
        $this->assertArrayHasKey('carrier_name', $newValues);
        
        // Verify organization context
        $this->assertEquals($this->organization->id, $newValues['organization_id']);
        $this->assertEquals($carrier->id, $newValues['carrier_id']);
        $this->assertEquals('Metadata Test Carrier', $newValues['carrier_name']);
    }

    public function test_events_are_tagged_correctly()
    {
        $carrier = CreateCarrier::run('Tag Test Carrier');

        sleep(1);

        $auditEntry = Audit::where('event', 'carrier.created')
            ->where('auditable_id', $carrier->id)
            ->first();

        $this->assertNotNull($auditEntry);
        $this->assertStringContainsString('tms-event', $auditEntry->tags);
        $this->assertStringContainsString('carrier', $auditEntry->tags);
    }

    public function test_events_can_be_queried_alongside_regular_audits()
    {
        // Create a carrier (which creates both regular audit and TMS event audit)
        $carrier = CreateCarrier::run('Query Test Carrier');

        sleep(1);

        // Get all audits for this carrier
        $allAudits = Audit::where('auditable_type', get_class($carrier))
            ->where('auditable_id', $carrier->id)
            ->get();

        // Should have at least 2: one for the model creation, one for the TMS event
        $this->assertGreaterThanOrEqual(2, $allAudits->count());

        // Check for regular model audit
        $modelAudit = $allAudits->where('event', 'created')->first();
        $this->assertNotNull($modelAudit);

        // Check for TMS event audit
        $eventAudit = $allAudits->where('event', 'carrier.created')->first();
        $this->assertNotNull($eventAudit);
        $this->assertStringContainsString('tms-event', $eventAudit->tags);
    }
}