<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $items = CartItem::with('produk.kategori')->where('id_user', Auth::id())->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        $subtotal = $items->sum(fn($i) => $i->produk->harga * $i->jumlah);
        $ongkir = $items->sum(fn($i) => $i->produk->ongkir ?? 0);
        $total = $subtotal + $ongkir;

        return view('checkout.index', compact('items', 'subtotal', 'ongkir', 'total'));
    }

    public function process(Request $request)
    {
        $items = CartItem::with('produk')->where('id_user', Auth::id())->get();
        if ($items->isEmpty()) return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');

        $v = $request->validate([
            'alamat_pengiriman' => 'required|string',
            'metode' => 'required|in:QRIS,SALDO',
            'bukti_bayar' => 'nullable|image|max:5120',
        ]);

        foreach ($items as $i) {
            if ($i->jumlah > $i->produk->stok) {
                return back()->with('error', 'Stok "'.$i->produk->nama_produk.'" tidak cukup.');
            }
        }

        $user = Auth::user();
        $subtotal = $items->sum(fn($i) => $i->produk->harga * $i->jumlah);
        $ongkir = $items->sum(fn($i) => $i->produk->ongkir ?? 0);
        $total = $subtotal + $ongkir;

        if ($v['metode'] == 'SALDO' && $user->saldo < $total) {
            return back()->with('error', 'Saldo GraPay tidak cukup.');
        }
        if ($v['metode'] == 'QRIS') {
            $request->validate(['bukti_bayar' => 'required|image|max:5120']);
        }

        $path = $request->hasFile('bukti_bayar') ? $request->file('bukti_bayar')->store('bukti_bayar', 'public') : null;
        $sukses = $v['metode'] == 'SALDO';

        DB::transaction(function () use ($items, $user, $v, $path, $sukses, $total) {
            if ($sukses) $user->decrement('saldo', $total);

            foreach ($items as $i) {
                $pesanan = Pesanan::create([
                    'id_user' => $user->id_user,
                    'id_produk' => $i->produk->id_produk,
                    'jumlah' => $i->jumlah,
                    'harga' => $i->produk->harga * $i->jumlah,
                    'ongkir' => $i->produk->ongkir ?? 0,
                    'kategori' => $i->produk->kategori->nama_kategori ?? $i->produk->jenis ?? 'Umum',
                    'alamat_pengiriman' => $v['alamat_pengiriman'],
                    'status_pesanan' => $sukses ? 'diproses' : 'menunggu_pembayaran',
                ]);

                $pesanan->pembayaran()->create([
                    'metode' => $v['metode'],
                    'bukti_bayar' => $path,
                    'tanggal_bayar' => now()->toDateString(),
                    'status_bayar' => $sukses ? 'sukses' : 'pending',
                ]);

                $i->produk->decrement('stok', $i->jumlah);
            }

            CartItem::where('id_user', $user->id_user)->delete();
        });

        return redirect()->route('pesanan.index')->with('success', $sukses ? 'Pembayaran via GraPay berhasil!' : 'Pesanan dibuat! Menunggu verifikasi pembayaran.');
    }
}