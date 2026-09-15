<?php

namespace App\Http\Controllers;

use App\Models\Retur;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminReturController extends Controller
{
    // ===== LIST RETUR =====
    public function index(Request $request)
    {
        $query = Retur::with(['user', 'pesanan.produk']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('nama', 'like', "%{$search}%"))
                  ->orWhereHas('pesanan', fn($p) => $p->where('id_pesanan', 'like', "%{$search}%"));
            });
        }

        $returs = $query->latest()->paginate(15)->withQueryString();

        return view('admin.retur.index', compact('returs'));
    }

    // ===== PROSES RETUR: ACC / TOLAK + REFUND 100% =====
    public function process(Request $request, $id)
    {
        // Ambil status dari hidden input 'status' (atau nama tombol sebagai cadangan)
        $status = $request->input('status');
        if (!$status) {
            if ($request->has('setujui') || $request->input('aksi') === 'setujui') {
                $status = 'disetujui';
            } elseif ($request->has('tolak') || $request->input('aksi') === 'tolak') {
                $status = 'ditolak';
            }
        }

        if (!in_array($status, ['disetujui', 'ditolak'])) {
            return back()->with('error', 'Aksi tidak valid: status tidak terkirim.');
        }

        $retur = Retur::with(['pesanan.produk', 'user'])->findOrFail($id);

        // ✅ FIX: terima status awal 'menunggu' ATAU 'pending'
        if (!in_array($retur->status, ['menunggu', 'pending'])) {
            return back()->with('error', 'Retur ini sudah diproses sebelumnya.');
        }

        DB::beginTransaction();

        try {
            $tableRetur = $retur->getTable();
            $retur->status = $status;
            if (Schema::hasColumn($tableRetur, 'catatan_admin'))  $retur->catatan_admin  = $request->catatan;
            if (Schema::hasColumn($tableRetur, 'tanggal_proses')) $retur->tanggal_proses = now();
            $retur->save();

            $user    = $retur->user;
            $pesanan = $retur->pesanan;
            $message = '';

            if ($status === 'disetujui' && $pesanan) {

                // ===== REFUND 100% OTOMATIS: (harga × jumlah) + ongkir =====
                $totalRefund = ($pesanan->harga * $pesanan->jumlah) + ($pesanan->ongkir ?? 0);

                // 1) Saldo GraPay user +100%
                $user->increment('saldo', $totalRefund);

                // 2) Status pesanan → diretur
                $pesanan->status_pesanan = 'diretur';
                $pesanan->save();

                // 3) Stok produk dikembalikan
                if ($pesanan->produk) {
                    $pesanan->produk->increment('stok', $pesanan->jumlah);
                }

                // 4) Notifikasi ke user
                $this->kirimNotifikasi(
                    $user->id_user,
                    'Retur Disetujui ✅',
                    'Retur pesanan #' . $pesanan->id_pesanan . ' (' . ($pesanan->produk->nama_produk ?? '-') . ') disetujui. Dana Rp ' . number_format($totalRefund, 0, ',', '.') . ' dikembalikan 100% ke saldo GraPay Anda.'
                );

                $message = 'Retur disetujui! Refund 100% (Rp ' . number_format($totalRefund, 0, ',', '.') . ') masuk ke saldo GraPay ' . $user->nama . '.';

            } else {

                // ===== RETUR DITOLAK =====
                $this->kirimNotifikasi(
                    $user->id_user,
                    'Retur Ditolak ❌',
                    'Retur pesanan #' . ($pesanan->id_pesanan ?? '-') . ' ditolak oleh admin.' . ($request->catatan ? ' Alasan: ' . $request->catatan : '')
                );

                $message = 'Retur ditolak. Notifikasi terkirim ke ' . ($user->nama ?? 'user') . '.';
            }

            DB::commit();

            return back()->with('success', $message);

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses retur: ' . $e->getMessage());
        }
    }

    // ===== Helper notif (aman walau nama kolom beda) =====
    private function kirimNotifikasi($idUser, $judul, $pesan)
    {
        $t = (new Notifikasi)->getTable();

        $data = [
            'id_user'    => $idUser,
            'is_read'    => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn($t, 'judul')) $data['judul'] = $judul;
        if (Schema::hasColumn($t, 'pesan'))      $data['pesan'] = $pesan;
        elseif (Schema::hasColumn($t, 'isi'))    $data['isi']   = $pesan;

        DB::table($t)->insert($data);
    }
}