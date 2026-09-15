<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    // ===== REGISTER: BELUM MASUK DB, SIMPAN DI SESSION =====
    public function register(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:100',
            'email'    => 'required|email|max:100|unique:users,email',
            'no_hp'    => 'required|string|max:20',
            'alamat'   => 'required|string',
            'password' => 'required|min:6|confirmed',
        ], [
            'email.unique'       => 'Email sudah terdaftar, silakan login.',
            'password.min'       => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $code = str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);

        session([
            'pending_register' => [
                'data'       => $request->only(['nama', 'email', 'no_hp', 'alamat', 'password']),
                'code'       => $code,
                'expired_at' => now()->addMinutes(10)->timestamp,
            ],
        ]);

        try {
            Mail::html(view('emails.verification', ['nama' => $request->nama, 'code' => $code])->render(), function ($message) use ($request) {
                $message->to($request->email)->subject('Kode Verifikasi Graniva');
            });
        } catch (\Throwable $e) {
            session()->forget('pending_register');
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal mengirim kode verifikasi: ' . $e->getMessage()], 500);
            }
            return back()->withInput()->with('error', 'Gagal mengirim kode verifikasi: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Kode verifikasi dikirim ke ' . $request->email,
                'redirect' => route('verify.show'),
            ]);
        }

        return redirect()->route('verify.show')->with('success', 'Kode verifikasi dikirim ke ' . $request->email);
    }

    public function showVerify()
    {
        if (!session('pending_register')) {
            return redirect()->route('register');
        }
        return view('auth.verify');
    }

    // ===== VERIFIKASI: BARU SINI AKUN MASUK DATABASE =====
    public function verify(Request $request)
    {
        $pending = session('pending_register');
        if (!$pending) {
            return redirect()->route('register');
        }

        $request->validate(['code' => 'required|string|size:8']);

        if (now()->timestamp > $pending['expired_at']) {
            session()->forget('pending_register');
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Kode kedaluwarsa. Silakan daftar ulang.'], 422);
            }
            return back()->with('error', 'Kode kedaluwarsa. Silakan daftar ulang.');
        }

        if (!hash_equals($pending['code'], $request->code)) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Kode verifikasi salah. Periksa kembali email Anda.'], 422);
            }
            return back()->with('error', 'Kode verifikasi salah. Periksa kembali email Anda.');
        }

        $user = User::create([
            'nama'     => $pending['data']['nama'],
            'email'    => $pending['data']['email'],
            'no_hp'    => $pending['data']['no_hp'],
            'alamat'   => $pending['data']['alamat'],
            'password' => Hash::make($pending['data']['password']),
            'saldo'    => 0,
            'status'   => 'aktif',
        ]);

        session()->forget('pending_register');
        Auth::login($user);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Akun berhasil diverifikasi! Selamat berbelanja di Graniva 🎉',
                'redirect' => route('produk.index'),
            ]);
        }

        return redirect()->route('produk.index')->with('success', 'Akun berhasil diverifikasi! Selamat berbelanja di Graniva 🎉');
    }

    // ===== KIRIM ULANG KODE VERIFIKASI =====
    public function resend(Request $request)
    {
        $pending = session('pending_register');
        if (!$pending) {
            return redirect()->route('register');
        }

        $code = str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        $pending['code'] = $code;
        $pending['expired_at'] = now()->addMinutes(10)->timestamp;
        session(['pending_register' => $pending]);

        try {
            Mail::html(view('emails.verification', ['nama' => $pending['data']['nama'], 'code' => $code])->render(), function ($message) use ($pending) {
                $message->to($pending['data']['email'])->subject('Kode Verifikasi Graniva');
            });
        } catch (\Throwable $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal mengirim ulang kode: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal mengirim ulang kode: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Kode baru telah dikirim ke email Anda.',
                'redirect' => route('verify.show'),
            ]);
        }

        return back()->with('success', 'Kode baru telah dikirim ke email Anda.');
    }

    // ===== HELPER: CEK APAKAH USER DIBLOKIR =====
    private function userDiblokir($user): bool
    {
        if (!$user) return false;

        // Terima banyak variasi nilai status blokir (case-insensitive)
        $statusBlok = ['diblokir', 'blokir', 'blocked', 'block', 'nonaktif', 'banned', 'ban', 'suspend', 'suspended'];

        return in_array(strtolower((string) $user->status), $statusBlok)
            || ($user->is_blocked ?? 0) == 1;
    }

    // ===== LOGIN (BLOKIR DOUBLE SAFETY + SOUND GAGAL) =====
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // ✅ SAFETY 1: cek blokir SEBELUM attempt
        if ($this->userDiblokir($user)) {
            session(['blocked_user_id' => $user->id_user]);
            $pesanBlokir = 'Akun Anda diblokir. Hubungi admin Graniva.';

            if ($request->ajax() || $request->expectsJson()) {
                session()->flash('error', $pesanBlokir);
                return response()->json([
                    'success'  => false,
                    'blocked'  => true,
                    'message'  => $pesanBlokir,
                    'redirect' => route('blocked'),
                ], 403);
            }

            return redirect()->route('blocked')->with('error', $pesanBlokir);
        }

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {

            // ✅ SAFETY 2: setelah login sukses, cek ulang — kalau diblokir, paksa logout
            $logged = Auth::user();
            if ($this->userDiblokir($logged)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                session(['blocked_user_id' => $logged->id_user]);
                $pesanBlokir = 'Akun Anda diblokir. Hubungi admin Graniva.';

                if ($request->ajax() || $request->expectsJson()) {
                    session()->flash('error', $pesanBlokir);
                    return response()->json([
                        'success'  => false,
                        'blocked'  => true,
                        'message'  => $pesanBlokir,
                        'redirect' => route('blocked'),
                    ], 403);
                }

                return redirect()->route('blocked')->with('error', $pesanBlokir);
            }

            $request->session()->regenerate();

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success'  => true,
                    'message'  => 'Login berhasil! Selamat datang kembali.',
                    'redirect' => route('produk.index'),
                ]);
            }

            return redirect()->intended(route('produk.index'));
        }

        // Gagal login
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], 422);
        }

        return back()->with('error', 'Email atau password salah.')->onlyInput('email');
    }

    // ===== LOGOUT (AKUN NORMAL) =====
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah keluar.');
    }

    // ===== KELUAR DARI HALAMAN AKUN DIBLOKIR (BARU) =====
    public function leaveBlocked(Request $request)
    {
        // Bersihkan penanda user diblokir
        $request->session()->forget('blocked_user_id');

        // Kalau somehow masih ada session login, paksa keluar
        if (Auth::check()) {
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ✅ Notif hijau + berhasil.mp3 di halaman login
        return redirect()->route('login')->with('success', 'Anda telah keluar.');
    }
}