<?php

namespace App\Services\Jokes;

use App\Models\ExternalRecord;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

class JokeImportService
{
    public function __construct(
        private readonly JokeApiClient $apiClient,
        private readonly JokeDataTransformer $transformer,
    ) {}

    public function import(): ?ExternalRecord
    {
        $source = (string) config('jokes.source');

        try {
            $payload = $this->apiClient->fetchRandomJoke();
            $jokeData = $this->transformer->fromApiPayload($source, $payload);

            $existingRecord = ExternalRecord::query()
                ->where('source', $jokeData->source)
                ->where('external_id', $jokeData->externalId)
                ->first();

            if ($existingRecord !== null) {
                Log::info('Skipped duplicate joke import.', [
                    'source' => $jokeData->source,
                    'external_id' => $jokeData->externalId,
                ]);

                return null;
            }

            return ExternalRecord::query()->create(
                $this->transformer->toAttributes($jokeData)
            );
        } catch (InvalidArgumentException $exception) {
            Log::warning('Joke import skipped due to invalid API response.', [
                'message' => $exception->getMessage(),
            ]);

            return null;
        } catch (Throwable $exception) {
            Log::error('Joke import failed.', [
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
