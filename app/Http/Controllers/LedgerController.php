<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Carbon\CarbonPeriod; 
 use Illuminate\Support\Facades\DB;

 
use Illuminate\Http\Request;

class LedgerController extends Controller
{
 
 
    

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

        return view('content.apps.Pembukuan.keluar.keluar', compact('expenses', 'tanggal', 'totalKeluar'));
    }





 public function total(Request $request) 
    {
        $periode = $request->get('periode', 'semua');
        
        switch($periode) {
            case 'hari_ini':
                $tanggalAwal = now()->startOfDay();
                $tanggalAkhir = now()->endOfDay();
                break;
            case '7_hari':
                $tanggalAwal = now()->subDays(6)->startOfDay();
                $tanggalAkhir = now()->endOfDay();
                break;
            case 'bulan_ini':
                $tanggalAwal = now()->startOfMonth();
                $tanggalAkhir = now()->endOfMonth();
                break;
            case 'tahun_ini':
                $tanggalAwal = now()->startOfYear();
                $tanggalAkhir = now()->endOfYear();
                break;
            case 'custom':
                $tanggalAwal = $request->tanggal_awal ? Carbon::parse($request->tanggal_awal)->startOfDay() : now()->startOfYear();
                $tanggalAkhir = $request->tanggal_akhir ? Carbon::parse($request->tanggal_akhir)->endOfDay() : now()->endOfYear();
                break;
            case 'semua':
            default:
                $incomes = Income::orderBy('tanggal_masuk', 'asc')->get();
                $expenses = Expense::orderBy('tanggal_keluar', 'asc')->get();
                
                $monthlyData = $this->processMonthlyData($incomes, $expenses);
                return view('content.apps.Pembukuan.total.total', compact('monthlyData', 'periode'));
        }

        $incomes = Income::whereBetween('tanggal_masuk', [$tanggalAwal, $tanggalAkhir])
                         ->orderBy('tanggal_masuk', 'asc')
                         ->get();

        $expenses = Expense::whereBetween('tanggal_keluar', [$tanggalAwal, $tanggalAkhir])
                           ->orderBy('tanggal_keluar', 'asc')
                           ->get();

        $monthlyData = $this->processMonthlyData($incomes, $expenses);

        return view('content.apps.Pembukuan.total.total', compact(
            'monthlyData',
            'tanggalAwal',
            'tanggalAkhir',
            'periode'
        ));
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