<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the welcome dashboard page.
     */
    public function index()
    {
        $paket = Paket::all();
        
        // Statistik
        $totalCustomer = Pelanggan::where('status', 'approve')->count();
        $customerLunas = Tagihan::where('status_pembayaran', 'lunas')->count();
        $belumLunas = Tagihan::where('status_pembayaran', 'belum bayar')->count();
        $totalPaket = $paket->count();

        // Status Active/Inactive
        $activeCustomers = Pelanggan::whereHas('loginStatus', function($q) {
            $q->where('is_active', true);
        })->count();
        
        $inactiveCustomers = Pelanggan::where(function($q) {
            $q->whereHas('loginStatus', function($subQ) {
                $subQ->where('is_active', false);
            })->orWhereDoesntHave('loginStatus');
        })->count();

        return view('content.apps.Dashboard.welcome', [
            'totalCustomer' => $totalCustomer,
            'customerLunas' => $customerLunas,
            'belumLunas' => $belumLunas,
            'totalPaket' => $totalPaket,
            'activeCustomers' => $activeCustomers,
            'inactiveCustomers' => $inactiveCustomers,
        ]);
    }
}
