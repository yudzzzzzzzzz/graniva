<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::with('produk.kategori')->where('id_user', Auth::id())->latest()->get();
        return view('user.wishlist.index', compact('wishlists'));
    }

    public function toggle(Request $request)
    {
        $v = $request->validate(['id_produk' => 'required|exists:produk,id_produk']);

        $existing = Wishlist::where('id_user', Auth::id())->where('id_produk', $v['id_produk'])->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Dihapus dari wishlist');
        }

        Wishlist::create(['id_user' => Auth::id(), 'id_produk' => $v['id_produk']]);
        return back()->with('success', 'Ditambahkan ke wishlist ❤️');
    }

    public function destroy($id)
    {
        Wishlist::where('id_user', Auth::id())->findOrFail($id)->delete();
        return back()->with('success', 'Dihapus dari wishlist');
    }
}