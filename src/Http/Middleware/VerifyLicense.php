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
        $serverUrl = 'https://nerdtechlabs.info';
        
        $isValid = true;

        try {
            $response = Http::post($serverUrl . '/api/validate', [
                'domain' => $request->getHost(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['valid']) && $data['valid'] === false) {
                    $isValid = false;
                }
                
                if (isset($data['status']) && strtolower($data['status']) === 'deactivated') {
                    $isValid = false;
                }
            } else {
                // If the server returns a 4xx or 5xx status (like 403 Forbidden or 400 Bad Request)
                // that means the license validation actually failed, not a connection issue.
                $isValid = false;
            }
        } catch (\Exception $e) {
            // Try-catch fail-safe: allow access only if the API throws a connection error.
            Log::warning('Nerdtech License Manager: Could not reach license server.', [
                'error' => $e->getMessage()
            ]);
        }

        if (!$isValid) {
            abort(403, 'License is invalid or deactivated. Please contact Nerdtech Labs.');
        }

        return $next($request);
    }
}
