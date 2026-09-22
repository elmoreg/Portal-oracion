<?php

namespace Tests;

use App\Jobs\TranslatePrayerContent;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Queue;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Content-translation jobs are dispatched whenever prayer content is
        // created. Fake the queue so tests never reach the external translation
        // provider on model creation; tests covering translation drive the
        // service or accessor directly with their own Http::fake().
        Queue::fake([TranslatePrayerContent::class]);
    }
}
