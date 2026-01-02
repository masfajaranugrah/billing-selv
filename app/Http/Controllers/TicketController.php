<?php

namespace App\Http\Controllers;

use App\Events\TicketCreated;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use App\Models\Ticket;
use App\Models\TicketStatusLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function ticketsJson()
    {
        $tickets = Ticket::with(['pelanggan', 'user', 'creator'])->latest()->get();

        $pelanggan = Pelanggan::all();
        $paket = Paket::all();
        $tagihans = Tagihan::with(['pelanggan', 'paket'])->latest()->get();

        $totalCustomer = $pelanggan->count();
        $lunas = $tagihans->where('status_pembayaran', 'lunas')->count();
        $belumLunas = $tagihans->where('status_pembayaran', 'belum bayar')->count();
        $totalPaket = $paket->count();

        return response()->json([
            'tickets' => $tickets,
            'totalCustomer' => $totalCustomer,
            'lunas' => $lunas,
            'belumLunas' => $belumLunas,
            'totalPaket' => $totalPaket,
        ]);
    }

public function index()
{
    // Ambil semua pelanggan & paket untuk dropdown modal
    $pelanggan = Pelanggan::all();
    $paket = Paket::all();

    // Ambil semua tagihan beserta relasinya
    $tagihans = Tagihan::with(['pelanggan', 'paket'])->latest()->get();
    $kabupatenList = Pelanggan::distinct()->pluck('kabupaten');
    $kecamatanList = Pelanggan::distinct()->pluck('kecamatan');
    
    // Statistik
    $totalCustomer = $pelanggan->count();
    $lunas = $tagihans->where('status_pembayaran', 'lunas')->count();
    $belumLunas = $tagihans->where('status_pembayaran', 'belum bayar')->count();
    $totalPaket = $paket->count();

    // ? FILTER HANYA STATUS 'pending'
    $tickets = Ticket::with(['user', 'creator'])
        ->where('status', 'pending')
        ->latest()
        ->get();

    return view('content.apps.Ticket.ticket', compact(
        'tickets',
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

    public function create()
    {

        // Ambil semua pelanggan & paket untuk dropdown modal
        $pelanggan = Pelanggan::all();
        $paket = Paket::all();

        // Ambil semua tagihan beserta relasinya
        $tagihans = Tagihan::with(['pelanggan', 'paket'])->latest()->get();
        $kabupatenList = Pelanggan::distinct()->pluck('kabupaten');
        $kecamatanList = Pelanggan::distinct()->pluck('kecamatan');
        // Statistik
        $totalCustomer = $pelanggan->count(); // jumlah pelanggan
        $lunas = $tagihans->where('status_pembayaran', 'lunas')->count(); // jumlah tagihan lunas
        $belumLunas = $tagihans->where('status_pembayaran', 'belum bayar')->count(); // jumlah tagihan belum lunas
        $totalPaket = $paket->count(); // jumlah paket

        // Ambil semua tiket terbaru, bisa ditambahkan pagination jika perlu

        // Ambil semua user dengan role 'team' untuk dijadikan pilihan penugas
        $users = User::where('role', 'team')->get();

        return view('content.apps.Ticket.add-ticket', compact(
            'users',
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

     public function store(Request $request)
{
    $request->validate([
        'pelanggan_id' => 'required|exists:pelanggans,id',
        'location_link' => 'nullable|url',
        'category' => 'nullable|string|max:50',
        'issue_description' => 'required|string',
        'additional_note' => 'nullable|string',
        'cs_note' => 'nullable|string',
        'cs_attachment' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'priority' => 'required|in:urgent,medium,low',
        // UBAH: user_id jadi nullable (tidak wajib)
        'user_id' => 'nullable|exists:users,id',
        // UBAH: status jadi nullable dengan default pending
        'status' => 'nullable|in:pending,assigned,progress,finished,approved,rejected',
        'complaint_source' => 'nullable|in:whatsapp,telepon,datang,email,app',
    ]);

    $pelanggan = Pelanggan::findOrFail($request->pelanggan_id);

    // Upload foto CS jika ada
    $csAttachment = null;
    if ($request->hasFile('cs_attachment')) {
        $csAttachment = $request->file('cs_attachment')->store('tickets/cs', 'public');
    }

    // Simpan ticket - CS hanya create, belum assign teknisi
    $ticket = Ticket::create([
        'pelanggan_id' => $pelanggan->id,
        'phone' => $pelanggan->no_whatsapp ?? $pelanggan->no_telp,
        'location_link' => $request->location_link,
        'category' => $request->category,
        'issue_description' => $request->issue_description,
        'additional_note' => $request->additional_note,
        'cs_note' => $request->cs_note,
        'attachment' => $csAttachment,
        'complaint_source' => $request->complaint_source ?? 'whatsapp',
        'priority' => $request->priority,
        'status' => 'pending', // Default pending, nanti admin yang assign
        'user_id' => null, // Belum ada teknisi
        'created_by' => Auth::id(),
        'technician_attachment' => null,
    ]);
    
    event(new TicketCreated($ticket));
    
    // Log status awal
    TicketStatusLog::create([
        'ticket_id' => $ticket->id,
        'status' => $ticket->status,
        'user_id' => Auth::id(),
    ]);

    return redirect()->route('tickets.indexs')
        ->with('success', 'Ticket berhasil dibuat. Menunggu assignment teknisi.');
}

    public function edit(Ticket $ticket)
    {
        // Ambil semua pelanggan & paket untuk dropdown modal
        $pelanggan = Pelanggan::all();
        $paket = Paket::all();

        // Ambil semua tagihan beserta relasinya
        $tagihans = Tagihan::with(['pelanggan', 'paket'])->latest()->get();
        $kabupatenList = Pelanggan::distinct()->pluck('kabupaten');
        $kecamatanList = Pelanggan::distinct()->pluck('kecamatan');
        // Statistik
        $totalCustomer = $pelanggan->count(); // jumlah pelanggan
        $lunas = $tagihans->where('status_pembayaran', 'lunas')->count(); // jumlah tagihan lunas
        $belumLunas = $tagihans->where('status_pembayaran', 'belum bayar')->count(); // jumlah tagihan belum lunas
        $totalPaket = $paket->count(); // jumlah paket

        // Ambil semua tiket terbaru, bisa ditambahkan pagination jika perlu

        // Ambil semua user role 'team' untuk dropdown
        $users = User::where('role', 'team')->get();

        return view('content.apps.Ticket.edit-ticket', compact(
            'ticket',
            'users',
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



public function finished()
{
    // Ambil semua pelanggan & paket untuk dropdown modal
    $pelanggan = Pelanggan::all();
    $paket = Paket::all();

    // Ambil semua tagihan beserta relasinya
    $tagihans = Tagihan::with(['pelanggan', 'paket'])->latest()->get();
    $kabupatenList = Pelanggan::distinct()->pluck('kabupaten');
    $kecamatanList = Pelanggan::distinct()->pluck('kecamatan');
    
    // Statistik
    $totalCustomer = $pelanggan->count();
    $lunas = $tagihans->where('status_pembayaran', 'lunas')->count();
    $belumLunas = $tagihans->where('status_pembayaran', 'belum bayar')->count();
    $totalPaket = $paket->count();

    // ? TICKET YANG STATUS 'finished' SAJA
    $tickets = Ticket::with(['user', 'creator'])
        ->where('status', 'finished')
        ->latest()
        ->get();

    return view('content.apps.Ticket.ticket-finished', compact(
        'tickets',
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







public function approved()
{
    // Ambil semua pelanggan & paket untuk dropdown modal
    $pelanggan = Pelanggan::all();
    $paket = Paket::all();

    // Ambil semua tagihan beserta relasinya
    $tagihans = Tagihan::with(['pelanggan', 'paket'])->latest()->get();
    $kabupatenList = Pelanggan::distinct()->pluck('kabupaten');
    $kecamatanList = Pelanggan::distinct()->pluck('kecamatan');
    
    // Statistik
    $totalCustomer = $pelanggan->count();
    $lunas = $tagihans->where('status_pembayaran', 'lunas')->count();
    $belumLunas = $tagihans->where('status_pembayaran', 'belum bayar')->count();
    $totalPaket = $paket->count();

    // ? TICKET YANG STATUS 'approved' SAJA
    $tickets = Ticket::with(['user', 'creator'])
        ->where('status', 'approved')
        ->latest()
        ->get();

    return view('content.apps.Ticket.ticket-approved', compact(
        'tickets',
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







    public function update(Request $request, Ticket $ticket)
    {
        // Validasi
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'phone' => 'nullable|string|max:20',
            'location_link' => 'nullable|url',
            'category' => 'nullable|string|max:50',
            'issue_description' => 'required|string',
            'additional_note' => 'nullable|string',
            'cs_note' => 'nullable|string',
            'cs_attachment' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'priority' => 'required|in:urgent,medium,low',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,assigned,progress,finished,approved,rejected',
        ]);

        // Hapus & upload cs_attachment baru jika ada
        if ($request->hasFile('cs_attachment')) {
            if ($ticket->cs_attachment) {
                \Storage::disk('public')->delete($ticket->cs_attachment);
            }
            $ticket->cs_attachment = $request->file('cs_attachment')->store('tickets/cs', 'public');
        }
        $pelanggan = Pelanggan::findOrFail($request->pelanggan_id);

        // Simpan update ticket
        $ticket->update([
            'pelanggan_id' => $request->pelanggan_id,
            'customer_name' => optional($request->pelanggan_id ? \App\Models\Pelanggan::find($request->pelanggan_id) : null)->nama_lengkap ?? $request->customer_name,
            'phone' => $request->phone,
            'location_link' => $request->location_link,
            'category' => $request->category,
            'issue_description' => $request->issue_description,
            'additional_note' => $request->additional_note,
            'cs_note' => $request->cs_note,
            'cs_attachment' => $ticket->cs_attachment ?? $ticket->cs_attachment,
            'priority' => $request->priority,
            'user_id' => $request->user_id,
            'status' => $request->status,
        ]);

        // Tambahkan log status baru
        TicketStatusLog::create([
            'ticket_id' => $ticket->id,
            'status' => $request->status,
            'user_id' => Auth::id(), // siapa yang update
        ]);

        return redirect()->route('tickets.indexs')->with('success', 'Ticket berhasil diperbarui dan status tercatat.');
    }

    public function destroy(Ticket $ticket)
    {
        if ($ticket->cs_attachment) {
            \Storage::disk('public')->delete($ticket->cs_attachment);
        }
        $ticket->delete();

        return redirect()->route('tickets.indexs')->with('success', 'Ticket berhasil dihapus');
    }
}
