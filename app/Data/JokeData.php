<?php

namespace App\Data;

readonly class JokeData
{
    public function __construct(
        public string $source,
        public string $externalId,
        public string $recordHash,
        public ?string $type,
        public ?string $setup,
        public ?string $punchline,
        /** @var array<string, mixed> */
        public array $payload,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromApiPayload(string $source, array $payload): self
    {
        $externalId = (string) ($payload['id'] ?? hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR)));

        return new self(
            source: $source,
            externalId: $externalId,
            recordHash: hash('sha256', $source.':'.$externalId),
            type: isset($payload['type']) ? (string) $payload['type'] : null,
            setup: isset($payload['setup']) ? (string) $payload['setup'] : null,
            punchline: isset($payload['punchline']) ? (string) $payload['punchline'] : null,
            payload: $payload,
        );
    }
}
