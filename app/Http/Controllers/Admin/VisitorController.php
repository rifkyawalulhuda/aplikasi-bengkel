<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisitorLog;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class VisitorController extends Controller
{
    public function index(): Response
    {
        $dailyAnalytics = collect(
            collect(CarbonPeriod::create(today()->subDays(13), today()))
                ->map(fn ($date): array => [
                    'date' => $date->format('Y-m-d'),
                    'totalVisits' => 0,
                    'uniqueVisitors' => 0,
                    'pageViews' => 0,
                ])
                ->all(),
        )->keyBy('date');

        $summary = [
            'todayTotalVisits' => 0,
            'todayUniqueVisitors' => 0,
            'pageViews' => 0,
            'trackedPaths' => 0,
        ];

        $topPaths = [];

        if (Schema::hasTable('visitor_logs')) {
            $summary['todayTotalVisits'] = VisitorLog::query()
                ->whereDate('visit_date', today())
                ->count();

            $summary['todayUniqueVisitors'] = VisitorLog::query()
                ->whereDate('visit_date', today())
                ->where('is_unique_daily', true)
                ->count();

            $summary['pageViews'] = VisitorLog::query()->count();
            $summary['trackedPaths'] = VisitorLog::query()->distinct('path')->count('path');

            VisitorLog::query()
                ->selectRaw('visit_date, count(*) as total_visits, sum(case when is_unique_daily = 1 then 1 else 0 end) as unique_visitors')
                ->whereDate('visit_date', '>=', today()->subDays(13))
                ->groupBy('visit_date')
                ->orderBy('visit_date')
                ->get()
                ->each(function (VisitorLog $log) use ($dailyAnalytics): void {
                    $date = $log->visit_date?->format('Y-m-d');

                    if (! $date || ! $dailyAnalytics->has($date)) {
                        return;
                    }

                    $dailyAnalytics->put($date, [
                        'date' => $date,
                        'totalVisits' => (int) $log->getAttribute('total_visits'),
                        'uniqueVisitors' => (int) $log->getAttribute('unique_visitors'),
                        'pageViews' => (int) $log->getAttribute('total_visits'),
                    ]);
                });

            $topPaths = VisitorLog::query()
                ->selectRaw('path, count(*) as total_views, sum(case when is_unique_daily = 1 then 1 else 0 end) as unique_visitors')
                ->groupBy('path')
                ->orderByDesc('total_views')
                ->orderBy('path')
                ->limit(10)
                ->get()
                ->map(fn (VisitorLog $log): array => [
                    'path' => $log->path,
                    'totalViews' => (int) $log->getAttribute('total_views'),
                    'uniqueVisitors' => (int) $log->getAttribute('unique_visitors'),
                ])
                ->all();
        }

        return Inertia::render('admin/VisitorsPage', [
            'summary' => $summary,
            'dailyAnalytics' => $dailyAnalytics->values()->all(),
            'topPaths' => $topPaths,
        ]);
    }
}
