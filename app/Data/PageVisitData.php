<?php

namespace App\Data;

use Carbon\CarbonInterface;

readonly class PageVisitData
{
    public function __construct(
        public string $visitorHash,
        public string $ip,
        public ?string $city,
        public string $deviceType,
        public string $userAgent,
        public string $url,
        public CarbonInterface $visitedAt,
    ) {}
}
