<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use App\Models\LedgerDaily;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::all();

        return view('content.apps.Laba.keluar.keluar', compact('expenses'));
    }

    public function create()
    {
        $kategori_default = ['Gaji', 'Transportasi', 'Internet', 'DLL'];

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
        $kategori = $request->kategori === 'DLL' && $request->kategori_dll
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
        $kategori_default = ['Gaji', 'Transportasi', 'Internet', 'DLL'];

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

        $kategori = $request->kategori === 'DLL' && $request->kategori_dll
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
        return match (strtolower($kategori)) {
            'pembelian' => '01',
            'jasa' => '02',
            'gaji' => '03',
            'internet' => '04',
            'transportasi' => '05',
            default => '06',
        };
    }
}
