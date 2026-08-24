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
            Log::info('Nerdtech License Manager: Verifying license for domain', ['domain' => $request->getHost()]);

            $response = Http::withoutVerifying()->post($serverUrl . '/api/validate', [
                'domain' => $request->getHost(),
            ]);

            Log::info('Nerdtech License Manager: Received response from license server', [
                'status_code' => $response->status(),
                'body' => $response->json() ?? $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // We assume invalid by default if the API responds, and require explicit proof of validity
                $isValid = false;
                
                if (isset($data['valid']) && $data['valid'] === true) {
                    $isValid = true;
                }
                
                if (isset($data['status']) && strtolower($data['status']) === 'active') {
                    $isValid = true;
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
            Log::info('Nerdtech License Manager: Validation failed. Aborting request.');
            abort(403, 'License is invalid or deactivated. Please contact Nerdtech Labs.');
        }

        Log::info('Nerdtech License Manager: Validation passed. Allowing request.');
        return $next($request);
    }
}
