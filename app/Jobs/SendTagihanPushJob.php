<?php

namespace App\Jobs;

use App\Models\Tagihan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTagihanPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0; // allow long running
    public $tries = 3;

    /**
     * @param array<int> $tagihanIds
     */
    public function __construct(public array $tagihanIds)
    {
    }

    public function handle(): void
    {
        $sent = 0;
        $ignored = 0;
        $failed = 0;

        Log::info('SendTagihanPushJob started', ['total_ids' => count($this->tagihanIds)]);

        $tagihans = Tagihan::with('pelanggan')
            ->whereIn('id', $this->tagihanIds)
            ->where('status_pembayaran', 'belum bayar')
            ->cursor();

        foreach ($tagihans as $tagihan) {
            try {
                $pelanggan = $tagihan->pelanggan;
                if (!$pelanggan || empty($pelanggan->webpushr_sid)) {
                    $ignored++;
                    Log::info('SendTagihanPushJob ignored: missing sid', [
                        'tagihan_id' => $tagihan->id,
                        'pelanggan_id' => $pelanggan?->id,
                    ]);
                    continue;
                }

                $result = $this->sendWebpushrNotification([
                    'title' => 'Tagihan Belum Dibayar',
                    'message' => "Halo {$pelanggan->nama_lengkap}, tagihan Anda akan jatuh tempo pada " . ($tagihan->tanggal_berakhir ?? 'segera') . '. Mohon segera lakukan pembayaran.',
                    'target_url' => url('/dashboard/customer/tagihan'),
                    'sid' => $pelanggan->webpushr_sid,
                ]);

                if ($result['success']) {
                    $sent++;
                } else {
                    $failed++;
                    Log::error('SendTagihanPushJob failed send', [
                        'tagihan_id' => $tagihan->id,
                        'pelanggan_id' => $pelanggan->id,
                        'http_code' => $result['http_code'] ?? null,
                        'error' => $result['error'] ?? null,
                        'response' => $result['response'] ?? null,
                    ]);
                }

                usleep(200000); // 0.2s throttle
            } catch (\Throwable $e) {
                $failed++;
                Log::error('SendTagihanPushJob error per item', [
                    'tagihan_id' => $tagihan->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('SendTagihanPushJob finished', [
            'sent' => $sent,
            'ignored' => $ignored,
            'failed' => $failed,
        ]);
    }

    /**
     * Kirim push ke Webpushr.
     *
     * @param array{title:string,message:string,target_url:string,sid:string} $data
     * @return array{success:bool,error?:string,http_code?:int}
     */
    private function sendWebpushrNotification(array $data): array
    {
        try {
            $ch = curl_init('https://api.webpushr.com/v1/notification/send/sid');

            $payload = [
                'title' => $data['title'],
                'message' => $data['message'],
                'target_url' => $data['target_url'],
                'sid' => $data['sid'],
            ];

            // Pakai fallback agar tetap kirim jika env belum di-set (menyamai controller lama)
            $headers = [
                'Content-Type: application/json',
                'webpushrKey: ' . env('WEBPUSHR_KEY', '2ee12b373a17d9ba5f44683cb42d4279'),
                'webpushrAuthToken: ' . env('WEBPUSHR_TOKEN', '116294'),
            ];

            curl_setopt_array($ch, [
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_CONNECTTIMEOUT => 5,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return ($httpCode === 200 && $response !== false)
                ? ['success' => true, 'response' => $response]
                : ['success' => false, 'http_code' => $httpCode, 'response' => $response];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
