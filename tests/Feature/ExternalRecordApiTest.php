<?php

namespace Tests\Feature;

use App\Models\ExternalRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExternalRecordApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_jokes_endpoint_returns_paginated_json(): void
    {
        ExternalRecord::factory()->count(3)->create();

        $response = $this->getJson('/api/jokes?per_page=2');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'source', 'external_id', 'setup', 'punchline', 'payload'],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(2, 'data');
    }

    public function test_jokes_endpoint_supports_limit_and_offset(): void
    {
        ExternalRecord::factory()->count(5)->create();

        $response = $this->getJson('/api/jokes?limit=2&offset=1');

        $response->assertOk()
            ->assertJsonPath('meta.limit', 2)
            ->assertJsonPath('meta.offset', 1)
            ->assertJsonCount(2, 'data');
    }
}
