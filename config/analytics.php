<?php

return [
    'visitor_cookie' => env('ANALYTICS_VISITOR_COOKIE', 'amo_visitor_id'),
    'visitor_cookie_days' => (int) env('ANALYTICS_VISITOR_COOKIE_DAYS', 365),
    'geo_api_url' => env('ANALYTICS_GEO_API_URL', 'http://ip-api.com/json/{ip}'),
    'geo_timeout' => (int) env('ANALYTICS_GEO_TIMEOUT', 5),
    'rate_limit' => env('ANALYTICS_RATE_LIMIT', '120,1'),
];
