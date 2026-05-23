<?php

namespace App\Services\Analytics;

use App\Models\PageVisit;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class VisitStatisticsService
{
    /**
     * @return array<int, array{hour: string, unique_visits: int}>
     */
    public function uniqueVisitsPerHour(int $hours = 24): array
    {
        $from = now()->subHours($hours)->startOfHour();

        $rows = PageVisit::query()
            ->selectRaw('hour_bucket, COUNT(*) as unique_visits')
            ->where('is_unique_in_hour', true)
            ->where('hour_bucket', '>=', $from)
            ->groupBy('hour_bucket')
            ->orderBy('hour_bucket')
            ->get();

        $indexed = $rows->keyBy(fn ($row) => Carbon::parse($row->hour_bucket)->format('Y-m-d H:00'));

        $result = [];

        for ($i = $hours; $i >= 0; $i--) {
            $hour = now()->subHours($i)->startOfHour();
            $key = $hour->format('Y-m-d H:00');
            $result[] = [
                'hour' => $hour->format('d.m H:00'),
                'unique_visits' => (int) ($indexed[$key]->unique_visits ?? 0),
            ];
        }

        return $result;
    }

    /**
     * @return array<int, array{city: string, count: int}>
     */
    public function visitsByCity(): array
    {
        return PageVisit::query()
            ->selectRaw("COALESCE(NULLIF(city, ''), 'Unknown') as city_label, COUNT(*) as total")
            ->where('is_unique_in_hour', true)
            ->groupBy('city_label')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'city' => (string) $row->city_label,
                'count' => (int) $row->total,
            ])
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, PageVisit>
     */
    public function recentVisits(int $limit = 20): Collection
    {
        return PageVisit::query()
            ->latest('visited_at')
            ->limit($limit)
            ->get();
    }
}
