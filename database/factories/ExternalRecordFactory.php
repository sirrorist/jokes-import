<?php

namespace Database\Factories;

use App\Models\ExternalRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExternalRecord>
 */
class ExternalRecordFactory extends Factory
{
    protected $model = ExternalRecord::class;

    public function definition(): array
    {
        $externalId = (string) fake()->unique()->numberBetween(1, 999999);

        return [
            'source' => 'official-joke-api',
            'external_id' => $externalId,
            'record_hash' => hash('sha256', 'official-joke-api:'.$externalId),
            'type' => 'general',
            'setup' => fake()->sentence(),
            'punchline' => fake()->sentence(),
            'payload_json' => [
                'id' => (int) $externalId,
                'type' => 'general',
                'setup' => fake()->sentence(),
                'punchline' => fake()->sentence(),
            ],
        ];
    }
}
