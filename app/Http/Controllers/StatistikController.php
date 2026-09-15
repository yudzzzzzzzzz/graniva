<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Support\Facades\Auth;

class StatistikController extends Controller
{
    public function index()
    {
        $pesanans = Pesanan::with('produk')->where('id_user', Auth::id())->get();

        $stats = [
            'total_pesanan' => $pesanans->count(),
            'total_belanja' => $pesanans->sum('harga'),
            'pesanan_selesai' => $pesanans->where('status_pesanan', 'selesai')->count(),
        ];

        return view('user.statistik.index', compact('stats', 'pesanans'));
    }
}