<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalRecord extends Model
{
    /** @use HasFactory<\Database\Factories\ExternalRecordFactory> */
    use HasFactory;

    protected $fillable = [
        'source',
        'external_id',
        'record_hash',
        'type',
        'setup',
        'punchline',
        'payload_json',
    ];

    protected function casts(): array
    {
        return [
            'payload_json' => 'array',
        ];
    }
}
