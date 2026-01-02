<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;

use App\Models\LoginLog;
use App\Models\Pelanggan;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;

class AuthController extends Controller
{
    public function indexLogin()
    {
        $pageConfigs = ['myLayout' => 'blank'];

        return view('content.apps.Auth.login', ['pageConfigs' => $pageConfigs]);
    }

    public function loginMember()
    {
        $pageConfigs = ['myLayout' => 'blank'];

        return view('content.apps.Auth.member.login', ['pageConfigs' => $pageConfigs]);
    }

    // public function loginMem(Request $request)
    // {

    //     $request->validate([
    //         'nomer_id' => 'required|exists:pelanggans,nomer_id',
    //     ]);

    //     // Ambil user berdasarkan nomer_id
    //     $pelanggan = Pelanggan::where('nomer_id', $request->nomer_id)->first();

    //     // Login user pakai guard default (web)
    //     Auth::guard('customer')->login($pelanggan);

    //     // Redirect ke dashboard member
    //     return redirect('dashboard/customer/tagihan')->with('success', 'Registrasi berhasil! Selamat datang, ');
    // }


public function loginMem(Request $request)
{
    $request->validate([
        'login_input' => 'required|string',
    ]);

    $input = trim($request->login_input);
    
    // Cek apakah input adalah nomor (WhatsApp) atau alphanumeric (Nomer ID)
    if (preg_match('/^[0-9]+$/', $input)) {
        // Input adalah nomor telepon
        $inputPhone = preg_replace('/[^0-9]/', '', $input);
        
        // Buat variasi format nomor telepon
        $phoneVariations = [];
        if (substr($inputPhone, 0, 1) === '0') {
            $phoneVariations[] = $inputPhone;
            $phoneVariations[] = '62' . substr($inputPhone, 1);
        } elseif (substr($inputPhone, 0, 2) === '62') {
            $phoneVariations[] = $inputPhone;
            $phoneVariations[] = '0' . substr($inputPhone, 2);
        } elseif (substr($inputPhone, 0, 1) === '8') {
            $phoneVariations[] = '0' . $inputPhone;
            $phoneVariations[] = '62' . $inputPhone;
        } else {
            $phoneVariations[] = $inputPhone;
        }
        
        // Cari berdasarkan nomor WhatsApp
        $pelanggan = Pelanggan::whereIn('no_whatsapp', $phoneVariations)->first();
        
    } else {
        // Input adalah Nomer ID (alphanumeric)
        $pelanggan = Pelanggan::where('nomer_id', $input)->first();
    }

    if (!$pelanggan) {
        return back()->withErrors([
            'login_input' => 'Nomor WhatsApp atau Nomer ID tidak terdaftar.',
        ])->withInput();
    }

    $remember = $request->boolean('remember');

    // Login pelanggan
    Auth::guard('customer')->login($pelanggan, $remember);

    // Update status
    Status::updateOrCreate(
        ['pelanggan_id' => $pelanggan->id],
        ['is_active' => true, 'logged_in_at' => now()]
    );

    // Update token dan timestamp jika remember me dicentang
    if ($remember) {
        $pelanggan->update([
            'member_token' => \Illuminate\Support\Str::random(64),
            'is_member_active' => true,
            'last_login_at' => now(),
        ]);
    }

    return redirect()->route('customer.tagihan.home')
        ->with('success', 'Login berhasil!');
}




    public function indexRegister()
    {
        $pageConfigs = ['myLayout' => 'blank'];

        return view('content.apps.Auth.register', ['pageConfigs' => $pageConfigs]);
    }

    // Register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Set default role 'marketing' karena form tidak ada pilihan role
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Auto login setelah register
        Auth::login($user);

        return redirect('/dashboard/admin/tagihan')->with('success', 'Registrasi berhasil! Selamat datang, '.$user->name);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {

            $request->session()->regenerate();

            // Log aktivitas login
            $agent = new Agent;
            LoginLog::create([
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'browser' => $agent->browser(),
                'platform' => $agent->platform(),
                'device' => $agent->device(),
            ]);

            $user = Auth::user();

            // Redirect berdasarkan role
            switch ($user->role) {

                case 'administrator':
                    return redirect('/dashboard/admin/tagihan');

                case 'admin':
                    return redirect('/dashboard/admin/tagihan');

                case 'marketing':
                    return redirect('/dashboard/marketing/pelanggan');

                case 'customer_service':
                    return redirect('/dashboard/cs/tickets');

                case 'team':
                    return redirect('/dashboard/teknisi/jobs');

                case 'karyawan':
                    return redirect('/dashboard/karyawan/absensi');
                case 'logistic':
                    return redirect('/dashboard/admin/barangs');

                default:
                    Auth::logout();

                    return redirect()->route('login')->withErrors([
                        'email' => 'Role tidak valid.',
                    ]);
            }
        }

        return redirect()->route('login')
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau password salah']);
    }

    // Logout 
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/dashboard/auth/login')->with('success', 'Berhasil logout.');
    }

    // Logout customer (guard 'customer')
    public function logoutCustomer(Request $request)
    {
        $pelanggan = Auth::guard('customer')->user();

        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Hapus status aktif
        if ($pelanggan) {
            Status::where('pelanggan_id', $pelanggan->id)->delete();
        }

        return redirect('/pelanggan/jernihnet/login')->with('success', 'Berhasil logout.');
    }
}
