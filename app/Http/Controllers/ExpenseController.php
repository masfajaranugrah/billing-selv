<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\LedgerDaily;
use App\Exports\ExpenseMonthlyExport;
use App\Exports\ExpenseDateRangeExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExpenseController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $expenses = Expense::all();
        
        // Kategori untuk cards
        $kategori_list = [
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
        
        // Hitung total per kategori untuk hari ini
        $todayTotals = [];
        foreach ($kategori_list as $nama => $kode) {
            $todayTotals[$nama] = Expense::whereDate('tanggal_keluar', $today)
                ->where('kategori', $nama)
                ->sum('jumlah');
        }
        
        // Total keseluruhan hari ini
        $totalHariIni = Expense::whereDate('tanggal_keluar', $today)->sum('jumlah');

        return view('content.apps.Laba.keluar.keluar', compact('expenses', 'kategori_list', 'todayTotals', 'totalHariIni', 'today'));
    }

    public function create()
    {
        $kategori_default = [
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
            'DLL (Lainnya)' => '206',
        ];

        return view('content.apps.Laba.keluar.add-keluar', compact('kategori_default'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string',
            'jumlah' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'kategori_dll' => 'nullable|string',
            'tanggal_keluar' => 'required|date',
        ]);

        // Tentukan kategori final
        $kategori = str_contains($request->kategori, 'DLL') && $request->kategori_dll
            ? $request->kategori_dll
            : $request->kategori;

        // Generate kode
        $kode = $this->getKode($kategori);

        // Bersihkan format rupiah dari input jumlah
        $jumlahBersih = str_replace('.', '', $request->jumlah);

        // Parse tanggal keluar
        $tanggalKeluar = Carbon::parse($request->tanggal_keluar);

        Expense::create([
            'kategori' => $kategori,
            'jumlah' => $jumlahBersih,
            'keterangan' => $request->keterangan,
            'kode' => $kode,
            'tanggal_keluar' => $tanggalKeluar,
            'created_at' => $tanggalKeluar,
            'updated_at' => $tanggalKeluar,
        ]);

        // Update ledger
        $this->updateLedger($tanggalKeluar->toDateString());

        return redirect()->route('keluar.index')->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $kategori_default = [
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
            'DLL (Lainnya)' => '206',
        ];

        return view('content.apps.Laba.keluar.edit-keluar', compact('expense', 'kategori_default'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori' => 'required|string',
            'jumlah' => 'required|numeric',
            'keterangan' => 'nullable|string',
            'kategori_dll' => 'nullable|string',
            'tanggal_keluar' => 'required|date',
        ]);

        $expense = Expense::findOrFail($id);

        $kategori = str_contains($request->kategori, 'DLL') && $request->kategori_dll
            ? $request->kategori_dll
            : $request->kategori;

        // Simpan tanggal lama
        $tanggalSebelumnya = Carbon::parse($expense->tanggal_keluar)->toDateString();

        // Parse tanggal keluar baru
        $tanggalKeluarBaru = Carbon::parse($request->tanggal_keluar);

        // Bersihkan format rupiah dari input jumlah
        $jumlahBersih = str_replace('.', '', $request->jumlah);

        $expense->update([
            'kategori' => $kategori,
            'jumlah' => $jumlahBersih,
            'keterangan' => $request->keterangan,
            'tanggal_keluar' => $tanggalKeluarBaru,
        ]);

        // Update ledger untuk tanggal lama dan tanggal baru
        $this->updateLedger($tanggalSebelumnya);
        $this->updateLedger($tanggalKeluarBaru->toDateString());

        return redirect()->route('keluar.index')->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $tanggal = Carbon::parse($expense->tanggal_keluar)->toDateString();
        $expense->delete();

        // Update ledger
        $this->updateLedger($tanggal);

        return redirect()->route('keluar.index')->with('success', 'Pengeluaran berhasil dihapus.');
    }

    /**
     * Update ledger harian otomatis sesuai tanggal
     */
    private function updateLedger($tanggal)
    {
        $ledger = LedgerDaily::firstOrCreate(['tanggal' => $tanggal]);

        $ledger->total_masuk = Income::whereDate('tanggal_masuk', $tanggal)->sum('jumlah');
        $ledger->total_keluar = Expense::whereDate('tanggal_keluar', $tanggal)->sum('jumlah');
        $ledger->saldo = $ledger->total_masuk - $ledger->total_keluar;

        $ledger->save();
    }

    private function getKode($kategori)
    {
        return match ($kategori) {
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
            default => '206',
        };
    }

    /**
     * Export laporan bulanan ke Excel
     */
    public function exportMonthly(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        
        $monthName = Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F');
        $filename = 'Laporan_Pengeluaran_' . $monthName . '_' . $year . '.xlsx';
        
        return Excel::download(new ExpenseMonthlyExport($month, $year), $filename);
    }

    /**
     * Export laporan per rentang tanggal ke Excel
     */
    public function exportDateRange(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $filename = 'Detail_Pengeluaran_' . Carbon::parse($startDate)->format('d-m-Y') . '_sd_' . Carbon::parse($endDate)->format('d-m-Y') . '.xlsx';
        
        return Excel::download(new ExpenseDateRangeExport($startDate, $endDate), $filename);
    }
}
