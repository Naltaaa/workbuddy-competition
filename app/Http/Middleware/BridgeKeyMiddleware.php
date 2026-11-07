<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BridgeKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('app.bridge_key');
        $provided = $request->header('X-Bridge-Key');

        if (empty($expected) || ! hash_equals($expected, (string) $provided)) {
            return response()->json(
                ['ok' => false, 'reason' => 'auth'],
                401,
                ['Cache-Control' => 'no-store']
            );
        }

        return $next($request);
    }
}