<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Booking\GetBookingFooterLocationAction;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\VisitorLog;
use App\Support\Enums\BookingStatus;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(GetBookingFooterLocationAction $getBookingFooterLocation): Response
    {
        $stats = [
            'bookingsToday' => 0,
            'pendingBookings' => 0,
            'confirmedBookings' => 0,
            'completedBookings' => 0,
            'visitorsToday' => 0,
        ];

        $visitorTrend = collect(
            collect(CarbonPeriod::create(today()->subDays(6), today()))
                ->map(fn ($date): array => [
                    'date' => $date->format('Y-m-d'),
                    'totalVisits' => 0,
                    'uniqueVisits' => 0,
                ])
                ->all(),
        )->keyBy('date');

        if (Schema::hasTable('bookings')) {
            $stats['bookingsToday'] = Booking::query()->whereDate('created_at', today())->count();
            $stats['pendingBookings'] = Booking::query()->where('status', BookingStatus::Pending)->count();
            $stats['confirmedBookings'] = Booking::query()->where('status', BookingStatus::Confirmed)->count();
            $stats['completedBookings'] = Booking::query()->where('status', BookingStatus::Completed)->count();
        }

        if (Schema::hasTable('visitor_logs')) {
            $stats['visitorsToday'] = VisitorLog::query()->whereDate('visit_date', today())->count();

            VisitorLog::query()
                ->selectRaw('visit_date, count(*) as total_visits, sum(case when is_unique_daily = 1 then 1 else 0 end) as unique_visits')
                ->whereDate('visit_date', '>=', today()->subDays(6))
                ->groupBy('visit_date')
                ->orderBy('visit_date')
                ->get()
                ->each(function (VisitorLog $log) use ($visitorTrend): void {
                    $date = $log->visit_date?->format('Y-m-d');

                    if (! $date || ! $visitorTrend->has($date)) {
                        return;
                    }

                    $visitorTrend->put($date, [
                        'date' => $date,
                        'totalVisits' => (int) $log->getAttribute('total_visits'),
                        'uniqueVisits' => (int) $log->getAttribute('unique_visits'),
                    ]);
                });
        }

        return Inertia::render('admin/DashboardPage', [
            'stats' => $stats,
            'visitorTrend' => $visitorTrend->values()->all(),
            'footerLocation' => $getBookingFooterLocation->handle(),
            'foundationChecklist' => [
                'Database schema siap untuk booking, paket, dan visitor analytics.',
                'Auth admin sudah diprefix ke /admin.',
                'Layout public dan admin sudah dipisah untuk iterasi berikutnya.',
            ],
        ]);
    }
}
