<?php

use App\Http\Controllers\Admin\BookingManagementController;
use App\Http\Controllers\Admin\CustomServiceItemController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ServicePackageController;
use App\Http\Controllers\Admin\VisitorController;
use App\Http\Controllers\Public\BookingController;
use App\Http\Controllers\Public\LandingPageController;
use Illuminate\Support\Facades\Route;

Route::redirect('/login', '/admin/login');
Route::redirect('/dashboard', '/admin/dashboard')->name('dashboard');

Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::get('/booking', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/bookings', [BookingController::class, 'store'])
    ->middleware('throttle:booking-submissions')
    ->name('bookings.store');
Route::get('/booking/success', [BookingController::class, 'success'])->name('bookings.success');
Route::get('/booking/{booking:booking_code}', [BookingController::class, 'show'])->name('bookings.public.show');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::redirect('/', '/admin/dashboard');

    Route::middleware('auth')->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::prefix('bookings')->name('bookings.')->controller(BookingManagementController::class)->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/{booking:booking_code}', 'show')->name('show');
            Route::patch('/{booking:booking_code}/status', 'updateStatus')->name('update-status');
            Route::patch('/{booking:booking_code}/notes', 'updateNotes')->name('update-notes');
        });

        Route::prefix('service-packages')->name('service-packages.')->controller(ServicePackageController::class)->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
        Route::get('/{servicePackage}/edit', 'edit')->name('edit');
        Route::patch('/{servicePackage}', 'update')->name('update');
        Route::patch('/{servicePackage}/activate', 'activate')->name('activate');
        Route::patch('/{servicePackage}/deactivate', 'deactivate')->name('deactivate');
        Route::delete('/{servicePackage}', 'destroy')->name('destroy');
    });

        Route::prefix('custom-service-items')->name('custom-service-items.')->controller(CustomServiceItemController::class)->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/{customServiceItem}/edit', 'edit')->name('edit');
            Route::patch('/{customServiceItem}', 'update')->name('update');
            Route::patch('/{customServiceItem}/deactivate', 'deactivate')->name('deactivate');
            Route::delete('/{customServiceItem}', 'destroy')->name('destroy');
        });

        Route::get('/visitors', [VisitorController::class, 'index'])->name('visitors.index');
    });
});

require __DIR__.'/settings.php';
