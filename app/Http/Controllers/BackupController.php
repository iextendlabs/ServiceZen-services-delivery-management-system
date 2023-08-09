<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $backups = Storage::files('backups');

        return view('backups.index', compact('backups'));
    }

    public function backup()
    {
        Artisan::call('database:backup');

        return redirect()->route('backups.index')->with('success', 'Backup created successfully.');
    }

    public function download($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        
        return response()->download($path);
    }
}