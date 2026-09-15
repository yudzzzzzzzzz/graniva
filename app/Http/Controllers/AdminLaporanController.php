<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;

class AdminLaporanController extends Controller
{
    // Query pesanan sukses berdasarkan periode
    private function getPesananByPeriode($periode)
    {
        $query = Pesanan::with(['produk', 'user', 'pembayaran'])
            ->whereHas('pembayaran', fn($q) => $q->where('status_bayar', 'sukses'));

        switch ($periode) {
            case 'hari_ini':
                $query->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
                break;
            case 'minggu_ini':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'bulan_ini':
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
            case 'tahun_ini':
                $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
                break;
            case 'semua':
                break;
            default:
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        }

        return $query->latest()->get();
    }

    public function index(Request $request)
    {
        $periode = $request->query('periode', 'bulan_ini');
        $pesanans = $this->getPesananByPeriode($periode);

        $totalPenjualan = $pesanans->sum(fn($p) => $p->harga + ($p->ongkir ?? 0));
        $jumlahTransaksi = $pesanans->count();
        $totalItem = $pesanans->sum('jumlah');
        $rataRata = $jumlahTransaksi > 0 ? $totalPenjualan / $jumlahTransaksi : 0;

        $produkTerlaris = $pesanans->groupBy('id_produk')
            ->map(fn($group) => [
                'nama' => $group->first()->produk->nama_produk ?? '-',
                'jumlah' => $group->sum('jumlah'),
                'pendapatan' => $group->sum(fn($p) => $p->harga),
            ])
            ->sortByDesc('jumlah')
            ->take(5)
            ->values();

        return view('admin.laporan.index', compact(
            'pesanans', 'totalPenjualan', 'jumlahTransaksi', 'totalItem', 'rataRata', 'produkTerlaris', 'periode'
        ));
    }

    // Export ke CSV (bisa dibuka di Excel)
    public function export(Request $request)
    {
        $periode = $request->query('periode', 'bulan_ini');
        $pesanans = $this->getPesananByPeriode($periode);

        $csvFileName = 'laporan_penjualan_' . $periode . '_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$csvFileName\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0",
        ];

        $columns = ['ID Pesanan', 'Tanggal', 'Nama User', 'Produk', 'Jumlah', 'Harga', 'Ongkir', 'Total', 'Metode Bayar', 'Status'];

        $callback = function () use ($pesanans, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns);

            foreach ($pesanans as $p) {
                fputcsv($file, [
                    $p->id_pesanan,
                    $p->created_at->format('d/m/Y H:i'),
                    $p->user->nama ?? '-',
                    $p->produk->nama_produk ?? '-',
                    $p->jumlah,
                    $p->harga,
                    $p->ongkir ?? 0,
                    $p->harga + ($p->ongkir ?? 0),
                    $p->pembayaran->metode ?? '-',
                    ucfirst(str_replace('_', ' ', $p->status_pesanan)),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}