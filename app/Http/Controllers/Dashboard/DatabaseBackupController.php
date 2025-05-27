<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Artisan;

class DatabaseBackupController extends Controller
{    
    public function __construct()
    {
        // Create POS directory if it doesn't exist
        if (!File::exists(storage_path('app/POS'))) {
            File::makeDirectory(storage_path('app/POS'), 0755, true);
        }
    }

    public function index()
    {
        return view('database.index', [
            'files' => File::exists(storage_path('app/POS')) ? File::allFiles(storage_path('app/POS')) : []
        ]);
    }

    // Backup database is not working, and you need to enter manually in terminal with command php artisan backup:run.
    public function create()
    {
        try {
            Artisan::call('backup:run');
            return Redirect::route('backup.index')->with('success', 'Database Backup Successfully!');
        } catch (\Exception $e) {
            return Redirect::route('backup.index')->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function download(String $getFileName)
    {
        $path = storage_path('app/POS/' . $getFileName);
        
        if (!File::exists($path)) {
            return Redirect::route('backup.index')->with('error', 'Backup file not found!');
        }

        return response()->download($path);
    }

    public function delete(String $getFileName)
    {
        if (Storage::exists('POS/' . $getFileName)) {
            Storage::delete('POS/' . $getFileName);
            return Redirect::route('backup.index')->with('success', 'Database Deleted Successfully!');
        }
        
        return Redirect::route('backup.index')->with('error', 'File not found!');
    }
}
