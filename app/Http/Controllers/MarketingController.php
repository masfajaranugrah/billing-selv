<?php

namespace App\Http\Controllers;

use App\Imports\PelangganImport;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class MarketingController extends Controller
{
    public function index()
    {
        $pelanggan = Pelanggan::with(['paket', 'user'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('content.apps.Marketing.pelanggan', compact('pelanggan'));
    }

    public function getDataAprove()
    {
        $pelanggan = Pelanggan::with(['paket', 'user'])
            ->where('status', 'approve')
            ->where('user_id', Auth::id())
            ->get();

        $pelanggan = $pelanggan->values()->map(function ($item, $index) {
            $item->nomor_urut = $index + 1;
            return $item;
        });

        return response()->json([
            'data' => $pelanggan,
        ]);
    }

    public function status()
    {
        $pelanggan = Pelanggan::with(['paket', 'user'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('content.apps.Marketing.status-pelanggan', compact('pelanggan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_ktp' => 'nullable|string|max:50',
            'no_whatsapp' => 'nullable|string|max:50',
            'no_telp' => 'nullable|string|max:50',
            'alamat_jalan' => 'nullable|string|max:255',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'desa' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'paket_id' => 'required|exists:pakets,id',
            'nomer_id' => 'required|string|max:50|unique:pelanggans,nomer_id',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',
            'deskripsi' => 'nullable|string',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg,webp,heic|max:10240',
        ]);

        DB::beginTransaction();

        try {
            $fotoKtpPath = null;
            
            if ($request->hasFile('foto_ktp')) {
                $fotoKtpPath = $request->file('foto_ktp')->store('foto_ktp', 'public');
            }

            $paket = Paket::findOrFail($validated['paket_id']);
            $tanggalMulai = $validated['tanggal_mulai'] ?? now();
            $tanggalBerakhir = $validated['tanggal_berakhir'] ?? now()->addDays($paket->masa_pembayaran);

            Pelanggan::create([
                'user_id' => Auth::id(),
                'nama_lengkap' => $validated['nama_lengkap'],
                'no_ktp' => $validated['no_ktp'] ?? null,
                'no_whatsapp' => $validated['no_whatsapp'] ?? null,
                'no_telp' => $validated['no_telp'] ?? null,
                'alamat_jalan' => $validated['alamat_jalan'] ?? null,
                'rt' => $validated['rt'] ?? null,
                'rw' => $validated['rw'] ?? null,
                'desa' => $validated['desa'] ?? null,
                'kecamatan' => $validated['kecamatan'] ?? null,
                'kabupaten' => $validated['kabupaten'] ?? null,
                'provinsi' => $validated['provinsi'] ?? null,
                'kode_pos' => $validated['kode_pos'] ?? null,
                'paket_id' => $paket->id,
                'nomer_id' => $validated['nomer_id'],
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_berakhir' => $tanggalBerakhir,
                'deskripsi' => $validated['deskripsi'] ?? null,
                'foto_ktp' => $fotoKtpPath,
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('marketing.pelanggan')->with('success', '? Pelanggan baru berhasil dibuat!');
            
        } catch (\Throwable $th) {
            DB::rollBack();
            
            if (isset($fotoKtpPath) && Storage::disk('public')->exists($fotoKtpPath)) {
                Storage::disk('public')->delete($fotoKtpPath);
            }

            return back()->with('error', '? Terjadi kesalahan: ' . $th->getMessage())->withInput();
        }
    }

    public function updateSid(Request $request, $nomerid)
    {
        $request->validate([
            'sid' => 'required|string',
        ]);

        $pelanggan = Pelanggan::where('nomer_id', $nomerid)->first();

        if (!$pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan',
            ], 404);
        }

        $pelanggan->update([
            'webpushr_sid' => $request->sid,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'SID berhasil disimpan',
            'data' => [
                'nomerid' => $pelanggan->nomer_id,
                'sid' => $request->sid,
            ],
        ]);
    }

    public function create()
    {
        $paket = Paket::all();
        return view('content.apps.Marketing.add-pelanggan', compact('paket'));
    }

    public function edit($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $paket = Paket::all();

        return view('content.apps.Marketing.edit-pelanggan', compact('pelanggan', 'paket'));
    }

    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_ktp' => 'nullable|string|max:50',
            'no_whatsapp' => 'nullable|string|max:50',
            'no_telp' => 'nullable|string|max:50',
            'alamat_jalan' => 'nullable|string|max:255',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'desa' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'paket_id' => 'required|exists:pakets,id',
            'nomer_id' => 'required|string|max:50|unique:pelanggans,nomer_id,' . $pelanggan->id,
            'tanggal_mulai' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date',
            'deskripsi' => 'nullable|string',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg,webp,heic|max:10240',
 
        ]);

        DB::beginTransaction();

        try {
            $paket = Paket::findOrFail($validated['paket_id']);
            $tanggalMulai = $validated['tanggal_mulai'] ?? now();
            $tanggalBerakhir = $validated['tanggal_berakhir'] ?? now()->parse($tanggalMulai)->addDays($paket->masa_pembayaran);

            if ($request->hasFile('foto_ktp')) {
                // Hapus foto lama jika ada
                if ($pelanggan->foto_ktp && Storage::disk('public')->exists($pelanggan->foto_ktp)) {
                    Storage::disk('public')->delete($pelanggan->foto_ktp);
                }
                
                // Upload foto baru
                $validated['foto_ktp'] = $request->file('foto_ktp')->store('foto_ktp', 'public');
            }

            $pelanggan->update([
                'nama_lengkap' => $validated['nama_lengkap'],
                'no_ktp' => $validated['no_ktp'] ?? null,
                'no_whatsapp' => $validated['no_whatsapp'] ?? null,
                'no_telp' => $validated['no_telp'] ?? null,
                'alamat_jalan' => $validated['alamat_jalan'] ?? null,
                'rt' => $validated['rt'] ?? null,
                'rw' => $validated['rw'] ?? null,
                'desa' => $validated['desa'] ?? null,
                'kecamatan' => $validated['kecamatan'] ?? null,
                'kabupaten' => $validated['kabupaten'] ?? null,
                'provinsi' => $validated['provinsi'] ?? null,
                'kode_pos' => $validated['kode_pos'] ?? null,
                'paket_id' => $paket->id,
                'nomer_id' => $validated['nomer_id'],
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_berakhir' => $tanggalBerakhir,
                'deskripsi' => $validated['deskripsi'] ?? null,
                'foto_ktp' => $validated['foto_ktp'] ?? $pelanggan->foto_ktp,
 
            ]);

            DB::commit();

            return redirect()->route('marketing.pelanggan')->with('success', '? Data pelanggan berhasil diperbarui!');
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', '? Terjadi kesalahan: ' . $th->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        
        // Hapus foto KTP jika ada
        if ($pelanggan->foto_ktp && Storage::disk('public')->exists($pelanggan->foto_ktp)) {
            Storage::disk('public')->delete($pelanggan->foto_ktp);
        }
        
        $pelanggan->delete();

        return redirect()->back()->with('success', 'Data pelanggan berhasil dihapus.');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new PelangganImport, $request->file('file'));

        return redirect()->route('marketing.pelanggan')->with('success', '? Data Excel berhasil diimport!');
    }
}
