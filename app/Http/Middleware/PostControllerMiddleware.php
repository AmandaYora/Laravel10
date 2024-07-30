<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Result;

class PostControllerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Base URL from the environment
        $allowedDomain = env('APP_URL', 'http://localhost');
        $origin = $request->header('Origin');
        
        $result = new Result();
        $result->code = Result::CODE_ERROR;
        $result->info = 'Error';
        $result->message = 'Forbidden: API not published';

        // Check if the request comes from the same domain or if CORS has been published
        if ($origin !== $allowedDomain && !app()->bound('cors_published')) {
            return response()->json($result, 403);
        }

        // If CORS is published or the request is from the same domain, set headers
        $response->headers->set('Access-Control-Allow-Origin', $origin ? $origin : '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        return $response;
    }
}
