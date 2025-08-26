<?php
// app/Http/Middleware/CheckBlockedIp.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BlockedIp; // Add this import
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CheckBlockedIp
{
    
    public function handle(Request $request, Closure $next): Response
    {
            // dd('Middleware is running!');

        $blockedIp = BlockedIp::where('ip_address', $request->ip())->first();
    // dd('Checking IP:', $request->ip(), 'Result:', $blockedIp);

        if ($blockedIp) {
            throw new AccessDeniedHttpException('Access from your IP address has been blocked for security reasons.');
        }

        return $next($request);
    }
}
