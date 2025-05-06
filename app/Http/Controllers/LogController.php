<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class LogController extends Controller
{
    public function view($file)
    {
        $validFiles = ['laravel', 'app_error', 'order_request'];
        
        if (!in_array($file, $validFiles)) {
            abort(404);
        }

        $logPath = storage_path("logs/{$file}.log");

        if (File::exists($logPath)) {
            $logContent = File::get($logPath);
        } else {
            $logContent = 'Log file does not exist.';
        }

        return view('logs.show', [
            'logContent' => $logContent,
            'currentFile' => $file
        ]);
    }

    public function clear($file)
    {
        $validFiles = ['laravel', 'app_error', 'order_request'];
        
        if (!in_array($file, $validFiles)) {
            abort(404);
        }

        $logPath = storage_path("logs/{$file}.log");

        if (File::exists($logPath)) {
            File::put($logPath, '');
            return redirect()->route('logs.view', ['file' => $file])
                ->with('success', 'Log file emptied successfully.');
        }

        return redirect()->route('logs.view', ['file' => $file])
            ->with('error', 'Log file not found.');
    }
}