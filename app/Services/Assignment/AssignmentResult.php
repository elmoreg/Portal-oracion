<?php

namespace App\Services\Assignment;

/**
 * Value object returned by AssignmentService::assign().
 *
 * @phpstan-type AssignmentResultArray array{assigned: int, skipped: int, unassignable: int}
 */
final readonly class AssignmentResult
{
    public function __construct(
        /** Number of prayer requests that were successfully assigned. */
        public int $assigned,
        /** Number of prayer requests skipped (e.g. already assigned mid-batch). */
        public int $skipped,
        /** Number of prayer requests that could not be assigned (no available intercessor). */
        public int $unassignable,
    ) {}

    public function total(): int
    {
        return $this->assigned + $this->skipped + $this->unassignable;
    }

    public function hasUnassignable(): bool
    {
        return $this->unassignable > 0;
    }
}
