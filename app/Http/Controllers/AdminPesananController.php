<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminPesananController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total' => Pesanan::count(),
            'menunggu_pembayaran' => Pesanan::where('status_pesanan', 'menunggu_pembayaran')->count(),
            'diproses' => Pesanan::where('status_pesanan', 'diproses')->count(),
            'dikirim' => Pesanan::where('status_pesanan', 'dikirim')->count(),
            'sampai' => Pesanan::where('status_pesanan', 'sampai')->count(),
        ];

        $pesanans = Pesanan::with(['produk', 'user', 'pembayaran'])
            ->when($request->status, fn($q, $s) => $q->where('status_pesanan', $s))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pesanan.index', compact('pesanans', 'stats'));
    }

    public function show($id)
    {
        $pesanan = Pesanan::with(['produk', 'user', 'pembayaran', 'alamat'])->findOrFail($id);
        return view('admin.pesanan.show', compact('pesanan'));
    }

    // ===== HELPER: CEK APAKAH PESANAN TERKUNCI (BARU) =====
    private function terkunci($pesanan): bool
    {
        return $pesanan->status_pesanan === 'dibatalkan';
    }

    // ===== UBAH STATUS (dropdown di detail) — DENGAN REFUND + KUNCI =====
    public function updateStatus(Request $request, $id)
    {
        $pesanan = Pesanan::with(['produk', 'user', 'pembayaran'])->findOrFail($id);
        $v = $request->validate(['status' => 'required|in:menunggu_pembayaran,diproses,dikirim,sampai,dibatalkan']);

        // ✅ KUNCI: pesanan yang sudah dibatalkan TIDAK bisa diubah ke status lain
        if ($this->terkunci($pesanan)) {
            return back()->with('error', 'Pesanan yang sudah dibatalkan terkunci permanen dan tidak dapat diubah statusnya lagi.');
        }

        // ✅ Kalau admin mengubah status ke DIBATALKAN → lewat jalur refund otomatis
        if ($v['status'] === 'dibatalkan') {
            try {
                $hasil = $this->prosesPembatalanDanRefund($pesanan, 'Dibatalkan oleh admin Graniva');
            } catch (\Throwable $e) {
                return back()->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
            }

            if ($hasil['refund']) {
                return back()->with('success', 'Pesanan dibatalkan. Refund 100% (Rp ' . number_format($hasil['total'], 0, ',', '.') . ') dikembalikan ke saldo GraPay ' . ($pesanan->user->nama ?? 'user') . '. Status kini TERKUNCI.');
            }

            return back()->with('success', 'Pesanan dibatalkan. Tidak ada refund karena pembayaran belum terverifikasi. Status kini TERKUNCI.');
        }

        // Status lain berjalan normal (diproses, dikirim, sampai, dll)
        $pesanan->update(['status_pesanan' => $v['status']]);

        Notifikasi::create([
            'id_user' => $pesanan->id_user,
            'judul' => 'Status Pesanan Diperbarui',
            'pesan' => 'Pesanan #'.$pesanan->id_pesanan.' sekarang: '.ucfirst(str_replace('_', ' ', $v['status'])),
            'tipe' => $v['status'] == 'dibatalkan' ? 'error' : 'success',
        ]);

        return back()->with('success', 'Status pesanan diperbarui');
    }

    public function verifyPayment(Request $request, $id)
    {
        $pesanan = Pesanan::findOrFail($id);
        $v = $request->validate(['aksi' => 'required|in:terima,tolak']);

        // ✅ KUNCI: pesanan dibatalkan tidak bisa diverifikasi
        if ($this->terkunci($pesanan)) {
            return back()->with('error', 'Pesanan ini sudah dibatalkan & terkunci. Verifikasi pembayaran tidak tersedia.');
        }

        if ($v['aksi'] === 'terima') {
            $pesanan->pembayaran()->update(['status_bayar' => 'sukses']);
            $pesanan->update(['status_pesanan' => 'diproses']);
            Notifikasi::create([
                'id_user' => $pesanan->id_user,
                'judul' => 'Pembayaran Diverifikasi',
                'pesan' => 'Pembayaran pesanan #'.$pesanan->id_pesanan.' diterima. Pesanan sedang diproses.',
                'tipe' => 'success',
            ]);
        } else {
            $pesanan->pembayaran()->update(['status_bayar' => 'gagal']);
            $pesanan->update(['status_pesanan' => 'dibatalkan']);
            $pesanan->produk()->increment('stok', $pesanan->jumlah);
            Notifikasi::create([
                'id_user' => $pesanan->id_user,
                'judul' => 'Pembayaran Ditolak',
                'pesan' => 'Pembayaran pesanan #'.$pesanan->id_pesanan.' ditolak. Silakan upload ulang bukti.',
                'tipe' => 'error',
            ]);
        }

        return back()->with('success', 'Pembayaran diverifikasi');
    }

    public function updateShipping(Request $request, $id)
    {
        $pesanan = Pesanan::findOrFail($id);
        $v = $request->validate([
            'kurir' => 'required|string|max:100',
            'no_resi' => 'required|string|max:100',
            'estimasi_datang' => 'nullable|date',
        ]);

        // ✅ KUNCI: pesanan dibatalkan tidak bisa dikirim
        if ($this->terkunci($pesanan)) {
            return back()->with('error', 'Pesanan ini sudah dibatalkan & terkunci. Pengiriman tidak tersedia.');
        }

        $pesanan->update([
            'kurir' => $v['kurir'],
            'no_resi' => $v['no_resi'],
            'estimasi_datang' => $v['estimasi_datang'] ?? now()->addDays(4)->toDateString(),
            'status_pesanan' => 'dikirim',
        ]);

        Notifikasi::create([
            'id_user' => $pesanan->id_user,
            'judul' => 'Pesanan Dikirim',
            'pesan' => 'Pesanan #'.$pesanan->id_pesanan.' dikirim via '.$v['kurir'].'. Resi: '.$v['no_resi'],
            'tipe' => 'success',
        ]);

        return back()->with('success', 'Pesanan ditandai dikirim');
    }

    public function markArrived($id)
    {
        $pesanan = Pesanan::findOrFail($id);

        // ✅ KUNCI: pesanan dibatalkan tidak bisa ditandai sampai
        if ($this->terkunci($pesanan)) {
            return back()->with('error', 'Pesanan ini sudah dibatalkan & terkunci. Tidak dapat ditandai sampai.');
        }

        $pesanan->update(['status_pesanan' => 'sampai']);

        Notifikasi::create([
            'id_user' => $pesanan->id_user,
            'judul' => 'Pesanan Sampai',
            'pesan' => 'Pesanan #'.$pesanan->id_pesanan.' telah sampai di tujuan. Terima kasih!',
            'tipe' => 'success',
        ]);

        return back()->with('success', 'Pesanan ditandai sampai tujuan');
    }

    public function setKendala(Request $request, $id)
    {
        $pesanan = Pesanan::findOrFail($id);
        $v = $request->validate(['kendala' => 'required|string']);

        $pesanan->update(['kendala_pengiriman' => $v['kendala']]);

        Notifikasi::create([
            'id_user' => $pesanan->id_user,
            'judul' => 'Kendala Pengiriman',
            'pesan' => 'Pesanan #'.$pesanan->id_pesanan.': '.$v['kendala'],
            'tipe' => 'error',
        ]);

        return back()->with('success', 'Kendala dicatat');
    }

    // ===== BATALKAN PESANAN + REFUND OTOMATIS =====
    public function cancel(Request $request, $id)
    {
        $pesanan = Pesanan::with(['produk', 'user', 'pembayaran'])->findOrFail($id);

        // ✅ Anti double refund / anti proses ulang pesanan terkunci
        if ($this->terkunci($pesanan)) {
            return back()->with('error', 'Pesanan ini sudah dibatalkan sebelumnya dan statusnya terkunci permanen.');
        }

        // Pesanan yang sudah dikirim/sampai tidak bisa dibatalkan (pakai retur)
        if (in_array($pesanan->status_pesanan, ['dikirim', 'sampai', 'selesai'])) {
            return back()->with('error', 'Pesanan yang sudah dikirim/sampai tidak dapat dibatalkan. Gunakan fitur retur.');
        }

        $alasan = $request->input('alasan') ?: 'Dibatalkan oleh admin Graniva';

        try {
            $hasil = $this->prosesPembatalanDanRefund($pesanan, $alasan);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }

        if ($hasil['refund']) {
            return back()->with('success', 'Pesanan dibatalkan. Refund 100% (Rp ' . number_format($hasil['total'], 0, ',', '.') . ') dikembalikan ke saldo GraPay ' . ($pesanan->user->nama ?? 'user') . '. Status kini TERKUNCI.');
        }

        return back()->with('success', 'Pesanan dibatalkan. Tidak ada refund karena pembayaran belum terverifikasi. Status kini TERKUNCI.');
    }

    // ===== HELPER: PEMBATALAN + REFUND 100% KE GRAPAY =====
    private function prosesPembatalanDanRefund($pesanan, $alasan = null)
    {
        $user       = $pesanan->user;
        $pembayaran = $pesanan->pembayaran;

        // Ambil metode & status bayar (dukung beberapa nama kolom)
        $metode    = $pembayaran->metode_pembayaran ?? $pembayaran->metode ?? null;
        $metode    = is_string($metode) ? strtolower($metode) : null;
        $statusByr = $pembayaran->status_bayar ?? $pembayaran->status ?? null;
        $statusByr = is_string($statusByr) ? strtolower($statusByr) : null;

        // Total refund = (harga × jumlah) + ongkir
        $totalRefund = ($pesanan->harga * $pesanan->jumlah) + ($pesanan->ongkir ?? 0);

        // ===== ATURAN REFUND =====
        $layakRefund = false;
        $keterangan  = '';

        if ($metode === 'grapay') {
            // Saldo GraPay: dana sudah terpotong saat transaksi → SELALU refund saat dibatalkan
            $layakRefund = true;
            $keterangan  = 'pembayaran via saldo GraPay';
        } elseif (in_array($statusByr, ['sukses', 'terverifikasi', 'lunas', 'verified'])) {
            // QRIS / Bank / E-Wallet: refund hanya jika pembayaran SUDAH diverifikasi (Lunas)
            $layakRefund = true;
            $keterangan  = 'pembayaran ' . ($metode ?? 'non-saldo') . ' sudah terverifikasi (Lunas)';
        } else {
            // Belum diverifikasi → dana belum dipotong/diterima → batal tanpa refund
            $keterangan = 'pembayaran belum terverifikasi, dana tidak dipotong';
        }

        DB::beginTransaction();

        try {
            // 1) Ubah status pesanan → dibatalkan + simpan alasan
            $pesanan->status_pesanan = 'dibatalkan';
            if ($alasan) $pesanan->alasan_batal = $alasan;
            $pesanan->save();

            if ($layakRefund) {
                // 2) Refund 100% ke saldo GraPay user
                $user->increment('saldo', $totalRefund);

                // 3) Kembalikan stok produk
                if ($pesanan->produk) {
                    $pesanan->produk->increment('stok', $pesanan->jumlah);
                }

                // 4) Notifikasi ke user: batal + refund
                $this->kirimNotifikasi(
                    $pesanan->id_user,
                    'Pesanan Dibatalkan + Refund ✅',
                    'Pesanan #' . $pesanan->id_pesanan . ' dibatalkan oleh admin (' . $keterangan . '). Dana Rp ' . number_format($totalRefund, 0, ',', '.') . ' telah dikembalikan 100% ke saldo GraPay Anda.',
                    'success'
                );
            } else {
                // Notifikasi ke user: batal tanpa refund
                $this->kirimNotifikasi(
                    $pesanan->id_user,
                    'Pesanan Dibatalkan',
                    'Pesanan #' . $pesanan->id_pesanan . ' dibatalkan oleh admin. ' . ucfirst($keterangan) . ', sehingga tidak ada pengembalian dana.',
                    'error'
                );
            }

            DB::commit();

            return ['refund' => $layakRefund, 'total' => $totalRefund];

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // ===== HELPER: KIRIM NOTIFIKASI (AMAN WALAU NAMA KOLOM BEDA) =====
    private function kirimNotifikasi($idUser, $judul, $pesan, $tipe = 'success')
    {
        $t = (new Notifikasi)->getTable();

        $data = [
            'id_user'    => $idUser,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn($t, 'judul')) $data['judul'] = $judul;
        if (Schema::hasColumn($t, 'pesan'))      $data['pesan'] = $pesan;
        elseif (Schema::hasColumn($t, 'isi'))    $data['isi']   = $pesan;
        if (Schema::hasColumn($t, 'tipe'))       $data['tipe']  = $tipe;
        if (Schema::hasColumn($t, 'is_read'))    $data['is_read'] = 0;

        DB::table($t)->insert($data);
    }
}