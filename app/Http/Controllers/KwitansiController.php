<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class KwitansiController extends Controller
{
    /**
     * Preview kwitansi di browser
     */
    public function preview($tagihanId)
    {
        // Tingkatkan memory & execution time
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        try {
            // 1. Ambil data tagihan
            $tagihan = Tagihan::findOrFail($tagihanId);

            // 2. Validasi apakah kwitansi ada
            if (!$tagihan->kwitansi) {
                abort(404, 'Kwitansi belum tersedia untuk tagihan ini');
            }

            // 3. Build path file
            $relativePath = $tagihan->kwitansi;

            if (!str_starts_with($relativePath, 'kwitansi/')) {
                $relativePath = 'kwitansi/' . $relativePath;
            }

            // Full path
            $filePath = storage_path('app/public/' . $relativePath);

            // 4. Cek apakah file exists
            if (!file_exists($filePath)) {
                Log::error('Kwitansi file not found', [
                    'tagihan_id' => $tagihanId,
                    'kwitansi_field' => $tagihan->kwitansi,
                    'expected_path' => $filePath,
                ]);
                abort(404, 'File kwitansi tidak ditemukan di server');
            }

            // 5. Deteksi mime type
            $mimeType = mime_content_type($filePath);
            $fileSize = filesize($filePath);

            // 6. Log untuk debugging
            Log::info('Preview kwitansi', [
                'tagihan_id' => $tagihanId,
                'file_size' => $fileSize,
                'memory_usage' => memory_get_usage(true),
                'memory_limit' => ini_get('memory_limit')
            ]);

            // 7. Return dengan streaming response untuk file besar
            return response()->stream(function() use ($filePath) {
                $stream = fopen($filePath, 'rb');

                if ($stream === false) {
                    throw new \Exception('Cannot open file stream');
                }

                // Disable output buffering
                if (ob_get_level()) {
                    ob_end_clean();
                }

                // Stream file menggunakan fpassthru (lebih efisien)
                fpassthru($stream);
                fclose($stream);

            }, 200, [
                'Content-Type' => $mimeType,
                'Content-Length' => $fileSize,
                'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
                'Cache-Control' => 'public, must-revalidate, max-age=0',
                'Pragma' => 'public',
                'Accept-Ranges' => 'bytes',
                'X-Accel-Buffering' => 'no' // Disable nginx buffering
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Tagihan tidak ditemukan');
        } catch (\Exception $e) {
            Log::error('Error preview kwitansi', [
                'tagihan_id' => $tagihanId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Terjadi kesalahan saat memuat kwitansi: ' . $e->getMessage());
        }
    }

    /**
     * Download kwitansi - Optimized untuk Android & iPhone
     */

public function download($tagihanId)
{
    ini_set('memory_limit', '512M');
    ini_set('max_execution_time', 300);

    try {
        // 1. Ambil data
        $tagihan = Tagihan::with('pelanggan')->findOrFail($tagihanId);

        if (!$tagihan->kwitansi) {
            abort(404, 'Kwitansi belum tersedia');
        }

        // 2. Path relatif
        $relativePath = $tagihan->kwitansi;
        if (!str_starts_with($relativePath, 'kwitansi/')) {
            $relativePath = 'kwitansi/' . $relativePath;
        }

        // 3. Full path
        $filePath = storage_path('app/public/' . $relativePath);

        if (!file_exists($filePath)) {
            Log::error('File kwitansi tidak ditemukan', [
                'tagihan_id' => $tagihanId,
                'path' => $filePath
            ]);
            abort(404);
        }

        // 4. Nama file
        $customer = preg_replace(
            '/[^A-Za-z0-9\s_-]/',
            '',
            $tagihan->pelanggan->nama_lengkap ?? 'Customer'
        );
        $customer = str_replace(' ', '_', trim($customer));

        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        $fileName = "Kwitansi_{$tagihan->nomer_id}_{$customer}.{$ext}";

        $mimeType = mime_content_type($filePath);
        $fileSize = filesize($filePath);

        // 5. Streaming download
        return response()->stream(function () use ($filePath) {

            if (ob_get_level()) {
                ob_end_clean();
            }

            $stream = fopen($filePath, 'rb');
            fpassthru($stream);
            fclose($stream);

        }, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Length'      => $fileSize,
            'Content-Disposition' => $this->generateContentDisposition($fileName),
            'Cache-Control'       => 'no-store, no-cache, must-revalidate',
            'Pragma'              => 'no-cache',
            'Accept-Ranges'       => 'bytes',
            'X-Accel-Buffering'   => 'no',
        ]);

    } catch (\Exception $e) {
        Log::error('Error download kwitansi', [
            'tagihan_id' => $tagihanId,
            'error' => $e->getMessage()
        ]);

        abort(500, 'Gagal mengunduh kwitansi');
    }
}


    /**
     * Generate Content-Disposition header dengan RFC 6266 (UTF-8 support)
     * untuk kompatibilitas Android & iPhone
     */
    private function generateContentDisposition($fileName)
    {
        // ASCII fallback untuk browser lama
        $fileNameAscii = mb_convert_encoding($fileName, 'ASCII', 'UTF-8');
        $fileNameAscii = preg_replace('/[^\x20-\x7E]/', '_', $fileNameAscii);

        // UTF-8 encoded filename untuk browser modern (RFC 6266/8187)
        $fileNameUtf8 = rawurlencode($fileName);

        // Format: attachment; filename="ascii-fallback.pdf"; filename*=UTF-8''utf8-encoded.pdf
        return sprintf(
            'attachment; filename="%s"; filename*=UTF-8\'\'%s',
            $fileNameAscii,
            $fileNameUtf8
        );
    }
}
