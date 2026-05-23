<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePageVisitRequest;
use App\Services\Analytics\PageVisitRecorder;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Cookie;

#[OA\Tag(name: 'Analytics', description: 'Page visit tracking')]
class PageVisitController extends Controller
{
    #[OA\Post(
        path: '/api/analytics/visit',
        description: 'Records a page visit. IP and city are resolved on the server.',
        summary: 'Track page visit',
        tags: ['Analytics'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['url'],
                properties: [
                    new OA\Property(property: 'url', type: 'string', example: 'https://example.com/page'),
                    new OA\Property(property: 'device_type', type: 'string', example: 'mobile'),
                    new OA\Property(property: 'visited_at', type: 'string', format: 'date-time'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Visit recorded'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(
        StorePageVisitRequest $request,
        PageVisitRecorder $recorder,
    ): JsonResponse {
        $result = $recorder->recordFromRequest($request, $request->validated());
        $visit = $result['visit'];

        $response = response()->json([
            'data' => [
                'id' => $visit->id,
                'visitor_id' => $result['visitor_id'],
                'is_unique_in_hour' => $visit->is_unique_in_hour,
                'city' => $visit->city,
                'device_type' => $visit->device_type,
            ],
        ], 201);

        if (!$request->hasCookie((string) config('analytics.visitor_cookie'))) {
            $response->withCookie($this->visitorCookie($result['visitor_id']));
        }

        return $response;
    }

    private function visitorCookie(string $visitorHash): Cookie
    {
        return cookie(
            name: (string) config('analytics.visitor_cookie'),
            value: $visitorHash,
            minutes: (int) config('analytics.visitor_cookie_days') * 24 * 60,
            path: '/',
            secure: app()->isProduction(),
            httpOnly: true,
            sameSite: 'lax',
        );
    }
}
