<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan; // <-- tambahkan ini

class DatabaseBackupController extends Controller
{
    private $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/Laravel');

        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    // Tampilkan halaman backup
    public function index()
    {
        $files = File::files($this->backupPath);
        return view('content.apps.Backup.index', compact('files'));
    }

    // Buat backup baru
  public function backup()
{
    $dbHost = env('DB_HOST');
    $dbPort = env('DB_PORT', '3306');
    $dbName = env('DB_DATABASE');
    $dbUser = env('DB_USERNAME');
    $dbPass = env('DB_PASSWORD');

    $fileName = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
    $filePath = $this->backupPath . '/' . $fileName;

    $command = "mysqldump -h {$dbHost} -P {$dbPort} -u {$dbUser} -p'{$dbPass}' {$dbName} > {$filePath}";

    try {
        system($command, $returnVar);
        if ($returnVar !== 0) {
            return back()->with('error', 'Backup gagal dibuat.');
        }
        return back()->with('success', 'Backup berhasil dibuat: ' . $fileName);
    } catch (\Exception $e) {
        return back()->with('error', 'Backup gagal: ' . $e->getMessage());
    }
}

    // Hapus backup
    public function delete($filename)
    {
        $filePath = $this->backupPath . '/' . $filename;

        if (File::exists($filePath)) {
            File::delete($filePath);
            return back()->with('success', 'Backup berhasil dihapus.');
        }

        return back()->with('error', 'File backup tidak ditemukan.');
    }


public function download($filename)
{
    $path = storage_path('app/Laravel/' . $filename);

    if (!is_file($path)) {
        abort(404);
    }

    // bersihkan semua buffer
    while (ob_get_level()) {
        ob_end_clean();
    }

    return response()->download($path, $filename, [
        'Content-Type' => 'application/zip',
        'Content-Length' => filesize($path),
    ]);
}


}