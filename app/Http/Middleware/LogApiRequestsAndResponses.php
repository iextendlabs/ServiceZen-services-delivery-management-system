<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Log;
class LogApiRequestsAndResponses
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        $Logger = Log::channel('api');
        // Log the incoming request
        if (env('api_logs_enabled')) {

            $Logger->debug('Incoming API Request', [
                'method' => $request->method(),
                'uri' => $request->fullUrl(),
                'parameters' => $request->all(),
            ]);
        }

        // Handle the request and get the response
        $response = $next($request);
        if (env('api_logs_enabled')) {
        // Log the outgoing response
            $Logger->debug('Outgoing API Response', [
                'status' => $response->status(),
                'content' => $response->getContent(),
            ]);
        }

        return $response;
    }
}
