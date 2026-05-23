<?php

namespace App\Services\Analytics;

use App\Data\PageVisitData;
use App\Models\PageVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageVisitRecorder
{
    public function __construct(
        private readonly GeoLocationService $geoLocationService,
        private readonly DeviceDetector $deviceDetector,
        private readonly UniqueVisitService $uniqueVisitService,
    ) {}

    /**
     * @param  array{url: string, device_type?: string|null, visited_at?: string|null, visitor_id?: string|null}  $validated
     * @return array{visit: PageVisit, visitor_id: string}
     */
    public function recordFromRequest(Request $request, array $validated): array
    {
        $visitor = $this->resolveVisitorIdentity(
            $request,
            isset($validated['visitor_id']) ? (string) $validated['visitor_id'] : null
        );
        $ip = $this->resolveClientIp($request);
        $userAgent = (string) $request->userAgent();
        $visitedAt = isset($validated['visited_at']) ? now()->parse($validated['visited_at']) : now();

        $visitData = new PageVisitData(
            visitorHash: $visitor['hash'],
            ip: $ip,
            city: $this->geoLocationService->resolveCity($ip),
            deviceType: $this->deviceDetector->detect(
                $userAgent,
                $validated['device_type'] ?? null
            ),
            userAgent: $userAgent,
            url: $validated['url'],
            visitedAt: $visitedAt,
        );

        return [
            'visit' => $this->record($visitData),
            'visitor_id' => $visitor['plain'],
        ];
    }

    public function record(PageVisitData $data): PageVisit
    {
        $hourBucket = $this->uniqueVisitService->hourBucket($data->visitedAt);
        $isUnique = $this->uniqueVisitService->isUniqueInHour($data->visitorHash, $hourBucket);

        return PageVisit::query()->create([
            'visitor_hash' => $data->visitorHash,
            'ip' => $data->ip,
            'city' => $data->city,
            'device_type' => $data->deviceType,
            'user_agent' => $data->userAgent,
            'url' => $data->url,
            'visited_at' => $data->visitedAt,
            'hour_bucket' => $hourBucket,
            'is_unique_in_hour' => $isUnique,
        ]);
    }

    /**
     * @return array{plain: string, hash: string}
     */
    public function resolveVisitorIdentity(Request $request, ?string $clientVisitorId = null): array
    {
        $plain = $clientVisitorId;

        if ($plain === null || $plain === '') {
            $cookieName = (string) config('analytics.visitor_cookie');
            $plain = $request->cookie($cookieName);
        }

        if (!is_string($plain) || $plain === '') {
            $plain = Str::uuid()->toString();
        }

        return [
            'plain' => $plain,
            'hash' => hash('sha256', $plain),
        ];
    }

    public function resolveClientIp(Request $request): string
    {
        return $request->ip() ?? '0.0.0.0';
    }
}
