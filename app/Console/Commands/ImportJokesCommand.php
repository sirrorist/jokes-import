<?php

namespace App\Console\Commands;

use App\Services\Jokes\JokeImportService;
use Illuminate\Console\Command;

class ImportJokesCommand extends Command
{
    protected $signature = 'jokes:import';

    protected $description = 'Fetch a random joke from the public API and store it if it is not a duplicate';

    public function handle(JokeImportService $importService): int
    {
        $record = $importService->import();

        if ($record === null) {
            $this->info('Import finished: no new record stored (duplicate or invalid response).');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            'Imported joke #%s (external_id=%s).',
            $record->id,
            $record->external_id
        ));

        return self::SUCCESS;
    }
}
