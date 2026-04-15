<?php

namespace App\Actions\Visitor;

use App\Models\VisitorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TrackVisitorAction
{
    public function handle(Request $request): void
    {
        if (! Schema::hasTable('visitor_logs')) {
            return;
        }

        $sessionKey = $request->hasSession() ? $request->session()->getId() : null;
        $ipHash = hash('sha256', sprintf(
            '%s|%s',
            (string) $request->ip(),
            (string) config('app.key'),
        ));
        $visitDate = today();
        $path = '/'.ltrim($request->path(), '/');
        $isUniqueDaily = ! VisitorLog::query()
            ->whereDate('visit_date', $visitDate)
            ->where(function ($query) use ($sessionKey, $ipHash): void {
                if ($sessionKey) {
                    $query->where('session_key', $sessionKey);
                }

                $query->orWhere('ip_hash', $ipHash);
            })
            ->exists();

        VisitorLog::create([
            'visit_date' => $visitDate,
            'ip_hash' => $ipHash,
            'session_key' => $sessionKey,
            'path' => $path,
            'referrer' => $request->headers->get('referer'),
            'user_agent' => $request->userAgent(),
            'is_unique_daily' => $isUniqueDaily,
        ]);
    }
}
