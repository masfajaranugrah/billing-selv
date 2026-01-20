<?php

namespace App\Http\Controllers;

use App\Jobs\BackupDatabaseJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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
        
        // Filter file .zip dan .sql
        $files = array_filter($files, function($file) {
            $ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
            return in_array($ext, ['zip', 'sql']);
        });
        
        // Sort by modification time (newest first)
        usort($files, function($a, $b) {
            return $b->getMTime() - $a->getMTime();
        });
        
        // Cek status backup yang sedang berjalan
        $backupStatus = $this->getActiveBackupStatus();
        
        // Cleanup old status files
        BackupDatabaseJob::cleanupStatusFiles();
        
        return view('content.apps.Backup.index', compact('files', 'backupStatus'));
    }

    // Buat backup baru - AJAX only
    public function backup(Request $request)
    {
        $type = $request->get('type', 'db');
        $timestamp = date('Y-m-d-H-i-s');
        
        // Untuk backup database saja, jalankan langsung (cepat)
        if ($type === 'db') {
            return $this->backupDatabaseDirect($timestamp);
        }
        
        // Untuk full backup, gunakan queue (background)
        return $this->backupFullQueue($timestamp);
    }

    /**
     * Backup database secara langsung (tanpa queue) - cepat
     */
    private function backupDatabaseDirect($timestamp)
    {
        set_time_limit(120); // 2 menit cukup untuk DB
        
        $sqlFileName = 'backup-db-' . $timestamp . '.sql';
        $sqlFilePath = $this->backupPath . '/' . $sqlFileName;

        $result = $this->runMysqldump($sqlFilePath);
        
        if ($result !== true) {
            return response()->json([
                'success' => false,
                'message' => $result
            ]);
        }

        $fileSize = $this->formatBytes(filesize($sqlFilePath));
        
        return response()->json([
            'success' => true,
            'message' => "Backup database berhasil: {$sqlFileName} ({$fileSize})",
            'completed' => true
        ]);
    }

    /**
     * Full backup via queue (background)
     */
    private function backupFullQueue($timestamp)
    {
        $backupId = Str::uuid()->toString();

        // Simpan backup ID di session untuk tracking
        session(['active_backup_id' => $backupId]);

        // Set initial status (file-based) - ini sangat cepat
        $statusFile = storage_path('app/backup_status_' . $backupId . '.json');
        file_put_contents($statusFile, json_encode([
            'status' => 'pending',
            'message' => 'Backup lengkap dijadwalkan...',
            'updated_at' => now()->toDateTimeString(),
        ]));

        // Dispatch job ke queue - non-blocking
        BackupDatabaseJob::dispatch('full', $timestamp, $backupId);

        return response()->json([
            'success' => true,
            'message' => 'Backup lengkap sedang diproses di background.',
            'backup_id' => $backupId,
            'completed' => false
        ]);
    }

    /**
     * Run mysqldump directly
     */
    private function runMysqldump($filePath)
    {
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port', '3306');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        // Cari mysqldump
        $mysqldumpPaths = [
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            'mysqldump',
            '/opt/homebrew/bin/mysqldump',
        ];

        $mysqldump = null;
        foreach ($mysqldumpPaths as $path) {
            $check = shell_exec("which $path 2>/dev/null") ?: (file_exists($path) ? $path : null);
            if ($check) {
                $mysqldump = trim($check) ?: $path;
                break;
            }
        }

        if (!$mysqldump) {
            return 'mysqldump tidak ditemukan.';
        }

        // Build command
        $escapedPass = escapeshellarg($dbPass);
        
        if (empty($dbPass)) {
            $command = sprintf(
                '%s -h %s -P %s -u %s %s > %s 2>&1',
                escapeshellcmd($mysqldump),
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                escapeshellarg($dbName),
                escapeshellarg($filePath)
            );
        } else {
            $command = sprintf(
                '%s -h %s -P %s -u %s -p%s %s > %s 2>&1',
                escapeshellcmd($mysqldump),
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                $escapedPass,
                escapeshellarg($dbName),
                escapeshellarg($filePath)
            );
        }

        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            if (file_exists($filePath)) unlink($filePath);
            return 'Database backup gagal: ' . implode(' ', $output);
        }

        if (!file_exists($filePath) || filesize($filePath) === 0) {
            if (file_exists($filePath)) unlink($filePath);
            return 'Database backup gagal: File kosong.';
        }

        return true;
    }

    // API endpoint untuk cek status backup
    public function checkStatus(Request $request)
    {
        $backupId = $request->get('id') ?? session('active_backup_id');
        
        if (!$backupId) {
            return response()->json(['status' => 'idle', 'message' => null]);
        }

        $status = BackupDatabaseJob::getStatus($backupId);

        if (!$status) {
            return response()->json(['status' => 'idle', 'message' => null]);
        }

        // Cleanup jika sudah selesai
        if (in_array($status['status'], ['completed', 'failed'])) {
            session()->forget('active_backup_id');
            $statusFile = storage_path('app/backup_status_' . $backupId . '.json');
            if (file_exists($statusFile)) unlink($statusFile);
        }

        return response()->json($status);
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

    // Download backup
    public function download($filename)
    {
        $path = storage_path('app/Laravel/' . $filename);

        if (!is_file($path)) {
            abort(404);
        }

        while (ob_get_level()) {
            ob_end_clean();
        }

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $contentType = $ext === 'zip' ? 'application/zip' : 'application/sql';

        return response()->download($path, $filename, [
            'Content-Type' => $contentType,
            'Content-Length' => filesize($path),
        ]);
    }

    private function getActiveBackupStatus()
    {
        $backupId = session('active_backup_id');
        if (!$backupId) return null;

        $status = BackupDatabaseJob::getStatus($backupId);
        if (!$status) {
            session()->forget('active_backup_id');
            return null;
        }

        if (in_array($status['status'], ['completed', 'failed'])) {
            session()->forget('active_backup_id');
        }

        return $status;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }
}