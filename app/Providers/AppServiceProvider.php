<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureRateLimiting();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('booking-submissions', function (Request $request): Limit {
            $maxAttempts = max(1, (int) config('booking.rate_limit.max_attempts', 5));
            $decaySeconds = max(60, (int) config('booking.rate_limit.decay_seconds', 600));

            return Limit::perMinutes((int) ceil($decaySeconds / 60), $maxAttempts)
                ->by($this->bookingRateLimitKey($request))
                ->response(function (Request $request, array $headers) {
                    $retryAfter = (int) ($headers['Retry-After'] ?? 0);
                    $message = (string) config('booking.messages.rate_limited');

                    if ($retryAfter > 0) {
                        $message = sprintf(
                            '%s Coba lagi dalam %s.',
                            $message,
                            $this->formatRetryAfter($retryAfter),
                        );
                    }

                    $response = redirect()
                        ->to($this->previousUrl($request))
                        ->withInput($request->except('_token'))
                        ->withErrors([
                            'booking' => $message,
                        ]);

                    foreach ($headers as $header => $value) {
                        $response->headers->set($header, is_array($value) ? implode(', ', $value) : $value);
                    }

                    return $response->setStatusCode(429);
                });
        });
    }

    private function bookingRateLimitKey(Request $request): string
    {
        return 'bookings:'.hash('sha256', implode('|', [
            (string) $request->ip(),
            (string) $request->userAgent(),
        ]));
    }

    private function previousUrl(Request $request): string
    {
        $previousUrl = url()->previous();

        if ($previousUrl === '' || $previousUrl === $request->fullUrl()) {
            return route('bookings.create');
        }

        return $previousUrl;
    }

    private function formatRetryAfter(int $seconds): string
    {
        $minutes = intdiv($seconds, 60);
        $remainingSeconds = $seconds % 60;

        if ($minutes > 0 && $remainingSeconds > 0) {
            return sprintf('%d menit %d detik', $minutes, $remainingSeconds);
        }

        if ($minutes > 0) {
            return sprintf('%d menit', $minutes);
        }

        return sprintf('%d detik', max(1, $remainingSeconds));
    }
}
