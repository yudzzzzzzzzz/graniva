<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $jumlahUser = User::count();
        $jumlahProduk = Produk::count();
        $jumlahPesanan = Pesanan::count();

        $totalPenjualan = Pesanan::whereHas('pembayaran', fn($q) => $q->where('status_bayar', 'sukses'))
            ->get()->sum(fn($p) => $p->harga + ($p->ongkir ?? 0));

        // Grafik 7 hari
        $labels = [];
        $dataPenjualan = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d/m');
            $nilai = Pesanan::whereHas('pembayaran', fn($q) => $q->where('status_bayar', 'sukses'))
                ->whereDate('created_at', $date->toDateString())
                ->get()->sum(fn($p) => $p->harga + ($p->ongkir ?? 0));
            $dataPenjualan[] = $nilai;
        }

        $statusPesanan = [
            'menunggu_pembayaran' => Pesanan::where('status_pesanan', 'menunggu_pembayaran')->count(),
            'diproses' => Pesanan::where('status_pesanan', 'diproses')->count(),
            'dikirim' => Pesanan::where('status_pesanan', 'dikirim')->count(),
            'sampai' => Pesanan::where('status_pesanan', 'sampai')->count(),
        ];

        $pesananTerbaru = Pesanan::with(['user', 'produk', 'pembayaran'])->latest()->take(5)->get();

        // ===== LAPORAN BULAN INI (buat print) =====
        $laporan = Pesanan::with(['user', 'produk', 'pembayaran'])
            ->whereHas('pembayaran', fn($q) => $q->where('status_bayar', 'sukses'))
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->latest()->get();

        return view('admin.dashboard.index', compact(
            'jumlahUser', 'jumlahProduk', 'jumlahPesanan', 'totalPenjualan',
            'labels', 'dataPenjualan', 'statusPesanan', 'pesananTerbaru', 'laporan'
        ));
    }
}