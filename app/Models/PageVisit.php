<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    /** @use HasFactory<\Database\Factories\PageVisitFactory> */
    use HasFactory;

    protected $fillable = [
        'visitor_hash',
        'ip',
        'city',
        'device_type',
        'user_agent',
        'url',
        'visited_at',
        'hour_bucket',
        'is_unique_in_hour',
    ];

    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
            'hour_bucket' => 'datetime',
            'is_unique_in_hour' => 'boolean',
        ];
    }
}
