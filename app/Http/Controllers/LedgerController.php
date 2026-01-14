<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\SaldoAwal;
use App\Models\Tagihan;
use App\Models\Paket;
use App\Exports\PembukuanTotalExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class LedgerController extends Controller
{
    // Kategori Pengeluaran
    private $kategoriPengeluaran = [
        'BEBAN GAJI' => '202',
        'ALAT KANTOR HABIS PAKAI' => '203',
        'ALAT LOGISTIK' => '203',
        'ALAT TULIS KANTOR' => '203',
        'KONSUMSI' => '204',
        'BEBAN TRANSPORTASI' => '205',
        'BEBAN PERAWATAN' => '205',
        'BEBAN LAT (LISTRIK, AIR, TELEPON)' => '205',
        'BEBAN KEPERLUAN RUMAH TANGGA' => '205',
        'BEBAN TAGIHAN INTERNET' => '205',
        'BEBAN LAIN-LAIN' => '205',
        'BEBAN KOMITMEN / FEE' => '205',
        'BEBAN PRIVE' => '205',
        'BEBAN SRAGEN' => '205',
        'BEBAN GUNUNGKIDUL' => '205',
    ];

    public function index(Request $request)
    {
        // Cek apakah ada filter bulan dan tahun
        $hasFilter = $request->has('bulan') && $request->has('tahun');

        if ($hasFilter) {
            // JIKA ADA FILTER: Tampilkan semua transaksi per hari dalam bulan tersebut
            $bulan = $request->get('bulan');
            $tahun = $request->get('tahun');

            $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

            // Get income per hari dalam bulan yang dipilih
            $incomesData = Income::whereBetween('tanggal_masuk', [$startDate, $endDate])
                ->selectRaw('DATE(tanggal_masuk) as tanggal, SUM(jumlah) as total_masuk')
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'asc')
                ->get()
                ->keyBy('tanggal');

            // Get expenses per hari dalam bulan yang dipilih
            $expensesData = Expense::whereBetween('tanggal_keluar', [$startDate, $endDate])
                ->selectRaw('DATE(tanggal_keluar) as tanggal, SUM(jumlah) as total_keluar')
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'asc')
                ->get()
                ->keyBy('tanggal');

            $filterMode = 'bulanan';

        } else {
            // DEFAULT (TANPA FILTER): Tampilkan transaksi hari ini saja
            $today = Carbon::today();
            $bulan = date('m');
            $tahun = date('Y');
            $startDate = $today;
            $endDate = $today->copy()->endOfDay();

            // Get income hari ini
            $incomesData = Income::whereDate('tanggal_masuk', $today)
                ->selectRaw('DATE(tanggal_masuk) as tanggal, SUM(jumlah) as total_masuk')
                ->groupBy('tanggal')
                ->get()
                ->keyBy('tanggal');

            // Get expenses hari ini
            $expensesData = Expense::whereDate('tanggal_keluar', $today)
                ->selectRaw('DATE(tanggal_keluar) as tanggal, SUM(jumlah) as total_keluar')
                ->groupBy('tanggal')
                ->get()
                ->keyBy('tanggal');

            $filterMode = 'harian';
        }

        // Combine data untuk tabel
        $ledgerData = collect([]);
        $dates = $incomesData->keys()->merge($expensesData->keys())->unique()->sort();

        foreach ($dates as $date) {
            $ledgerData->push([
                'tanggal' => $date,
                'total_masuk' => $incomesData->has($date) ? $incomesData[$date]->total_masuk : 0,
                'total_keluar' => $expensesData->has($date) ? $expensesData[$date]->total_keluar : 0,
            ]);
        }

        // Calculate totals
        $todayTotalMasuk = $incomesData->sum('total_masuk');
        $todayTotalKeluar = $expensesData->sum('total_keluar');
        $todaySaldo = $todayTotalMasuk - $todayTotalKeluar;

        // Return JSON jika request AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'todayTotalMasuk' => $todayTotalMasuk,
                'todayTotalKeluar' => $todayTotalKeluar,
                'todaySaldo' => $todaySaldo,
                'ledgerData' => $ledgerData->values()->toArray(),
                'bulan' => $bulan,
                'tahun' => $tahun,
                'filterMode' => $filterMode
            ]);
        }

        return view('content.apps.Pembukuan.masuk.masuk', compact(
            'todayTotalMasuk',
            'todayTotalKeluar',
            'todaySaldo',
            'ledgerData',
            'startDate',
            'endDate',
            'bulan',
            'tahun',
            'filterMode'
        ));
    }

    public function keluar(Request $request)
    {
        $tanggal = $request->tanggal ? Carbon::parse($request->tanggal)->toDateString() : now()->toDateString();

        $expenses = Expense::whereDate('tanggal_keluar', $tanggal)->get();
        $totalKeluar = $expenses->sum('jumlah');

        // LedgerData untuk ringkasan bawah (misal: total pengeluaran per kategori/hari)
        $ledgerData = $expenses->map(function($e) {
            return [
                'kode' => $e->kode,
                'kategori' => $e->kategori,
                'jumlah' => $e->jumlah,
                'tanggal' => $e->tanggal_keluar,
                'keterangan' => $e->keterangan,
            ];
        });

        return view('content.apps.Pembukuan.keluar.keluar', compact('expenses', 'tanggal', 'totalKeluar', 'ledgerData'));
    }

    public function total(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        
        // Get Saldo Awal (default to empty model if null to prevent crashes)
        $saldoAwal = SaldoAwal::getByPeriod($bulan, $tahun) ?? new SaldoAwal();
        
        // Get first and last day of the month
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        
        // Get all incomes for the month
        $incomes = Income::whereBetween('tanggal_masuk', [$startDate, $endDate])
            ->orderBy('tanggal_masuk', 'asc')
            ->get();
        
        // Get all expenses for the month
        $expenses = Expense::whereBetween('tanggal_keluar', [$startDate, $endDate])
            ->orderBy('tanggal_keluar', 'asc')
            ->get();
        
        // ===== PEMASUKAN =====
        // Pemasukan Dedicated dari Tagihan (paket dengan nama mengandung "dedicated" atau "DAD" dan status lunas)
        $dedicatedData = $this->getDedicatedFromTagihan($startDate, $endDate);
        
        // 1. Kotor taken AUTOMATICALLY from Tagihan
        $pemasukanDedicatedKotor = $dedicatedData['kotor'];
        
        // 2. Potongan taken MANUALLY from Saldo Awal
        $potonganDedicated = $saldoAwal->pemasukan_dedicated_potongan ?? 0;
        
        // 3. Bersih = Kotor - Potongan
        $pemasukanDedicatedBersih = $pemasukanDedicatedKotor - $potonganDedicated;
        
        $jumlahDedicatedLunas = $dedicatedData['jumlah_lunas'];
        $jumlahDedicatedTotal = $dedicatedData['jumlah_total'];
        
        // Pemasukan Registrasi dan Home Net - MANUAL from DB
        $pemasukanRegistrasi = $saldoAwal->pemasukan_registrasi ?? 0;
        $pemasukanHomeNetKotor = $saldoAwal->pemasukan_homenet_kotor ?? 0;
        $potonganHomeNet = $saldoAwal->pemasukan_homenet_potongan ?? 0;
        $pemasukanHomeNetBersih = $saldoAwal->pemasukan_homenet_bersih ?? 0;
        
        // Total Pemasukan
        $totalPemasukan = $pemasukanRegistrasi + $pemasukanDedicatedBersih + $pemasukanHomeNetBersih;
        
        // ===== PENGELUARAN =====
        // Define all categories with their codes - show all individually
        $kategoriPengeluaran = [
            'BEBAN GAJI' => ['kode' => '202', 'jumlah' => 0],
            'ALAT KANTOR HABIS PAKAI' => ['kode' => '203', 'jumlah' => 0],
            'ALAT LOGISTIK' => ['kode' => '203', 'jumlah' => 0],
            'ALAT TULIS KANTOR' => ['kode' => '203', 'jumlah' => 0],
            'KONSUMSI' => ['kode' => '204', 'jumlah' => 0],
            'BEBAN TRANSPORTASI' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN PERAWATAN' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN LAT (LISTRIK, AIR, TELEPON)' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN KEPERLUAN RUMAH TANGGA' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN TAGIHAN INTERNET' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN LAIN-LAIN' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN KOMITMEN / FEE' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN PRIVE' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN SRAGEN' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN GUNUNGKIDUL' => ['kode' => '205', 'jumlah' => 0],
        ];
        
        // Sum expenses by kategori field
        foreach ($expenses as $expense) {
            $kategori = strtoupper(trim($expense->kategori ?? ''));
            if (isset($kategoriPengeluaran[$kategori])) {
                $kategoriPengeluaran[$kategori]['jumlah'] += $expense->jumlah;
            }
        }
        
        // Convert to array format for view
        $pengeluaranData = [];
        foreach ($kategoriPengeluaran as $nama => $data) {
            $pengeluaranData[] = [
                'kode' => $data['kode'],
                'kategori' => $nama,
                'jumlah' => $data['jumlah']
            ];
        }
        
        $totalPengeluaran = $expenses->sum('jumlah');
        
        // ===== PIUTANG (Manual from DB) =====
        $piutangDedicated = $saldoAwal->piutang_dedicated ?? 0;
        $piutangHomeNet = $saldoAwal->piutang_homenet ?? 0;
        $totalPiutang = $piutangDedicated + $piutangHomeNet;
        
        // ===== OMSET =====
        $omsetDedicated = $incomes->where('kategori', 'Dedicated')->where('status', 'Lunas')->sum('jumlah');
        $omsetKotor = $incomes->where('kategori', 'Home Net Kotor')->sum('jumlah');
        $potonganOmset = abs($incomes->where('kategori', 'Potongan Home Net')->sum('jumlah'));
        $omsetHomeNetBersih = $omsetKotor - $potonganOmset;
        $totalOmset = $omsetDedicated + $omsetHomeNetBersih;
        
        // Compile first month data
        $firstMonth = [
            'label' => Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM YYYY'),
            'saldoAwal' => $saldoAwal,
            'pemasukan' => [
                'registrasi' => $pemasukanRegistrasi,
                'dedicatedKotor' => $pemasukanDedicatedKotor,
                'potonganDedicated' => $potonganDedicated,
                'dedicatedBersih' => $pemasukanDedicatedBersih,
                'jumlahDedicatedLunas' => $jumlahDedicatedLunas,
                'jumlahDedicatedTotal' => $jumlahDedicatedTotal,
                'homeNetKotor' => $pemasukanHomeNetKotor,
                'potonganHomeNet' => $potonganHomeNet,
                'homeNetBersih' => $pemasukanHomeNetBersih,
            ],
            'totalPemasukan' => $totalPemasukan,
            'pengeluaran' => $pengeluaranData,
            'totalPengeluaran' => $totalPengeluaran,
            'piutang' => [
                'dedicated' => $piutangDedicated,
                'homeNet' => $piutangHomeNet,
            ],
            'totalPiutang' => $totalPiutang,
            'omset' => [
                'dedicated' => $omsetDedicated,
                'kotor' => $omsetKotor,
                'homeNetBersih' => $omsetHomeNetBersih,
            ],
            'totalOmset' => $totalOmset,
        ];
        
        return view('content.apps.Pembukuan.total.total', compact('firstMonth', 'saldoAwal', 'bulan', 'tahun'));
    }
    
    /**
     * Export data to Excel
     */
    public function exportExcel(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        
        // Get Saldo Awal
        $saldoAwal = SaldoAwal::getByPeriod($bulan, $tahun) ?? new SaldoAwal();
        
        // Get first and last day of the month
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        
        // Get all incomes and expenses
        $incomes = Income::whereBetween('tanggal_masuk', [$startDate, $endDate])->get();
        $expenses = Expense::whereBetween('tanggal_keluar', [$startDate, $endDate])->get();
        
        // Calculate Pemasukan
        $dedicatedData = $this->getDedicatedFromTagihan($startDate, $endDate);
        $pemasukanDedicatedKotor = $dedicatedData['kotor'];
        $potonganDedicated = $saldoAwal->pemasukan_dedicated_potongan ?? 0;
        $pemasukanDedicatedBersih = $pemasukanDedicatedKotor - $potonganDedicated;
        $pemasukanRegistrasi = $saldoAwal->pemasukan_registrasi ?? 0;
        $pemasukanHomeNetKotor = $saldoAwal->pemasukan_homenet_kotor ?? 0;
        $potonganHomeNet = $saldoAwal->pemasukan_homenet_potongan ?? 0;
        $pemasukanHomeNetBersih = $saldoAwal->pemasukan_homenet_bersih ?? 0;
        $totalPemasukan = $pemasukanRegistrasi + $pemasukanDedicatedBersih + $pemasukanHomeNetBersih;
        
        // Calculate Pengeluaran
        $kategoriPengeluaran = [
            'BEBAN GAJI' => ['kode' => '202', 'jumlah' => 0],
            'ALAT KANTOR HABIS PAKAI' => ['kode' => '203', 'jumlah' => 0],
            'ALAT LOGISTIK' => ['kode' => '203', 'jumlah' => 0],
            'ALAT TULIS KANTOR' => ['kode' => '203', 'jumlah' => 0],
            'KONSUMSI' => ['kode' => '204', 'jumlah' => 0],
            'BEBAN TRANSPORTASI' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN PERAWATAN' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN LAT (LISTRIK, AIR, TELEPON)' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN KEPERLUAN RUMAH TANGGA' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN TAGIHAN INTERNET' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN LAIN-LAIN' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN KOMITMEN / FEE' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN PRIVE' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN KOS-KOSAN' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN SRAGEN' => ['kode' => '205', 'jumlah' => 0],
            'BEBAN GUNUNGKIDUL' => ['kode' => '205', 'jumlah' => 0],
        ];
        
        foreach ($expenses as $expense) {
            $kategori = strtoupper(trim($expense->kategori ?? ''));
            if (isset($kategoriPengeluaran[$kategori])) {
                $kategoriPengeluaran[$kategori]['jumlah'] += $expense->jumlah;
            }
        }
        
        $pengeluaranData = [];
        foreach ($kategoriPengeluaran as $nama => $data) {
            if ($data['jumlah'] > 0) { // Only include non-zero items
                $pengeluaranData[] = [
                    'kode' => $data['kode'],
                    'kategori' => $nama,
                    'jumlah' => $data['jumlah']
                ];
            }
        }
        
        $totalPengeluaran = $expenses->sum('jumlah');
        
        // Piutang
        $piutangDedicated = $saldoAwal->piutang_dedicated ?? 0;
        $piutangHomeNet = $saldoAwal->piutang_homenet ?? 0;
        $totalPiutang = $piutangDedicated + $piutangHomeNet;
        
        // Compile data
        $data = [
            'label' => Carbon::createFromDate($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM YYYY'),
            'saldoAwal' => $saldoAwal,
            'pemasukan' => [
                'registrasi' => $pemasukanRegistrasi,
                'dedicatedKotor' => $pemasukanDedicatedKotor,
                'potonganDedicated' => $potonganDedicated,
                'dedicatedBersih' => $pemasukanDedicatedBersih,
                'homeNetKotor' => $pemasukanHomeNetKotor,
                'potonganHomeNet' => $potonganHomeNet,
                'homeNetBersih' => $pemasukanHomeNetBersih,
            ],
            'totalPemasukan' => $totalPemasukan,
            'pengeluaran' => $pengeluaranData,
            'totalPengeluaran' => $totalPengeluaran,
            'piutang' => [
                'dedicated' => $piutangDedicated,
                'homeNet' => $piutangHomeNet,
            ],
            'totalPiutang' => $totalPiutang,
        ];
        
        $filename = 'Rugi_Laba_' . Carbon::createFromDate($tahun, $bulan, 1)->format('F_Y') . '.xlsx';
        
        return Excel::download(new PembukuanTotalExport($data, $bulan, $tahun), $filename);
    }
    
    /**
     * Get Dedicated income from Tagihan table
     * Finds tagihan where paket nama contains "dedicated" or "DAD" and status is "Lunas"
     */
    private function getDedicatedFromTagihan($startDate, $endDate)
    {
        // Get paket IDs that contain "dedicated", "dadicated", or "DAD" in their name (case-insensitive)
        $dedicatedPaketIds = Paket::where('nama_paket', 'LIKE', '%dedicated%')
            ->orWhere('nama_paket', 'LIKE', '%dadicated%')
            ->orWhere('nama_paket', 'LIKE', '%dad%')
            ->pluck('id');
        
        // Get all tagihan with dedicated paket in the period
        $allDedicatedTagihan = Tagihan::whereIn('paket_id', $dedicatedPaketIds)
            ->whereBetween('tanggal_berakhir', [$startDate, $endDate])
            ->get();
        
        // Get paid (lunas) tagihan - case insensitive check
        $paidDedicatedTagihan = $allDedicatedTagihan->filter(function($t) {
            return strtolower($t->status_pembayaran) === 'lunas';
        });
        
        // Calculate totals
        $kotor = 0;
        foreach ($paidDedicatedTagihan as $tagihan) {
            if ($tagihan->paket) {
                $kotor += $tagihan->paket->harga;
            }
        }
        
        return [
            'kotor' => $kotor,
            'potongan' => 0, 
            'jumlah_lunas' => $paidDedicatedTagihan->count(),
            'jumlah_total' => $allDedicatedTagihan->count(),
        ];
    }

    private function processMonthlyData($incomes, $expenses)
    {
        $groupedIncomes = $incomes->groupBy(function($val) {
            return Carbon::parse($val->tanggal_masuk)->format('Y-m');
        });

        $groupedExpenses = $expenses->groupBy(function($val) {
            return Carbon::parse($val->tanggal_keluar)->format('Y-m');
        });

        $allMonths = $groupedIncomes->keys()->merge($groupedExpenses->keys())->unique()->sort();

        $monthlyData = [];
        $saldoAkumulasi = 0;

        foreach($allMonths as $month) {
            $monthIncomes = $groupedIncomes->get($month, collect());
            $monthExpenses = $groupedExpenses->get($month, collect());

            // OMSET
            $omsetDedicated = $monthIncomes->where('kategori', 'Dedicated')->where('status', 'Lunas')->sum('jumlah');
            $omsetKotor = $monthIncomes->where('kategori', 'Home Net Kotor')->sum('jumlah');
            $potonganOmset = abs($monthIncomes->where('kategori', 'Potongan Home Net')->sum('jumlah'));
            $omsetHomeNetBersih = $omsetKotor - $potonganOmset;
            $totalOmset = $omsetDedicated + $omsetHomeNetBersih;

            // PEMASUKAN
            $pemasukanRegistrasi = $monthIncomes->where('kategori', 'Registrasi')->sum('jumlah');
            $pemasukanDedicated = $monthIncomes->where('kategori', 'Dedicated')->where('status', 'Lunas')->sum('jumlah');
            $pemasukanHomeNetKotor = $monthIncomes->where('kategori', 'Home Net Kotor')->sum('jumlah');
            $potonganHomeNet = abs($monthIncomes->where('kategori', 'Potongan Home Net')->sum('jumlah'));
            $pemasukanHomeNetBersih = $pemasukanHomeNetKotor - $potonganHomeNet;
            $totalPemasukan = $pemasukanRegistrasi + $pemasukanDedicated + $pemasukanHomeNetBersih;

            // PENGELUARAN
            $bebanGaji = $monthExpenses->where('kode_akun', '202')->sum('jumlah');
            $alatLogistik = $monthExpenses->where('kode_akun', '203')->sum('jumlah');

            $pengeluaranLainnya = $monthExpenses->whereNotIn('kode_akun', ['202', '203'])
                ->groupBy('kode_akun')
                ->map(function($group) {
                    return [
                        'kode' => $group->first()->kode_akun,
                        'nama' => $group->first()->nama_akun,
                        'jumlah' => $group->sum('jumlah')
                    ];
                })->values()->toArray();

            $totalPengeluaran = $monthExpenses->sum('jumlah');

            // PIUTANG
            $piutangDedicated = $monthIncomes->where('kategori', 'Dedicated')->where('status', 'Piutang')->sum('jumlah');
            $piutangHomeNet = $monthIncomes->where('kategori', 'Home Net')->where('status', 'Piutang')->sum('jumlah');
            $totalPiutang = $piutangDedicated + $piutangHomeNet;

            $saldoBersih = $totalPemasukan - $totalPengeluaran;

            $monthlyData[$month] = [
                'label' => Carbon::parse($month.'-01')->locale('id')->isoFormat('MMMM YYYY'),
                'saldoAwal' => $saldoAkumulasi,
                'omset' => [
                    'dedicated' => $omsetDedicated,
                    'kotor' => $omsetKotor,
                    'homeNetBersih' => $omsetHomeNetBersih,
                ],
                'totalOmset' => $totalOmset,
                'pemasukan' => [
                    'registrasi' => $pemasukanRegistrasi,
                    'dedicated' => $pemasukanDedicated,
                    'homeNetKotor' => $pemasukanHomeNetKotor,
                    'potonganHomeNet' => $potonganHomeNet,
                    'homeNetBersih' => $pemasukanHomeNetBersih,
                ],
                'totalPemasukan' => $totalPemasukan,
                'pengeluaran' => [
                    '202_bebanGaji' => $bebanGaji,
                    '203_alatLogistik' => $alatLogistik,
                    'lainnya' => $pengeluaranLainnya
                ],
                'totalPengeluaran' => $totalPengeluaran,
                'piutang' => [
                    'dedicated' => $piutangDedicated,
                    'homeNet' => $piutangHomeNet,
                ],
                'totalPiutang' => $totalPiutang,
                'saldoBersih' => $saldoBersih,
            ];

            $saldoAkumulasi += $saldoBersih;
        }

        return $monthlyData;
    }
}

