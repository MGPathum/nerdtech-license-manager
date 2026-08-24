<?php

namespace Nerdtech\LicenseManager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VerifyLicense
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $domain = $request->getHost();
        $serverUrl = config('nerdtech-license.server_url');
        
        $isValid = true;

        try {
            $response = Http::post($serverUrl . '/api/verify-license', [
                'domain' => $domain,
            ]);

            if ($response->successful()) {
                if ($response->json('valid') === false) {
                    $isValid = false;
                }
            }
        } catch (\Exception $e) {
            // Try-catch fail-safe: allow access if the API throws a connection error.
            Log::warning('Nerdtech License Manager: Could not reach license server.', [
                'error' => $e->getMessage()
            ]);
        }

        if (!$isValid) {
            abort(403, 'YOUR SOFTWARE LICENSE HAS EXPIRED. PLEASE CONTACT NERDTECH-LABS.');
        }

        return $next($request);
    }
}
