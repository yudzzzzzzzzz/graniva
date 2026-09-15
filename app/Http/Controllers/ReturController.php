<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Pesanan;
use App\Models\Retur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReturController extends Controller
{
    public function index()
    {
        $returs = Retur::with('pesanan.produk')
            ->where('id_user', Auth::id())
            ->latest()->get();

        return view('user.retur.index', compact('returs'));
    }

    public function create(Request $request)
    {
        $pesanan = Pesanan::with('produk')
            ->where('id_user', Auth::id())
            ->findOrFail($request->query('pesanan'));

        return view('user.retur.create', compact('pesanan'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'id_pesanan' => 'required|exists:pesanan,id_pesanan',
            'alasan' => 'required|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        // Pastikan pesanan milik user
        Pesanan::where('id_user', Auth::id())->findOrFail($v['id_pesanan']);

        if ($request->hasFile('foto')) {
            $v['foto'] = $request->file('foto')->store('retur', 'public');
        }

        $v['id_user'] = Auth::id();
        Retur::create($v);

        return redirect()->route('retur.index')->with('success', 'Pengajuan retur terkirim');
    }
}