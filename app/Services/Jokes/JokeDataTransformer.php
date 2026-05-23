<?php

namespace App\Services\Jokes;

use App\Data\JokeData;
use InvalidArgumentException;

class JokeDataTransformer
{
    /**
     * @return array<string, mixed>
     */
    public function toAttributes(JokeData $data): array
    {
        return [
            'source' => $data->source,
            'external_id' => $data->externalId,
            'record_hash' => $data->recordHash,
            'type' => $data->type,
            'setup' => $data->setup,
            'punchline' => $data->punchline,
            'payload_json' => $data->payload,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function fromApiPayload(string $source, array $payload): JokeData
    {
        if ($payload === []) {
            throw new InvalidArgumentException('Joke API returned an empty payload.');
        }

        if (! isset($payload['id']) && (! isset($payload['setup']) || ! isset($payload['punchline']))) {
            throw new InvalidArgumentException('Joke API payload is missing required fields.');
        }

        return JokeData::fromApiPayload($source, $payload);
    }
}
