<?php

return [
    'source' => 'official-joke-api',
    'api_url' => env('JOKE_API_URL', 'https://official-joke-api.appspot.com/random_joke'),
    'timeout' => (int) env('JOKE_API_TIMEOUT', 10),
    'connect_timeout' => (int) env('JOKE_API_CONNECT_TIMEOUT', 5),
    'retry_times' => (int) env('JOKE_API_RETRY_TIMES', 2),
    'retry_sleep_ms' => (int) env('JOKE_API_RETRY_SLEEP_MS', 200),
];
