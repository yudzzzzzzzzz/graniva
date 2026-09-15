<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class AdminWithdrawController extends Controller
{
    public function index()
    {
        $withdraws = Withdrawal::with('user')->latest()->paginate(10);
        return view('admin.withdraw.index', compact('withdraws'));
    }

    public function process(Request $request, $id)
    {
        $w = Withdrawal::findOrFail($id);
        $v = $request->validate(['aksi' => 'required|in:terima,tolak']);
        $total = $w->nominal + $w->biaya_admin;

        if ($v['aksi'] == 'terima') {
            $w->update(['status' => 'sukses']);
            Notifikasi::create([
                'id_user' => $w->id_user,
                'judul' => 'Penarikan Disetujui',
                'pesan' => 'Penarikan Rp '.number_format($w->nominal, 0, ',', '.').' ke '.$w->metode.' berhasil diproses.',
                'tipe' => 'success',
            ]);
        } else {
            $w->user()->increment('saldo', $total);
            $w->update(['status' => 'gagal']);
            Notifikasi::create([
                'id_user' => $w->id_user,
                'judul' => 'Penarikan Ditolak',
                'pesan' => 'Penarikan Rp '.number_format($w->nominal, 0, ',', '.').' ditolak. Saldo dikembalikan ke GraPay.',
                'tipe' => 'error',
            ]);
        }

        return back()->with('success', 'Penarikan diproses');
    }
}