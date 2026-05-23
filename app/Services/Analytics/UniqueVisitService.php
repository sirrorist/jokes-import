<?php

namespace App\Services\Analytics;

use App\Models\PageVisit;
use Carbon\CarbonInterface;

class UniqueVisitService
{
    public function hourBucket(CarbonInterface $visitedAt): CarbonInterface
    {
        return $visitedAt->copy()->startOfHour();
    }

    public function isUniqueInHour(string $visitorHash, CarbonInterface $hourBucket): bool
    {
        return !PageVisit::query()
            ->where('visitor_hash', $visitorHash)
            ->where('hour_bucket', $hourBucket)
            ->where('is_unique_in_hour', true)
            ->exists();
    }
}
