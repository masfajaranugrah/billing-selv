<?php

namespace App\Http\Controllers;

use App\Models\SaldoAwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SaldoAwalController extends Controller
{
    /**
     * Get saldo awal by period (month/year)
     */
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        
        $saldoAwal = SaldoAwal::getByPeriod($bulan, $tahun);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $saldoAwal
            ]);
        }
        
        // return view('content.apps.Pembukuan.saldo-awal.index', compact('saldoAwal', 'bulan', 'tahun'));
        return redirect()->route('pembukuan.total', ['bulan' => $bulan, 'tahun' => $tahun]);
    }

    /**
     * Store or update saldo awal
     */
    public function store(Request $request)
    {
        // Clean currency format first before validation
        $rawDedicated = $request->omset_dedicated ?? '0';
        $rawHomenetKotor = $request->omset_homenet_kotor ?? '0';
        $rawHomenetBersih = $request->omset_homenet_bersih ?? '0';

        // Remove dots and commas (support both 1.000.000 and 1,000,000 formats by just keeping digits)
        $omsetDedicated = preg_replace('/[^\d]/', '', $rawDedicated);
        $omsetHomenetKotor = preg_replace('/[^\d]/', '', $rawHomenetKotor);
        $omsetHomenetBersih = preg_replace('/[^\d]/', '', $rawHomenetBersih);

        // Merge cleaned values back to request for validation
        $request->merge([
            'bulan' => (int) $request->bulan,
            'tahun' => (int) $request->tahun,
            'omset_dedicated' => $omsetDedicated,
            'omset_homenet_kotor' => $omsetHomenetKotor,
            'omset_homenet_bersih' => $omsetHomenetBersih,
        ]);

        $validator = Validator::make($request->all(), [
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020|max:2100',
            'omset_dedicated' => 'required|numeric|min:0',
            'omset_homenet_kotor' => 'required|numeric|min:0',
            'omset_homenet_bersih' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Update or create
            $saldoAwal = SaldoAwal::updateOrCreate(
                [
                    'bulan' => $request->bulan,
                    'tahun' => $request->tahun,
                ],
                [
                    'omset_dedicated' => $omsetDedicated,
                    'omset_homenet_kotor' => $omsetHomenetKotor,
                    'omset_homenet_bersih' => $omsetHomenetBersih,
                ]
            );

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Saldo Awal berhasil disimpan',
                    'data' => $saldoAwal
                ]);
            }

            return redirect()->route('pembukuan.total', ['bulan' => $request->bulan, 'tahun' => $request->tahun])
                ->with('success', 'Saldo Awal berhasil disimpan');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Gagal menyimpan data')->withInput();
        }
    }

    /**
     * Update saldo awal
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'omset_dedicated' => 'required|numeric|min:0',
            'omset_homenet_kotor' => 'required|numeric|min:0',
            'omset_homenet_bersih' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $saldoAwal = SaldoAwal::findOrFail($id);

        // Clean currency format
        $omsetDedicated = str_replace(['.', ','], ['', '.'], $request->omset_dedicated);
        $omsetHomenetKotor = str_replace(['.', ','], ['', '.'], $request->omset_homenet_kotor);
        $omsetHomenetBersih = str_replace(['.', ','], ['', '.'], $request->omset_homenet_bersih);

        $saldoAwal->update([
            'omset_dedicated' => $omsetDedicated,
            'omset_homenet_kotor' => $omsetHomenetKotor,
            'omset_homenet_bersih' => $omsetHomenetBersih,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Saldo Awal berhasil diperbarui',
                'data' => $saldoAwal
            ]);
        }

        return redirect()->route('pembukuan.total')->with('success', 'Saldo Awal berhasil diperbarui');
    }
}
