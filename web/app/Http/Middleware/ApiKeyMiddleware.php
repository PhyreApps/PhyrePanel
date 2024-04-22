<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-Api-Key');
        $apiSecret = $request->header('X-Api-Secret');
        $ipAddress = $request->ip();
        $authorized = false;

        $findApiKey = ApiKey::where('api_key', $apiKey)->where('api_secret', $apiSecret)->first();
        if ($findApiKey) {
            if ($findApiKey->enable_whitelisted_ips) {
                if (in_array($ipAddress, explode(',', $findApiKey->whitelisted_ips))) {
                    $authorized = true;
                }
            } else {
                $authorized = true;
            }
        }

        if (!$authorized) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
