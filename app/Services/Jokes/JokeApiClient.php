<?php

namespace App\Services\Jokes;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use JsonException;

class JokeApiClient
{
    /**
     * @return array<string, mixed>
     */
    public function fetchRandomJoke(): array
    {
        $url = (string) config('jokes.api_url');
        $timeout = (int) config('jokes.timeout');
        $connectTimeout = (int) config('jokes.connect_timeout');
        $retryTimes = (int) config('jokes.retry_times');
        $retrySleepMs = (int) config('jokes.retry_sleep_ms');

        try {
            $response = Http::timeout($timeout)
                ->connectTimeout($connectTimeout)
                ->retry($retryTimes, $retrySleepMs, throw: false)
                ->acceptJson()
                ->get($url);

            if (!$response->successful()) {
                throw new InvalidArgumentException(
                    sprintf('Joke API responded with HTTP %d.', $response->status())
                );
            }

            $payload = $response->json();

            if (!is_array($payload)) {
                throw new InvalidArgumentException('Joke API returned a non-object JSON response.');
            }

            return $payload;
        } catch (ConnectionException|RequestException $exception) {
            Log::error('Joke API request failed.', [
                'url' => $url,
                'message' => $exception->getMessage(),
            ]);

            throw new InvalidArgumentException('Joke API request failed: '.$exception->getMessage(), 0, $exception);
        } catch (JsonException $exception) {
            Log::error('Joke API returned invalid JSON.', [
                'url' => $url,
                'message' => $exception->getMessage(),
            ]);

            throw new InvalidArgumentException('Joke API returned invalid JSON.', 0, $exception);
        }
    }
}
