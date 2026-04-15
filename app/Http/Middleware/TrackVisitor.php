<?php

namespace App\Http\Middleware;

use App\Actions\Visitor\TrackVisitorAction;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    public function __construct(
        private readonly TrackVisitorAction $trackVisitor,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldTrack($request, $response)) {
            return $response;
        }

        $this->trackVisitor->handle($request);

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('get')) {
            return false;
        }

        if ($request->expectsJson() || $request->ajax()) {
            return false;
        }

        if ($response->getStatusCode() >= 400) {
            return false;
        }

        if (
            $request->routeIs('admin.*') ||
            $request->is('admin') ||
            $request->is('admin/*')
        ) {
            return false;
        }

        $path = trim($request->path(), '/');

        if ($path === '') {
            return true;
        }

        if (
            $request->is('build/*') ||
            $request->is('storage/*') ||
            $request->is('favicon.ico') ||
            $request->is('robots.txt') ||
            $request->is('sitemap*') ||
            $request->is('up')
        ) {
            return false;
        }

        if (! Str::contains($path, '.')) {
            return true;
        }

        $assetExtensions = [
            'css',
            'js',
            'map',
            'png',
            'jpg',
            'jpeg',
            'gif',
            'svg',
            'webp',
            'ico',
            'txt',
            'xml',
            'json',
            'woff',
            'woff2',
            'ttf',
            'eot',
            'webmanifest',
        ];

        return ! in_array(Str::lower((string) Str::afterLast($path, '.')), $assetExtensions, true);
    }
}
