<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\CartItem;
use App\Models\Notifikasi;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Retur;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->q, fn($q, $s) => $q->where(fn($qq) => $qq
                ->where('nama', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        $pesanans = Pesanan::with(['produk', 'pembayaran'])
            ->where('id_user', $id)->latest()->get();

        return view('admin.users.show', compact('user', 'pesanans'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $v = $request->validate([
            'nama'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id.',id_user',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $user->update($v);
        return back()->with('success', 'Profil user diupdate');
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $v = $request->validate(['password' => 'required|min:6']);

        $user->update(['password' => Hash::make($v['password'])]);
        return back()->with('success', 'Password user direset');
    }

    public function toggleBlock($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_blocked' => !$user->is_blocked]);

        return back()->with('success', $user->is_blocked ? 'User diblokir' : 'User dibuka blokirnya');
    }

    // ===== HAPUS AKUN USER =====
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $nama = $user->nama;

        DB::transaction(function () use ($user) {
            // Ambil semua id pesanan milik user
            $pesananIds = Pesanan::where('id_user', $user->id_user)->pluck('id_pesanan');

            if ($pesananIds->isNotEmpty()) {
                // Bersihin retur + detailnya dulu
                $returIds = Retur::whereIn('id_pesanan', $pesananIds)->pluck('id_retur');
                if ($returIds->isNotEmpty()) {
                    DB::table('detail_retur')->whereIn('id_retur', $returIds)->delete();
                    Retur::whereIn('id_retur', $returIds)->delete();
                }

                Pembayaran::whereIn('id_pesanan', $pesananIds)->delete();
                Ulasan::whereIn('id_pesanan', $pesananIds)->delete();
                Pesanan::whereIn('id_pesanan', $pesananIds)->delete();
            }

            // Bersihin data lain milik user
            Ulasan::where('id_user', $user->id_user)->delete();
            CartItem::where('id_user', $user->id_user)->delete();
            Alamat::where('id_user', $user->id_user)->delete();
            Notifikasi::where('id_user', $user->id_user)->delete();

            // Hapus akunnya
            $user->delete();
        });

        return back()->with('success', 'Akun '.$nama.' beserta semua datanya dihapus');
    }
}