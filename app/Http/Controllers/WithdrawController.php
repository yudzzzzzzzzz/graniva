<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawController extends Controller
{
    public function index()
    {
        $riwayat = Withdrawal::where('id_user', Auth::id())->latest()->take(5)->get();
        return view('user.withdraw.index', compact('riwayat'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'nominal' => 'required|numeric|min:1000',
            'metode' => 'required|in:DANA,GOPAY,OVO,BANK',
            'no_rekening' => 'required|string|max:30',
            'nama_bank' => 'nullable|string|max:50',
        ], ['nominal.min' => 'Minimal penarikan Rp 1.000.']);

        if ($v['metode'] == 'BANK' && !$request->nama_bank) {
            return back()->withErrors(['nama_bank' => 'Nama bank wajib diisi.'])->withInput();
        }

        $biaya = 500;
        $total = $v['nominal'] + $biaya;
        $user = Auth::user();

        if ($user->saldo < $total) {
            return back()->withErrors(['nominal' => 'Saldo tidak cukup. Butuh Rp '.number_format($total, 0, ',', '.').' (termasuk biaya admin).'])->withInput();
        }

        DB::transaction(function () use ($user, $v, $biaya, $total) {
            $user->decrement('saldo', $total);
            Withdrawal::create([
                'id_user' => $user->id_user,
                'nominal' => $v['nominal'],
                'biaya_admin' => $biaya,
                'metode' => $v['metode'],
                'no_rekening' => $v['no_rekening'],
                'nama_bank' => $v['metode'] == 'BANK' ? $v['nama_bank'] : null,
                'status' => 'pending',
            ]);
        });

        return back()->with('success', 'Penarikan dibuat! Saldo ditahan sementara, menunggu persetujuan admin.');
    }
}