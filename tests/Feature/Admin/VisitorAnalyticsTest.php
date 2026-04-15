<?php

use App\Models\User;
use App\Models\VisitorLog;
use Inertia\Testing\AssertableInertia as Assert;

function createVisitorLogRecord(array $overrides = []): VisitorLog
{
    static $sequence = 1;

    $visitorLog = VisitorLog::query()->create(array_merge([
        'visit_date' => today()->subDays($sequence % 3),
        'session_key' => 'session-'.$sequence,
        'ip_hash' => hash('sha256', 'ip-'.$sequence),
        'path' => $sequence % 2 === 0 ? '/' : '/booking/success',
        'referrer' => 'https://example.com',
        'user_agent' => 'Pest Browser '.$sequence,
        'is_unique_daily' => true,
    ], $overrides));

    $sequence++;

    return $visitorLog;
}

test('guests are redirected from visitor analytics page', function () {
    $this->get(route('admin.visitors.index'))
        ->assertRedirect(route('login'));
});

test('authenticated admin can view visitor analytics summary and top paths', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    createVisitorLogRecord([
        'visit_date' => today(),
        'session_key' => 'today-a',
        'ip_hash' => hash('sha256', 'today-a'),
        'path' => '/',
        'is_unique_daily' => true,
    ]);

    createVisitorLogRecord([
        'visit_date' => today(),
        'session_key' => 'today-a',
        'ip_hash' => hash('sha256', 'today-a'),
        'path' => '/booking/success',
        'is_unique_daily' => false,
    ]);

    createVisitorLogRecord([
        'visit_date' => today()->subDay(),
        'session_key' => 'yesterday-a',
        'ip_hash' => hash('sha256', 'yesterday-a'),
        'path' => '/',
        'is_unique_daily' => true,
    ]);

    createVisitorLogRecord([
        'visit_date' => today()->subDay(),
        'session_key' => 'yesterday-b',
        'ip_hash' => hash('sha256', 'yesterday-b'),
        'path' => '/',
        'is_unique_daily' => true,
    ]);

    $this->get(route('admin.visitors.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/VisitorsPage')
            ->where('summary.todayTotalVisits', 2)
            ->where('summary.todayUniqueVisitors', 1)
            ->where('summary.pageViews', 4)
            ->where('summary.trackedPaths', 2)
            ->has('dailyAnalytics', 14)
            ->where('topPaths.0.path', '/')
            ->where('topPaths.0.totalViews', 3)
            ->where('topPaths.1.path', '/booking/success')
            ->where('topPaths.1.totalViews', 1));
});
