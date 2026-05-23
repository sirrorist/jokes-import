<?php

namespace Tests\Feature;

use App\Models\ExternalRecord;
use App\Services\Jokes\JokeImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class JokeImportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_joke_from_api(): void
    {
        Http::fake([
            '*' => Http::response([
                'type' => 'general',
                'setup' => 'Why did the chicken cross the road?',
                'punchline' => 'To get to the other side.',
                'id' => 42,
            ], 200),
        ]);

        $record = app(JokeImportService::class)->import();

        $this->assertInstanceOf(ExternalRecord::class, $record);
        $this->assertDatabaseHas('external_records', [
            'external_id' => '42',
            'source' => 'official-joke-api',
        ]);
    }

    public function test_it_skips_duplicate_jokes(): void
    {
        Http::fake([
            '*' => Http::response([
                'type' => 'general',
                'setup' => 'Duplicate setup',
                'punchline' => 'Duplicate punchline',
                'id' => 99,
            ], 200),
        ]);

        $service = app(JokeImportService::class);

        $this->assertNotNull($service->import());
        $this->assertNull($service->import());
        $this->assertSame(1, ExternalRecord::query()->count());
    }

    public function test_it_handles_invalid_api_response(): void
    {
        Http::fake([
            '*' => Http::response('not-json', 500),
        ]);

        $record = app(JokeImportService::class)->import();

        $this->assertNull($record);
        $this->assertSame(0, ExternalRecord::query()->count());
    }
}
