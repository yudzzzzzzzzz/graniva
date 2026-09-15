<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\CartItem;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $items = CartItem::with('produk')->where('id_user', Auth::id())->get();
        $subtotal = $items->sum(fn($i) => $i->produk->harga * $i->jumlah);

        $lastAlamat = Alamat::where('id_user', Auth::id())->latest()->first();
        $alamatPrefill = $lastAlamat->alamat_lengkap ?? Auth::user()->alamat;

        return view('cart.index', compact('items', 'subtotal', 'alamatPrefill'));
    }

    public function add(Request $request)
    {
        $v = $request->validate([
            'id_produk' => 'required|exists:produk,id_produk',
            'jumlah' => 'nullable|integer|min:1',
        ]);

        $produk = Produk::findOrFail($v['id_produk']);
        $jumlah = $v['jumlah'] ?? 1;

        $cart = CartItem::where('id_user', Auth::id())->where('id_produk', $v['id_produk'])->first();

        if ($cart) {
            $cart->increment('jumlah', $jumlah);
        } else {
            CartItem::create([
                'id_user' => Auth::id(),
                'id_produk' => $v['id_produk'],
                'jumlah' => $jumlah,
            ]);
        }

        return back()->with('success', 'Ditambahkan ke keranjang');
    }

    public function update(Request $request, $id)
    {
        $cart = CartItem::where('id_user', Auth::id())->findOrFail($id);
        $v = $request->validate(['jumlah' => 'required|integer|min:1']);

        if ($v['jumlah'] > $cart->produk->stok) {
            return back()->with('error', 'Stok tidak cukup');
        }

        $cart->update(['jumlah' => $v['jumlah']]);
        return back()->with('success', 'Keranjang diupdate');
    }

    public function remove($id)
    {
        CartItem::where('id_user', Auth::id())->findOrFail($id)->delete();
        return back()->with('success', 'Item dihapus dari keranjang');
    }

    // Simpan alamat dari cart lalu lanjut ke checkout
    public function toCheckout(Request $request)
    {
        $v = $request->validate(['alamat' => 'required|string']);

        $alamat = Alamat::firstOrNew(['id_user' => Auth::id(), 'label' => 'Checkout']);
        $alamat->penerima = $alamat->penerima ?? Auth::user()->nama;
        $alamat->no_hp = $alamat->no_hp ?? Auth::user()->no_hp ?? '-';
        $alamat->alamat_lengkap = $v['alamat'];
        $alamat->kota = $alamat->kota ?? '';
        $alamat->kode_pos = $alamat->kode_pos ?? '';   // ← ini yang tadi kurang
        $alamat->is_default = true;
        $alamat->save();

        return redirect()->route('checkout.index');
    }
}