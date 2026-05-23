<?php

use App\Http\Controllers\Api\ExternalRecordController;
use App\Http\Controllers\Api\PageVisitController;
use Illuminate\Support\Facades\Route;

Route::get('/jokes', [ExternalRecordController::class, 'index'])->name('api.jokes.index');

Route::post('/analytics/visit', [PageVisitController::class, 'store'])
    ->middleware('throttle:'.config('analytics.rate_limit'))
    ->name('api.analytics.visit');
