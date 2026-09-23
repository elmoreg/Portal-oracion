<?php

namespace Tests\Feature;

use App\Enums\PrayerRequestStatus;
use App\Enums\UserRole;
use App\Models\PrayerRequest;
use App\Models\User;
use App\Services\Assignment\AssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignPrayerRequestsTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    private function makeService(int $maxPerIntercessor = 10, int $batchSize = 100): AssignmentService
    {
        return new AssignmentService(
            maxPerIntercessor: $maxPerIntercessor,
            batchSize: $batchSize,
        );
    }

    private function pendingRequest(string $countryCode = 'AR'): PrayerRequest
    {
        return PrayerRequest::factory()->create([
            'status' => PrayerRequestStatus::Pending,
            'country_code' => $countryCode,
        ]);
    }

    // -----------------------------------------------------------------------
    // Basic assignment
    // -----------------------------------------------------------------------

    public function test_pending_unassigned_request_is_assigned_to_an_intercessor(): void
    {
        $intercessor = User::factory()->create(['role' => UserRole::Intercessor]);
        $request = $this->pendingRequest();

        $result = $this->makeService()->assign();

        $this->assertSame(1, $result->assigned);
        $this->assertSame(0, $result->unassignable);

        $request->refresh();
        $this->assertSame(PrayerRequestStatus::Assigned, $request->status);
        $this->assertTrue($request->isAssignedTo($intercessor));
    }

    public function test_already_assigned_request_is_not_reassigned(): void
    {
        $intercessor = User::factory()->create(['role' => UserRole::Intercessor]);
        $request = PrayerRequest::factory()->create(['status' => PrayerRequestStatus::Assigned]);
        $request->intercessors()->attach($intercessor->id, ['assigned_at' => now()]);

        $result = $this->makeService()->assign();

        $this->assertSame(0, $result->assigned);
        // The existing assignment is untouched.
        $this->assertSame(1, $request->intercessors()->count());
    }

    public function test_no_intercessors_available_makes_request_unassignable(): void
    {
        $this->pendingRequest();

        $result = $this->makeService()->assign();

        $this->assertSame(0, $result->assigned);
        $this->assertSame(1, $result->unassignable);
        $this->assertTrue($result->hasUnassignable());
    }

    // -----------------------------------------------------------------------
    // Zone affinity
    // -----------------------------------------------------------------------

    public function test_intercessor_from_same_country_is_preferred_over_one_from_different_country(): void
    {
        $argentinianIntercessor = User::factory()->fromCountry('AR')->create();
        $chileanIntercessor = User::factory()->fromCountry('CL')->create();
        $request = $this->pendingRequest('AR');

        $this->makeService()->assign();

        $request->refresh();
        $this->assertTrue($request->isAssignedTo($argentinianIntercessor));
        $this->assertFalse($request->isAssignedTo($chileanIntercessor));
    }

    public function test_fallback_to_different_country_when_no_local_intercessor_available(): void
    {
        $chileanIntercessor = User::factory()->fromCountry('CL')->create();
        $request = $this->pendingRequest('AR');

        $result = $this->makeService()->assign();

        $this->assertSame(1, $result->assigned);
        $request->refresh();
        $this->assertTrue($request->isAssignedTo($chileanIntercessor));
    }

    public function test_request_without_country_code_is_assigned_to_least_loaded_intercessor(): void
    {
        $intercessorA = User::factory()->create();
        $intercessorB = User::factory()->create();

        // Give intercessor A one existing active assignment to make B less loaded.
        $existing = PrayerRequest::factory()->create(['status' => PrayerRequestStatus::Assigned]);
        $existing->intercessors()->attach($intercessorA->id, ['assigned_at' => now()]);

        $request = PrayerRequest::factory()->create([
            'status' => PrayerRequestStatus::Pending,
            'country_code' => null,
        ]);

        $this->makeService()->assign();

        $request->refresh();
        $this->assertTrue($request->isAssignedTo($intercessorB));
    }

    // -----------------------------------------------------------------------
    // Load balancing
    // -----------------------------------------------------------------------

    public function test_least_loaded_intercessor_receives_the_next_assignment(): void
    {
        $overloaded = User::factory()->create();
        $free = User::factory()->create();

        // Give the first intercessor 3 active assignments.
        PrayerRequest::factory()->count(3)->create(['status' => PrayerRequestStatus::Assigned])
            ->each(fn ($r) => $r->intercessors()->attach($overloaded->id, ['assigned_at' => now()]));

        $request = $this->pendingRequest();

        $this->makeService()->assign();

        $request->refresh();
        $this->assertTrue($request->isAssignedTo($free));
    }

    public function test_intercessor_at_max_capacity_does_not_receive_new_assignments(): void
    {
        $fullIntercessor = User::factory()->create();

        // Saturate the intercessor to the limit.
        PrayerRequest::factory()->count(2)->create(['status' => PrayerRequestStatus::Assigned])
            ->each(fn ($r) => $r->intercessors()->attach($fullIntercessor->id, ['assigned_at' => now()]));

        $newRequest = $this->pendingRequest();

        $result = $this->makeService(maxPerIntercessor: 2)->assign();

        $this->assertSame(0, $result->assigned);
        $this->assertSame(1, $result->unassignable);
        $this->assertFalse($newRequest->fresh()->isAssignedTo($fullIntercessor));
    }

    public function test_load_is_balanced_across_intercessors_in_a_single_batch(): void
    {
        $intercessorA = User::factory()->create();
        $intercessorB = User::factory()->create();

        PrayerRequest::factory()->count(4)->create([
            'status' => PrayerRequestStatus::Pending,
            'country_code' => 'AR',
        ]);

        $this->makeService()->assign();

        $countA = $intercessorA->assignedPrayerRequests()->count();
        $countB = $intercessorB->assignedPrayerRequests()->count();

        // Both should have received 2 assignments.
        $this->assertSame(2, $countA);
        $this->assertSame(2, $countB);
    }

    // -----------------------------------------------------------------------
    // Inactive intercessors
    // -----------------------------------------------------------------------

    public function test_inactive_intercessor_does_not_receive_assignments(): void
    {
        User::factory()->inactive()->create();
        $request = $this->pendingRequest();

        $result = $this->makeService()->assign();

        $this->assertSame(0, $result->assigned);
        $this->assertSame(1, $result->unassignable);
        $request->refresh();
        $this->assertSame(PrayerRequestStatus::Pending, $request->status);
    }

    // -----------------------------------------------------------------------
    // Dry run
    // -----------------------------------------------------------------------

    public function test_dry_run_does_not_persist_any_changes(): void
    {
        User::factory()->create();
        $request = $this->pendingRequest();

        $result = $this->makeService()->assign(dryRun: true);

        $this->assertSame(1, $result->assigned);

        // Nothing in the DB should change.
        $request->refresh();
        $this->assertSame(PrayerRequestStatus::Pending, $request->status);
        $this->assertSame(0, $request->intercessors()->count());
    }

    // -----------------------------------------------------------------------
    // Artisan command
    // -----------------------------------------------------------------------

    public function test_artisan_command_runs_successfully(): void
    {
        User::factory()->create();
        $this->pendingRequest();

        $this->artisan('prayers:assign')
            ->assertSuccessful()
            ->expectsOutputToContain('Peticiones asignadas');
    }

    public function test_artisan_command_dry_run_shows_warning_and_makes_no_changes(): void
    {
        User::factory()->create();
        $request = $this->pendingRequest();

        $this->artisan('prayers:assign --dry-run')
            ->assertSuccessful()
            ->expectsOutputToContain('Modo simulación');

        $request->refresh();
        $this->assertSame(PrayerRequestStatus::Pending, $request->status);
    }

    // -----------------------------------------------------------------------
    // Batch size
    // -----------------------------------------------------------------------

    public function test_batch_size_limits_the_number_of_requests_processed_per_run(): void
    {
        User::factory()->count(10)->create();
        PrayerRequest::factory()->count(5)->create(['status' => PrayerRequestStatus::Pending]);

        $result = $this->makeService(batchSize: 3)->assign();

        $this->assertSame(3, $result->assigned);
        $this->assertSame(2, PrayerRequest::where('status', PrayerRequestStatus::Pending)->count());
    }
}
