<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OutstandingController extends Controller
{
    // get data json
    public function indexGetJson()
    {
        // Ambil semua pelanggan & paket untuk dropdown modal
        $pelanggan = Pelanggan::all();
        $paket = Paket::all();

        // Ambil semua tagihan dengan status "belum bayar" beserta relasinya
        $tagihans = Tagihan::with(['pelanggan', 'paket'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $pelanggan = $item->pelanggan;
                $paket = $item->paket;

                return [
                    'id' => $item->id,
                    'nomer_id' => $pelanggan->nomer_id ?? '-',
                    'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
                    'alamat_jalan' => $pelanggan->alamat_jalan ?? '-',
                    'rt' => $pelanggan->rt ?? '-',
                    'rw' => $pelanggan->rw ?? '-',
                    'desa' => $pelanggan->desa ?? '-',
                    'kecamatan' => $pelanggan->kecamatan ?? '-',
                    'kabupaten' => $pelanggan->kabupaten ?? '-',
                    'provinsi' => $pelanggan->provinsi ?? '-',
                    'kode_pos' => $pelanggan->kode_pos ?? '-',

                    'paket' => [
                        'id' => $paket->id ?? null,
                        'nama_paket' => $paket->nama_paket ?? '-',
                        'harga' => $paket->harga ?? 0,
                        'kecepatan' => $paket->kecepatan ?? 0,
                        'masa_pembayaran' => $paket->masa_pembayaran ?? 0,
                        'durasi' => $paket->durasi ?? 0,
                    ],

                    'tanggal_mulai' => $item->tanggal_mulai,
                    'tanggal_berakhir' => $item->tanggal_berakhir,
                    'status_pembayaran' => $item->status_pembayaran,
                    'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
                    'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
                    'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
                    'catatan' => $item->catatan ?? '-',
                ];
            });

        // Ambil list unik untuk dropdown
        $kabupatenList = $pelanggan->pluck('kabupaten')->unique()->values();
        $kecamatanList = $pelanggan->pluck('kecamatan')->unique()->values();

        // Statistik
        $totalCustomer = $pelanggan->count();
        $lunas = 0;
        $belumLunas = $tagihans->count();
        $totalPaket = $paket->count();

        return response()->json([
            'status' => true,
            'message' => 'Data tagihan berhasil diambil.',
            'data' => [
                'tagihans' => $tagihans,
                'pelanggan' => $pelanggan,
                'paket' => $paket,
                'statistics' => [
                    'total_customer' => $totalCustomer,
                    'lunas' => $lunas,
                    'belum_lunas' => $belumLunas,
                    'total_paket' => $totalPaket,
                ],
                'filters' => [
                    'kabupaten' => $kabupatenList,
                    'kecamatan' => $kecamatanList,
                ],
            ],
        ]);
    }

    public function getByIdJson($id)
    {
        // Ambil data tagihan berdasarkan ID + relasi pelanggan & paket
        $item = Tagihan::with(['pelanggan', 'paket'])->find($id);

        if (! $item) {
            return response()->json([
                'status' => false,
                'message' => 'Tagihan tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        $pelanggan = $item->pelanggan;
        $paket = $item->paket;

        // Bentuk JSON detail (sama dengan indexGetJson)
        $tagihanDetail = [
            'id' => $item->id,
            'nomer_id' => $pelanggan->nomer_id ?? '-',
            'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
            'alamat_jalan' => $pelanggan->alamat_jalan ?? '-',
            'rt' => $pelanggan->rt ?? '-',
            'rw' => $pelanggan->rw ?? '-',
            'desa' => $pelanggan->desa ?? '-',
            'kecamatan' => $pelanggan->kecamatan ?? '-',
            'kabupaten' => $pelanggan->kabupaten ?? '-',
            'provinsi' => $pelanggan->provinsi ?? '-',
            'kode_pos' => $pelanggan->kode_pos ?? '-',

            'paket' => [
                'id' => $paket->id ?? null,
                'nama_paket' => $paket->nama_paket ?? '-',
                'harga' => $paket->harga ?? 0,
                'kecepatan' => $paket->kecepatan ?? 0,
                'masa_pembayaran' => $paket->masa_pembayaran ?? 0,
                'durasi' => $paket->durasi ?? 0,
            ],

            'tanggal_mulai' => $item->tanggal_mulai,
            'tanggal_berakhir' => $item->tanggal_berakhir,
            'status_pembayaran' => $item->status_pembayaran,
            'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
            'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
            'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
            'catatan' => $item->catatan ?? '-',
        ];

        return response()->json([
            'status' => true,
            'message' => 'Detail tagihan berhasil diambil.',
            'data' => $tagihanDetail,
        ]);
    }




public function konfirmasiBayar(Request $request, $id)
{
$tagihan = Tagihan::with('pelanggan', 'paket')->findOrFail($id);


DB::beginTransaction();
try {
    // Upload bukti pembayaran (opsional)
    if ($request->hasFile('bukti_pembayaran')) {
        $file = $request->file('bukti_pembayaran');
        $path = $file->store('bukti_pembayaran', 'public');
        $tagihan->bukti_pembayaran = $path;
    }

    // Update status tagihan menjadi lunas
    $tagihan->status_pembayaran = 'lunas';
    $tagihan->tanggal_pembayaran = now();

    // Generate PDF kwitansi
    $pdf = Pdf::loadView('content.apps.pdf.kwitansi', ['tagihan' => $tagihan]);
    $filename = 'kwitansi-'.$tagihan->id.'.pdf';
    $pdfPath = 'kwitansi/'.$filename;
    Storage::disk('public')->put($pdfPath, $pdf->output());

    // Simpan path PDF ke field kwitansi
    $tagihan->kwitansi = $pdfPath;
    $tagihan->save();

    // Buat link publik PDF
    $pdfUrl = asset('storage/'.$pdfPath);

    // Buat record Income
    Income::create([
        'kode' => $this->getKode('penjualan'),
        'kategori' => 'penjualan',
        'jumlah' => $tagihan->jumlah_tagihan ?? $tagihan->paket->harga,
        'keterangan' => 'Pembayaran paket '.$tagihan->paket->nama_paket.' dari '.$tagihan->pelanggan->nama_lengkap,
        'tanggal_masuk' => now(),
    ]);

    // ===== Kirim push notification sebelum return =====
    $pelanggan = $tagihan->pelanggan;
    if ($pelanggan && $pelanggan->webpushr_sid) {
        $end_point = 'https://api.webpushr.com/v1/notification/send/sid';

        $http_header = [
            'Content-Type: Application/Json',
            'webpushrKey: 2ee12b373a17d9ba5f44683cb42d4279', // ganti dengan API key Webpushr
            'webpushrAuthToken: 116294', // ganti dengan Auth Token Webpushr
        ];

        $req_data = [
           'title' => 'Pembayaran Berhasil',
            'message' => "Terima kasih, {$pelanggan->nama_lengkap}. Pembayaran Anda telah kami terima dan dikonfirmasi.",
            'target_url' => url('https://layanan.jernih.net.id/dashboard/customer/tagihan/selesai'), // link ke halaman tagihan
            'sid' => $pelanggan->webpushr_sid,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
        curl_setopt($ch, CURLOPT_URL, $end_point);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        // Optional: log response untuk debug
    }
    // ===================================================

    DB::commit();

    return response()->json([
        'success' => true,
        'pdfUrl' => $pdfUrl,
        'message' => 'Pembayaran berhasil dikonfirmasi dan notifikasi terkirim!',
    ]);
} catch (\Exception $e) {
    DB::rollBack();

    return response()->json(['success' => false, 'message' => $e->getMessage()]);
}


}




    /**
     * Contoh fungsi helper untuk kirim WA (dummy)
     */
    private function sendWA($nomor, $pesan)
    {
        // TODO: implementasi request ke API WhatsApp
        // return true jika berhasil, false jika gagal
        return true;
    }


public function index(Request $request)
{
    // Ambil semua pelanggan & paket untuk dropdown modal
  $pelanggan = Pelanggan::where('status', 'approve')
        ->orderBy('nama_lengkap', 'asc')
        ->get();
    $paket = Paket::all();

    // ? BUILD QUERY
    $query = Tagihan::with(['pelanggan', 'paket']);

    // ? FILTER OUTSTANDING: Hanya tagihan bulan sebelumnya (bukan bulan ini)
    $query->where(function($q) {
        $q->whereYear('tanggal_berakhir', '<', now()->year)
          ->orWhere(function($subQ) {
              $subQ->whereYear('tanggal_berakhir', '=', now()->year)
                   ->whereMonth('tanggal_berakhir', '<', now()->month);
          });
    });

    // ? SEARCH FILTER - HANYA DI STATUS "BELUM BAYAR"
    if ($request->filled('search')) {
        $search = trim($request->search);
        
        // ? HARDCODE: Hanya cari di status "belum bayar"
        $query->where('status_pembayaran', 'belum bayar')
              ->whereHas('pelanggan', function($q) use ($search) {
                  $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nomer_id', 'like', "%{$search}%")
                    ->orWhere('no_whatsapp', 'like', "%{$search}%")
                    ->orWhere('no_telp', 'like', "%{$search}%")
                    ->orWhere('alamat_jalan', 'like', "%{$search}%")
                    ->orWhere('rt', 'like', "%{$search}%")
                    ->orWhere('rw', 'like', "%{$search}%")
                    ->orWhere('desa', 'like', "%{$search}%")
                    ->orWhere('kecamatan', 'like', "%{$search}%")
                    ->orWhere('kabupaten', 'like', "%{$search}%")
                    ->orWhere('kode_pos', 'like', "%{$search}%");
              });
    } else {
        // ? JIKA TIDAK ADA SEARCH, TAMPILKAN SEMUA (DENGAN FILTER STATUS JIKA ADA)
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }
    }

    // ? PAGINATION
    $tagihans = $query->orderBy('tanggal_berakhir', 'desc')
        ->paginate(40)
        ->withQueryString()
        ->through(function ($item) {
            $pelanggan = $item->pelanggan;
            $paket = $item->paket;

            return [
                'id' => $item->id,
                'pelanggan_id' => $item->pelanggan_id,
                'nomer_id' => $pelanggan->nomer_id ?? '-',
                'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
                'alamat_jalan' => $pelanggan->alamat_jalan ?? '-',
                'rt' => $pelanggan->rt ?? '-',
                'rw' => $pelanggan->rw ?? '-',
                'desa' => $pelanggan->desa ?? '-',
                'kecamatan' => $pelanggan->kecamatan ?? '-',
                'kabupaten' => $pelanggan->kabupaten ?? '-',
                'provinsi' => $pelanggan->provinsi ?? '-',
                'kode_pos' => $pelanggan->kode_pos ?? '-',
                'paket' => [
                    'id' => $paket->id ?? null,
                    'nama_paket' => $paket->nama_paket ?? '-',
                    'harga' => $paket->harga ?? 0,
                    'kecepatan' => $paket->kecepatan ?? 0,
                    'masa_pembayaran' => $paket->masa_pembayaran ?? 0,
                    'durasi' => $paket->durasi ?? 0,
                ],
                'tanggal_mulai' => $item->tanggal_mulai,
                'tanggal_berakhir' => $item->tanggal_berakhir,
                'status_pembayaran' => $item->status_pembayaran ?? 'belum bayar',
                'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
                'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
                'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
                'catatan' => $item->catatan ?? '-',
            ];
        });

    // Statistik - update untuk outstanding saja
    $totalCustomer = Pelanggan::where('status', 'approve')->count();
    $lunas = Tagihan::where('status_pembayaran', 'lunas')
        ->where(function($q) {
            $q->whereYear('tanggal_berakhir', '<', now()->year)
              ->orWhere(function($subQ) {
                  $subQ->whereYear('tanggal_berakhir', '=', now()->year)
                       ->whereMonth('tanggal_berakhir', '<', now()->month);
              });
        })->count();
    
    $belumLunas = Tagihan::where('status_pembayaran', 'belum bayar')
        ->where(function($q) {
            $q->whereYear('tanggal_berakhir', '<', now()->year)
              ->orWhere(function($subQ) {
                  $subQ->whereYear('tanggal_berakhir', '=', now()->year)
                       ->whereMonth('tanggal_berakhir', '<', now()->month);
              });
        })->count();
    
    $totalPaket = $paket->count();

    return view('content.apps.Outstanding.tagihan', [
        'tagihans' => $tagihans,
        'pelanggan' => $pelanggan,
        'paket' => $paket,
        'totalCustomer' => $totalCustomer,
        'lunas' => $lunas,
        'belumLunas' => $belumLunas,
        'totalPaket' => $totalPaket,
    ]);
}



public function proses()
{
    // Ambil semua pelanggan & paket untuk dropdown modal
    $pelanggan = Pelanggan::all();
    $paket = Paket::all();

    // Query dengan pagination - 20 data per page (TANPA through/map)
    $tagihans = Tagihan::with(['pelanggan', 'paket'])
        ->where('status_pembayaran', 'proses_verifikasi')
        ->orderBy('created_at', 'desc')
        ->paginate(20); // HANYA INI SAJA, JANGAN PAKAI through() atau map()

    // Ambil list unik untuk filter dropdown
    $kabupatenList = $pelanggan->pluck('kabupaten')->unique();
    $kecamatanList = $pelanggan->pluck('kecamatan')->unique();

    // Statistik
    $totalCustomer = $pelanggan->count();
    $lunas = 0;
    $belumLunas = Tagihan::where('status_pembayaran', 'proses_verifikasi')->count();
    $totalPaket = $paket->count();

    return view('content.apps.Tagihan.proses-tagihan', compact(
        'tagihans',
        'pelanggan',
        'paket',
        'totalCustomer',
        'lunas',
        'belumLunas',
        'totalPaket',
        'kabupatenList',
        'kecamatanList'
    ));
}






public function lunas()
{
    // Ambil semua pelanggan & paket untuk dropdown modal
    $pelanggan = Pelanggan::all();
    $paket = Paket::all();

    // Ambil semua tagihan dengan status "lunas" beserta relasinya
    $tagihans = Tagihan::with(['pelanggan', 'paket'])
        ->where('status_pembayaran', 'lunas')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($item) {
            $pelanggan = $item->pelanggan;
            $paket = $item->paket;


$kwitansiUrl = null;
if (!empty($item->kwitansi)) {
    $kwitansiUrl = $item->kwitansi;
}
            return [
                'id' => $item->id,
                'nomer_id' => $pelanggan->nomer_id ?? '-',
                'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
                'alamat_jalan' => $pelanggan->alamat_jalan ?? '-',
                'rt' => $pelanggan->rt ?? '-',
                'rw' => $pelanggan->rw ?? '-',
                'desa' => $pelanggan->desa ?? '-',
                'kecamatan' => $pelanggan->kecamatan ?? '-',
                'kabupaten' => $pelanggan->kabupaten ?? '-',
                'provinsi' => $pelanggan->provinsi ?? '-',
                'kode_pos' => $pelanggan->kode_pos ?? '-',
                'paket' => [
                    'id' => $paket->id ?? null,
                    'nama_paket' => $paket->nama_paket ?? '-',
                    'harga' => $paket->harga ?? 0,
                    'kecepatan' => $paket->kecepatan ?? 0,
                    'masa_pembayaran' => $paket->masa_pembayaran ?? 0,
                    'durasi' => $paket->durasi ?? 0,
                ],
                'tanggal_mulai' => $item->tanggal_mulai ?? null,
                'tanggal_berakhir' => $item->tanggal_berakhir ?? null,
                'status_pembayaran' => $item->status_pembayaran ?? 'belum bayar',
 		 'type_pembayaran' => $item->rekening->nama_bank ?? '-',

                'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
                'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
                'kwitansi' => $kwitansiUrl,
                'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
                'catatan' => $item->catatan ?? '-',
            ];
        });

    // Ambil list unik untuk filter dropdown
    $kabupatenList = $pelanggan->pluck('kabupaten')->unique();
    $kecamatanList = $pelanggan->pluck('kecamatan')->unique();

    // Statistik
    $totalCustomer = $pelanggan->count();
    $lunas = $tagihans->count(); // Jumlah tagihan yang sudah lunas
    $belumLunas = Tagihan::where('status_pembayaran', '!=', 'lunas')->count(); // Hitung tagihan belum lunas
    $totalPaket = $paket->count();

    return view('content.apps.Tagihan.tagihan-lunas', compact(
        'tagihans',
        'pelanggan',
        'paket',
        'totalCustomer',
        'lunas',
        'belumLunas',
        'totalPaket',
        'kabupatenList',
        'kecamatanList'
    ));
}


    /**
     * Update data tagihan
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'nullable|date',
            'catatan' => 'nullable|string',
            'paket_id' => 'required|exists:pakets,id',
            'bukti_pembayaran' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'kwitansi' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $tagihan = Tagihan::findOrFail($id);
        $paket = Paket::findOrFail($request->paket_id);

        // Parse tanggal
        $tanggalMulai = \Carbon\Carbon::parse($request->tanggal_mulai);
        $tanggalBerakhir = $request->tanggal_berakhir
            ? \Carbon\Carbon::parse($request->tanggal_berakhir)
            : $tanggalMulai->copy()->addDays($paket->masa_pembayaran);

        // Handle bukti_pembayaran
        if ($request->hasFile('bukti_pembayaran')) {
            // Hapus file lama jika ada
            if ($tagihan->bukti_pembayaran && Storage::disk('public')->exists($tagihan->bukti_pembayaran)) {
                Storage::disk('public')->delete($tagihan->bukti_pembayaran);
            }

            // Simpan file baru
            $tagihan->bukti_pembayaran = $request->file('bukti_pembayaran')
                ->store('bukti_pembayaran', 'public');
        }

        // Handle kwitansi jika ada
        if ($request->hasFile('kwitansi')) {
            if ($tagihan->kwitansi && Storage::disk('public')->exists($tagihan->kwitansi)) {
                Storage::disk('public')->delete($tagihan->kwitansi);
            }

            $tagihan->kwitansi = $request->file('kwitansi')
                ->store('kwitansi', 'public');
        }

        // Update field lainnya
        $tagihan->update([
            'paket_id' => $request->paket_id,
            'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
            'tanggal_berakhir' => $tanggalBerakhir->format('Y-m-d'),
            'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Tagihan berhasil diperbarui!');
    }


public function store(Request $request)
{
    $request->validate([
        'pelanggan_id' => 'required|exists:pelanggans,id',
        'paket_id' => 'required|exists:pakets,id',
        'tanggal_mulai' => 'required|date',
        'tanggal_berakhir' => 'nullable|date',
        'catatan' => 'nullable|string',
    ]);

    $paket = Paket::findOrFail($request->paket_id);
    $tanggalMulai = \Carbon\Carbon::parse($request->tanggal_mulai);
    $tanggalBerakhir = $request->tanggal_berakhir
        ? \Carbon\Carbon::parse($request->tanggal_berakhir)
        : $tanggalMulai->copy()->addDays($paket->masa_pembayaran);

    $tagihan = Tagihan::create([
        'pelanggan_id' => $request->pelanggan_id,
        'paket_id' => $request->paket_id,
        'harga' => $paket->harga,
        'tanggal_mulai' => $tanggalMulai->format('Y-m-d'),
        'tanggal_berakhir' => $tanggalBerakhir->format('Y-m-d'),
        'status_pembayaran' => 'belum bayar',
        'catatan' => $request->catatan,
    ]);

    $pelanggan = Pelanggan::find($request->pelanggan_id);

    // Kirim push notification jika SID tersedia
    if ($pelanggan && $pelanggan->webpushr_sid) {
        $ch = curl_init('https://api.webpushr.com/v1/notification/send/sid');

        $payload = [
    'title' => 'Pemberitahuan untuk Anda',
    'message' => "Halo {$pelanggan->nama}, kami baru saja menerbitkan tagihan untuk Anda. Silakan cek detailnya.",
    'target_url' => url('https://layanan.jernih.net.id/dashboard/customer/tagihan'),
    'sid' => $pelanggan->webpushr_sid,
];

        $headers = [
            'Content-Type: application/json',
            'webpushrKey: 2ee12b373a17d9ba5f44683cb42d4279',
            'webpushrAuthToken: 116294',
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);



        curl_close($ch);
    }

    return redirect()->back()->with('success', 'Tagihan berhasil ditambahkan dan notifikasi terkirim!');
}



    private function sendOneSignalNotification($playerId, $title, $message)
    {
        $content = [
            'en' => $message,
        ];

        $fields = [
            'app_id' => env('ONESIGNAL_APP_ID'),
            'include_player_ids' => [$playerId],
            'headings' => ['en' => $title],
            'contents' => $content,
        ];

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic '.env('ONESIGNAL_REST_API_KEY'),
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * Generate kode otomatis per kategori
     */
    private function getKode($kategori)
    {
        return match (strtolower($kategori)) {
            'internet' => '01',
            'penjualan' => '02',
            'piutang' => '03',
            default => 'O4', // DLL atau kategori custom
        };

    }

    // ? Update tagihan
    public function updateStatus($id)
    {
        $tagihan = \App\Models\Tagihan::with('pelanggan', 'paket')->find($id);

        if (! $tagihan) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak ditemukan.',
            ], 404);
        }

        // Update status tagihan
        $tagihan->status_pembayaran = 'lunas';
        $tagihan->tanggal_pembayaran = now();
        $tagihan->save();

        // Buat data Income baru
        Income::create([
            'kode' => $this->getCode(), // atau gunakan helper getKode() jika mau auto-generate
            'kategori' => 'Tagihan',
            'jumlah' => $tagihan->jumlah_tagihan ?? $tagihan->paket->harga,
            'keterangan' => 'Pembayaran paket '.$tagihan->paket->nama_paket.' dari '.$tagihan->pelanggan->nama_lengkap,
            'tanggal_masuk' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran berhasil diperbarui menjadi lunas dan income tercatat.',
        ]);
    }

    // ? Hapus tagihan
    public function destroy($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $tagihan->delete();

        return redirect()->back()->with('success', '??? Tagihan berhasil dihapus!');
    }

 public function massStore(Request $request)
{
    $request->validate([
        'tanggal_mulai' => 'required|date',
        'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',
    ]);

    // Ambil MAX 100 pelanggan yang BELUM PUNYA TAGIHAN BELUM BAYAR
    $pelanggan = Pelanggan::with('paket')
        ->where('status', 'approve')
        ->whereNotIn('id', function ($query) {
            $query->select('pelanggan_id')
                  ->from('tagihans')
                  ->where('status_pembayaran', 'belum bayar');
        })
        ->limit(100)
        ->get();

    if ($pelanggan->isEmpty()) {
        return back()->with('error', 'Tidak ada pelanggan yang bisa dibuatkan tagihan.');
    }

    DB::beginTransaction();
    try {
        foreach ($pelanggan as $p) {
            Tagihan::create([
                'pelanggan_id' => $p->id,
                'paket_id' => $p->paket_id,
                'harga' => $p->paket->harga,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_berakhir' => $request->tanggal_berakhir,
                'status_pembayaran' => 'belum bayar',
            ]);
        }

        DB::commit();
        return back()->with('success', 'Berhasil membuat tagihan untuk 100 pelanggan berikutnya.');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', $e->getMessage());
    }
}





