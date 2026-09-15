<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Ulasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UlasanController extends Controller
{
    public function store(Request $request)
    {
        // ✅ UPDATE: limit gambar 8 MB + pesan error bahasa Indonesia
        $v = $request->validate([
            'id_produk' => 'required|exists:produk,id_produk',
            'rating'    => 'required|integer|min:1|max:5',
            'komentar'  => 'nullable|string|max:1000',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
        ], [
            'id_produk.required' => 'Produk tidak valid.',
            'id_produk.exists'   => 'Produk tidak ditemukan.',
            'rating.required'    => 'Silakan klik bintang untuk memberi rating terlebih dahulu.',
            'rating.integer'     => 'Rating harus berupa angka.',
            'rating.min'         => 'Rating minimal 1 bintang.',
            'rating.max'         => 'Rating maksimal 5 bintang.',
            'komentar.max'       => 'Komentar maksimal 1000 karakter.',
            'gambar.image'       => 'File yang diunggah harus berupa gambar.',
            'gambar.mimes'       => 'Format gambar yang didukung: JPG, JPEG, PNG, WEBP.',
            'gambar.max'         => 'Ukuran gambar maksimal 8 MB. Gambar kamu kegedean, kompres dulu ya.',
        ]);

        // ===== WAJIB UDAH BELI (pembayaran sukses) =====
        $sudahBeli = Pesanan::where('id_user', Auth::id())
            ->where('id_produk', $v['id_produk'])
            ->whereHas('pembayaran', fn($q) => $q->where('status_bayar', 'sukses'))
            ->exists();

        if (!$sudahBeli) {
            return back()->with('error', 'Anda harus membeli produk ini terlebih dahulu untuk memberi ulasan');
        }

        // ===== Satu user cuma boleh satu ulasan per produk =====
        $sudahUlas = Ulasan::where('id_user', Auth::id())
            ->where('id_produk', $v['id_produk'])
            ->exists();

        if ($sudahUlas) {
            return back()->with('error', 'Anda sudah mengulas produk ini');
        }

        // Upload gambar kalau ada
        if ($request->hasFile('gambar')) {
            $v['gambar'] = $request->file('gambar')->store('ulasan', 'public');
        }

        $v['id_user'] = Auth::id();
        Ulasan::create($v);

        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    }

    public function destroy($id)
    {
        $ulasan = Ulasan::where('id_ulasan', $id)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        if ($ulasan->gambar) {
            Storage::disk('public')->delete($ulasan->gambar);
        }

        $ulasan->delete();
        return back()->with('success', 'Ulasan dihapus');
    }
}