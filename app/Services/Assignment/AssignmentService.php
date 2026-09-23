<?php

namespace App\Services\Assignment;

use App\Enums\PrayerRequestStatus;
use App\Models\PrayerRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AssignmentService
{
    /**
     * @param  int  $maxPerIntercessor  Maximum number of active assignments per intercessor.
     * @param  int  $batchSize  Maximum number of pending requests to process in one run.
     */
    public function __construct(
        private readonly int $maxPerIntercessor,
        private readonly int $batchSize,
    ) {}

    /**
     * Assign unassigned pending prayer requests to available intercessors.
     *
     * Assignment strategy:
     *   1. Prefer intercessors from the same country as the request.
     *   2. Among candidates of the same zone, pick the one with the least active assignments.
     *   3. If no intercessor in the same zone is available, fall back to the globally least-loaded one.
     *   4. Skip if all intercessors are at the max-load limit.
     */
    public function assign(bool $dryRun = false): AssignmentResult
    {
        $pendingRequests = $this->pendingUnassignedRequests();

        if ($pendingRequests->isEmpty()) {
            return new AssignmentResult(assigned: 0, skipped: 0, unassignable: 0);
        }

        /** @var Collection<int, User> $intercessors */
        $intercessors = User::intercessors()->get();

        if ($intercessors->isEmpty()) {
            return new AssignmentResult(assigned: 0, skipped: 0, unassignable: $pendingRequests->count());
        }

        // Build an in-memory load map: userId => current active assignment count.
        /** @var array<int, int> $loadMap */
        $loadMap = $intercessors->mapWithKeys(
            fn (User $user) => [$user->id => $user->activeAssignmentsCount()]
        )->all();

        $assigned = 0;
        $skipped = 0;
        $unassignable = 0;

        foreach ($pendingRequests as $request) {
            $intercessor = $this->pickIntercessor($intercessors, $loadMap, $request->country_code);

            if ($intercessor === null) {
                $unassignable++;

                continue;
            }

            if (! $dryRun) {
                DB::transaction(function () use ($request, $intercessor): void {
                    $request->intercessors()->syncWithoutDetaching([
                        $intercessor->id => ['assigned_at' => now()],
                    ]);

                    $request->update(['status' => PrayerRequestStatus::Assigned]);
                });
            }

            // Keep the in-memory load map in sync so subsequent picks in the same
            // batch account for assignments made earlier in the same run.
            $loadMap[$intercessor->id]++;
            $assigned++;
        }

        return new AssignmentResult(assigned: $assigned, skipped: $skipped, unassignable: $unassignable);
    }

    /**
     * @return Collection<int, PrayerRequest>
     */
    private function pendingUnassignedRequests(): Collection
    {
        return PrayerRequest::query()
            ->where('status', PrayerRequestStatus::Pending)
            ->whereDoesntHave('intercessors')
            ->orderBy('created_at')
            ->limit($this->batchSize)
            ->get();
    }

    /**
     * Pick the best available intercessor for a request, considering zone affinity
     * and current workload.
     *
     * @param  Collection<int, User>  $intercessors
     * @param  array<int, int>  $loadMap
     */
    private function pickIntercessor(Collection $intercessors, array $loadMap, ?string $countryCode): ?User
    {
        // Only consider intercessors that have not reached the load limit.
        $available = $intercessors->filter(
            fn (User $user) => $loadMap[$user->id] < $this->maxPerIntercessor
        );

        if ($available->isEmpty()) {
            return null;
        }

        // Prefer intercessors from the same country, if we have a country code.
        if ($countryCode !== null) {
            $zoneMatch = $available->filter(
                fn (User $user) => $user->country_code === $countryCode
            );

            if ($zoneMatch->isNotEmpty()) {
                return $this->leastLoaded($zoneMatch, $loadMap);
            }
        }

        // Fallback: least-loaded intercessor regardless of zone.
        return $this->leastLoaded($available, $loadMap);
    }

    /**
     * Return the intercessor with the lowest active assignment count.
     *
     * @param  Collection<int, User>  $intercessors
     * @param  array<int, int>  $loadMap
     */
    private function leastLoaded(Collection $intercessors, array $loadMap): User
    {
        return $intercessors->sortBy(fn (User $user) => $loadMap[$user->id])->first();
    }
}
