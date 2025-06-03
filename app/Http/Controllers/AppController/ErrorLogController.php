<?php

namespace App\Http\Controllers\AppController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ErrorLogController extends Controller
{
    public function store(Request $request)
    {
        $logData = [
            'timestamp' => $request->input('timestamp', now()->toISOString()),
            'error_name' => $request->input('errorName', 'unnamed_error'),
            'error' => $request->input('error'),
            'stack_trace' => $request->input('stackTrace'),
            'additional_data' => json_encode($request->input('additionalData', [])),
            'device_info' => json_encode($request->input('deviceInfo', [])),
        ];

        // Format the log message
        $logMessage = sprintf(
            "[%s] %s: %s\nStack Trace: %s\nAdditional Data: %s\nDevice Info: %s\n",
            $logData['timestamp'],
            $logData['error_name'],
            $logData['error'],
            $logData['stack_trace'] ?? 'none',
            $logData['additional_data'],
            $logData['device_info']
        );

        // Log to the custom file
        Log::channel('app_errors')->error($logMessage);

        return response()->json(['success' => true]);
    }
}