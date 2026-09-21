<?php

namespace Database\Factories;

use App\Enums\PrayerRequestStatus;
use App\Models\PrayerRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PrayerRequest>
 */
class PrayerRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requester_name' => fake()->optional()->firstName(),
            'content' => fake()->paragraph(),
            'status' => PrayerRequestStatus::Pending,
            'ip_address' => fake()->ipv4(),
            'country_code' => 'AR',
            'country_name' => 'Argentina',
        ];
    }
}
