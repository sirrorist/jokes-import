<?php

namespace App\Http\Controllers;

use App\Services\Analytics\VisitStatisticsService;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(VisitStatisticsService $statisticsService): View
    {
        return view('analytics.index', [
            'hourlyStats' => $statisticsService->uniqueVisitsPerHour(),
            'cityStats' => $statisticsService->visitsByCity(),
            'recentVisits' => $statisticsService->recentVisits(),
        ]);
    }
}
