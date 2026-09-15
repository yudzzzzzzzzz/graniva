<?php

namespace App\Http\Controllers;

use App\Models\Topup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopupController extends Controller
{
    public function index()
    {
        $riwayat = Topup::where('id_user', Auth::id())->latest()->take(5)->get();
        return view('user.topup.index', compact('riwayat'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'nominal' => 'required|numeric|min:1000',
            'bukti_bayar' => 'required|image|max:5120',
        ], [
            'nominal.min' => 'Minimal top up Rp 1.000.',
            'bukti_bayar.required' => 'Upload bukti pembayaran QRIS.',
            'bukti_bayar.max' => 'Ukuran bukti maksimal 5MB.',
        ]);

        $path = $request->file('bukti_bayar')->store('bukti_topup', 'public');

        Topup::create([
            'id_user' => Auth::id(),
            'nominal' => $v['nominal'],
            'bukti_bayar' => $path,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Top up dibuat! Menunggu verifikasi admin, saldo masuk otomatis setelah disetujui.');
    }
}