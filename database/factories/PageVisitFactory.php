<?php

namespace Database\Factories;

use App\Models\PageVisit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PageVisit>
 */
class PageVisitFactory extends Factory
{
    protected $model = PageVisit::class;

    public function definition(): array
    {
        $visitedAt = fake()->dateTimeBetween('-2 days', 'now');
        $hourBucket = (clone $visitedAt)->setTime((int) $visitedAt->format('H'), 0);

        return [
            'visitor_hash' => hash('sha256', fake()->uuid()),
            'ip' => fake()->ipv4(),
            'city' => fake()->city(),
            'device_type' => fake()->randomElement(['desktop', 'mobile', 'tablet']),
            'user_agent' => fake()->userAgent(),
            'url' => fake()->url(),
            'visited_at' => $visitedAt,
            'hour_bucket' => $hourBucket,
            'is_unique_in_hour' => true,
        ];
    }
}
