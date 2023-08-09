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
        $directory = 'backups';

        $files = Storage::allFiles($directory);
        
        $filesWithLastModified = [];
        
        foreach ($files as $file) {
            $lastModified = Storage::lastModified($file);
            $filesWithLastModified[$file] = $lastModified;
        }
        
        // Sort the files by their last modified timestamps
        arsort($filesWithLastModified);
        
        $backups = [];
        
        foreach ($filesWithLastModified as $file => $lastModified) {
            $formattedLastModified = date('Y-m-d H:i:s', $lastModified);
            $backups[] = ([
                'file' => $file,
                'modified' => $formattedLastModified
            ]);
        }
        
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
    
    public function delete($filename)
    {
        if (Storage::exists('backups/' . $filename)) {
            Storage::delete('backups/' . $filename);
            return redirect()->route('backups.index')->with('success', 'File deleted successfully.');
        } else {
           return redirect()->route('backups.index')->with('success', 'File not found.');
        }
    }
    
    public function clear() {
        $directoryToDelete = 'backups';

        $files = Storage::files($directoryToDelete);
        
        if ($files) {
            foreach ($files as $file) {
                Storage::delete($file);
            }
            return redirect()->route('backups.index')->with('success', 'All backup files in the directory have been deleted.');
        } else {
            return redirect()->route('backups.index')->with('success', 'File not found.');
        }
    }
}