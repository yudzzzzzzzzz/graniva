<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Topup;
use Illuminate\Http\Request;

class AdminTopupController extends Controller
{
    public function index()
    {
        $topups = Topup::with('user')->latest()->paginate(10);
        return view('admin.topup.index', compact('topups'));
    }

    public function process(Request $request, $id)
    {
        $topup = Topup::findOrFail($id);
        $v = $request->validate(['aksi' => 'required|in:terima,tolak']);

        if ($v['aksi'] === 'terima') {
            $topup->user()->increment('saldo', $topup->nominal);
            $topup->update(['status' => 'sukses']);
            Notifikasi::create([
                'id_user' => $topup->id_user,
                'judul' => 'Top Up Berhasil',
                'pesan' => 'Top up Rp '.number_format($topup->nominal, 0, ',', '.').' berhasil. Saldo GraPay bertambah.',
                'tipe' => 'success',
            ]);
        } else {
            $topup->update(['status' => 'gagal']);
            Notifikasi::create([
                'id_user' => $topup->id_user,
                'judul' => 'Top Up Ditolak',
                'pesan' => 'Top up Rp '.number_format($topup->nominal, 0, ',', '.').' ditolak. Cek kembali bukti pembayaran.',
                'tipe' => 'error',
            ]);
        }

        return back()->with('success', 'Top up diproses');
    }
}