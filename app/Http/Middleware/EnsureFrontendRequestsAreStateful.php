<?php

namespace Laravel\Sanctum\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFrontendRequestsAreStateful
{
    public function handle(Request $request, Closure $next): Response
    {
        $stateful = config('sanctum.stateful', []);

        if ($this->fromFrontend($request, $stateful)) {
            config(['session.driver' => 'cookie']);
        }

        return $next($request);
    }

    protected function fromFrontend(Request $request, array $stateful): bool
    {
        $referer = $request->headers->get('referer');
        $origin = $request->headers->get('origin');

        foreach (array_filter([$referer, $origin]) as $url) {
            foreach ($stateful as $domain) {
                if (str_contains($url, $domain)) {
                    return true;
                }
            }
        }

        return false;
    }
}
