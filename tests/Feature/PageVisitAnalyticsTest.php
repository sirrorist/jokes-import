<?php

namespace Tests\Feature;

use App\Models\PageVisit;
use App\Services\Analytics\PageVisitRecorder;
use App\Services\Analytics\UniqueVisitService;
use App\Services\Analytics\VisitStatisticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PageVisitAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_visit_endpoint_persists_visit(): void
    {
        $response = $this->postJson('/api/analytics/visit', [
            'url' => 'https://example.com/page',
            'device_type' => 'desktop',
            'visitor_id' => 'visitor-test-1',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.is_unique_in_hour', true);

        $this->assertDatabaseHas('page_visits', [
            'url' => 'https://example.com/page',
            'device_type' => 'desktop',
        ]);
    }

    public function test_unique_visit_logic_counts_once_per_hour(): void
    {
        $visitorHash = hash('sha256', 'same-visitor');
        $hour = Carbon::parse('2026-05-19 14:30:00');

        PageVisit::factory()->create([
            'visitor_hash' => $visitorHash,
            'hour_bucket' => $hour->copy()->startOfHour(),
            'visited_at' => $hour,
            'is_unique_in_hour' => true,
        ]);

        $uniqueService = app(UniqueVisitService::class);

        $this->assertFalse(
            $uniqueService->isUniqueInHour($visitorHash, $hour->copy()->startOfHour())
        );
    }

    public function test_statistics_service_aggregates_unique_hourly_visits(): void
    {
        $hour = now()->startOfHour();

        PageVisit::factory()->create([
            'hour_bucket' => $hour,
            'visited_at' => $hour,
            'is_unique_in_hour' => true,
            'city' => 'Moscow',
        ]);

        PageVisit::factory()->create([
            'hour_bucket' => $hour,
            'visited_at' => $hour->copy()->addMinutes(10),
            'is_unique_in_hour' => false,
            'city' => 'Moscow',
        ]);

        $stats = app(VisitStatisticsService::class)->uniqueVisitsPerHour(1);
        $current = collect($stats)->last();

        $this->assertGreaterThanOrEqual(1, $current['unique_visits']);
    }

    public function test_second_visit_in_same_hour_is_not_unique(): void
    {
        $recorder = app(PageVisitRecorder::class);
        $request = request();
        $payload = [
            'url' => 'https://example.com/a',
            'visitor_id' => 'visitor-abc',
        ];

        $first = $recorder->recordFromRequest($request, $payload);
        $second = $recorder->recordFromRequest($request, $payload);

        $this->assertTrue($first['visit']->is_unique_in_hour);
        $this->assertFalse($second['visit']->is_unique_in_hour);
    }
}