/**
 * Halaman Outstanding - Semua Tagihan (Tanpa Filter Status)
 * Berguna untuk melihat semua tagihan dari bulan lain
 */
public function outstanding(Request $request)
{
    // ? Base query dengan eager loading
    $query = Tagihan::with(['pelanggan', 'paket']);

    // ? Filter berdasarkan bulan/tahun (opsional)
    if ($request->filled('bulan')) {
        $query->whereMonth('tanggal_mulai', $request->bulan);
    }

    if ($request->filled('tahun')) {
        $query->whereYear('tanggal_mulai', $request->tahun);
    }

    // ? Filter berdasarkan status (opsional, tapi tetap bisa semua)
    if ($request->filled('status_filter') && $request->status_filter !== 'semua') {
        $query->where('status_pembayaran', $request->status_filter);
    }

    // ? Search functionality
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->whereHas('pelanggan', function($subQ) use ($search) {
                $subQ->where('nama_lengkap', 'LIKE', "%{$search}%")
                     ->orWhere('nomer_id', 'LIKE', "%{$search}%")
                     ->orWhere('no_whatsapp', 'LIKE', "%{$search}%");
            })
            ->orWhereHas('paket', function($subQ) use ($search) {
                $subQ->where('nama_paket', 'LIKE', "%{$search}%");
            });
        });
    }

    // ? Sorting berdasarkan tanggal terbaru
    $sortBy = $request->input('sort_by', 'created_at');
    $sortOrder = $request->input('sort_order', 'desc');
    $query->orderBy($sortBy, $sortOrder);

    // ? Pagination
    $perPage = $request->input('per_page', 20);
    $tagihans = $query->paginate($perPage)->withQueryString();

    // ? Map data untuk view
    $tagihans->getCollection()->transform(function ($item) {
        $pelanggan = $item->pelanggan;
        $paket = $item->paket;

        return (object) [
            'id' => $item->id,
            'nomer_id' => $pelanggan->nomer_id ?? '-',
            'nama_lengkap' => $pelanggan->nama_lengkap ?? '-',
            'alamat_jalan' => $pelanggan->alamat_jalan ?? '-',
            'rt' => $pelanggan->rt ?? '-',
            'rw' => $pelanggan->rw ?? '-',
            'desa' => $pelanggan->desa ?? '-',
            'kecamatan' => $pelanggan->kecamatan ?? '-',
            'kabupaten' => $pelanggan->kabupaten ?? '-',
            'provinsi' => $pelanggan->provinsi ?? '-',
            'kode_pos' => $pelanggan->kode_pos ?? '-',
            'paket' => [
                'id' => $paket->id ?? null,
                'nama_paket' => $paket->nama_paket ?? '-',
                'harga' => $paket->harga ?? 0,
                'kecepatan' => $paket->kecepatan ?? 0,
                'masa_pembayaran' => $paket->masa_pembayaran ?? 0,
                'durasi' => $paket->durasi ?? 0,
            ],
            'tanggal_mulai' => $item->tanggal_mulai ?? null,
            'tanggal_berakhir' => $item->tanggal_berakhir ?? null,
            'status_pembayaran' => $item->status_pembayaran ?? 'belum bayar',
            'tanggal_pembayaran' => $item->tanggal_pembayaran ?? '-',
            'bukti_pembayaran' => $item->bukti_pembayaran ?? '-',
            'kwitansi' => $item->kwitansi ?? null,
            'no_whatsapp' => $pelanggan->no_whatsapp ?? '08xxxxxxxxxx',
            'catatan' => $item->catatan ?? '-',
        ];
    });

    // ? Ambil pelanggan & paket untuk dropdown (jika ada modal)
    $pelanggan = Pelanggan::where('status', 'approve')->get();
    $paket = Paket::all();

    // ? Statistik Outstanding
    try {
        $totalTagihan = Tagihan::count();
        $totalBelumBayar = Tagihan::where('status_pembayaran', 'belum bayar')->count();
        $totalProses = Tagihan::where('status_pembayaran', 'proses_verifikasi')->count();
        $totalLunas = Tagihan::where('status_pembayaran', 'lunas')->count();

        // Total tagihan yang overdue (lewat tanggal jatuh tempo)
        $totalOverdue = Tagihan::where('status_pembayaran', '!=', 'lunas')
            ->where('tanggal_berakhir', '<', now())
            ->count();

        // Total nilai outstanding (belum dibayar)
        $nilaiOutstanding = Tagihan::where('status_pembayaran', 'belum bayar')
            ->join('pakets', 'tagihans.paket_id', '=', 'pakets.id')
            ->sum('pakets.harga');

        $statistics = [
            'total' => $totalTagihan,
            'belum_bayar' => $totalBelumBayar,
            'proses' => $totalProses,
            'lunas' => $totalLunas,
            'overdue' => $totalOverdue,
            'nilai_outstanding' => $nilaiOutstanding,
        ];
    } catch (\Exception $e) {
        $statistics = [
            'total' => 0,
            'belum_bayar' => 0,
            'proses' => 0,
            'lunas' => 0,
            'overdue' => 0,
            'nilai_outstanding' => 0,
        ];
    }

    // ? Filter dropdown lists
    $kabupatenList = Pelanggan::pluck('kabupaten')->unique()->filter();
    $kecamatanList = Pelanggan::pluck('kecamatan')->unique()->filter();

    // ? Bulan untuk filter
    $bulanList = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    // ? Tahun untuk filter (5 tahun terakhir)
    $tahunList = range(date('Y'), date('Y') - 4);

    return view('content.apps.Tagihan.outstanding', compact(
        'tagihans',
        'pelanggan',
        'paket',
        'statistics',
        'kabupatenList',
        'kecamatanList',
        'bulanList',
        'tahunList'
    ));
}





}
