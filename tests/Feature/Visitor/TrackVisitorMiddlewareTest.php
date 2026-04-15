<?php

use App\Models\User;
use App\Models\VisitorLog;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware('web')->get('/test-pages/info', fn () => 'ok');
    Route::middleware('web')->get('/build/test.js', fn () => 'asset');
    Route::middleware('web')->post('/test-pages/submit', fn () => 'submitted');
});

test('public get request is tracked as visitor log', function () {
    $this->withHeader('referer', 'https://google.com')
        ->withHeader('user-agent', 'Pest Browser')
        ->get('/test-pages/info')
        ->assertOk();

    $log = VisitorLog::query()->first();

    expect($log)->not->toBeNull()
        ->and($log->path)->toBe('/test-pages/info')
        ->and($log->referrer)->toBe('https://google.com')
        ->and($log->user_agent)->toBe('Pest Browser')
        ->and($log->session_key)->not->toBeNull()
        ->and(strlen($log->ip_hash))->toBe(64)
        ->and($log->is_unique_daily)->toBeTrue();
});

test('repeat visit on same day is still tracked but no longer unique', function () {
    $this->get('/test-pages/info')->assertOk();
    $this->get('/test-pages/info')->assertOk();

    expect(VisitorLog::query()->count())->toBe(2)
        ->and(VisitorLog::query()->where('is_unique_daily', true)->count())->toBe(1)
        ->and(VisitorLog::query()->where('is_unique_daily', false)->count())->toBe(1);
});

test('non get requests are not tracked', function () {
    $this->post('/test-pages/submit')->assertOk();

    expect(VisitorLog::query()->count())->toBe(0);
});

test('admin traffic is not tracked', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertOk();

    expect(VisitorLog::query()->count())->toBe(0);
});

test('asset-like requests are not tracked', function () {
    $this->get('/build/test.js')->assertOk();

    expect(VisitorLog::query()->count())->toBe(0);
});
