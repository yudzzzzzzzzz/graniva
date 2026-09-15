<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Pesanan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    public function index()
    {
        $pesanans = Pesanan::with(['produk', 'pembayaran'])
            ->where('id_user', Auth::id())->latest()->get();
        return view('pesanan.index', compact('pesanans'));
    }

    public function create(Request $request)
    {
        $produk = Produk::findOrFail($request->query('produk'));
        return view('pesanan.create', compact('produk'));
    }

    // ===== SIMPAN DRAFT (belum jadi pesanan) =====
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_produk' => 'required|exists:produk,id_produk',
            'jumlah' => 'required|integer|min:1',
            'alamat_pengiriman' => 'required|string',
        ]);

        $produk = Produk::findOrFail($validated['id_produk']);
        if ($validated['jumlah'] > $produk->stok) {
            return back()->withErrors(['jumlah' => 'Stok tidak cukup. Tersedia: '.$produk->stok]);
        }

        session(['draft_pesanan' => [
            'id_produk' => $produk->id_produk,
            'jumlah' => $validated['jumlah'],
            'alamat_pengiriman' => $validated['alamat_pengiriman'],
        ]]);

        return redirect()->route('pesanan.bayarDraft');
    }

    // ===== HALAMAN BAYAR DRAFT =====
    public function showPaymentDraft()
    {
        $draft = session('draft_pesanan');
        if (!$draft) return redirect()->route('produk.index')->with('error', 'Tidak ada draft pesanan.');

        $produk = Produk::findOrFail($draft['id_produk']);

        $pesanan = (object) [
            'id_pesanan' => null,
            'produk' => $produk,
            'jumlah' => $draft['jumlah'],
            'harga' => $produk->harga * $draft['jumlah'],
            'ongkir' => $produk->ongkir ?? 0,
        ];

        $draftMode = true;
        return view('pesanan.bayar', compact('pesanan', 'draftMode'));
    }

    // ===== BAYAR DRAFT → BARU DIBUAT PESANANNYA =====
    public function payDraft(Request $request)
    {
        $draft = session('draft_pesanan');
        if (!$draft) return redirect()->route('produk.index')->with('error', 'Draft tidak ditemukan.');

        $user = Auth::user();
        $produk = Produk::findOrFail($draft['id_produk']);
        $jumlah = $draft['jumlah'];

        if ($jumlah > $produk->stok) {
            return redirect()->route('produk.index')->with('error', 'Stok tidak cukup.');
        }

        $v = $request->validate([
            'metode' => 'required|in:QRIS,SALDO',
            'bukti_bayar' => 'nullable|image|max:5120',
        ]);

        $harga = $produk->harga * $jumlah;
        $ongkir = $produk->ongkir ?? 0;
        $total = $harga + $ongkir;

        if ($v['metode'] == 'SALDO' && $user->saldo < $total) {
            return back()->with('error', 'Saldo GraPay tidak cukup.');
        }
        if ($v['metode'] == 'QRIS') {
            $request->validate(['bukti_bayar' => 'required|image|max:5120']);
        }

        $path = $request->hasFile('bukti_bayar') ? $request->file('bukti_bayar')->store('bukti_bayar', 'public') : null;
        $sukses = $v['metode'] == 'SALDO';

        DB::transaction(function () use ($user, $produk, $draft, $jumlah, $harga, $ongkir, $v, $path, $sukses) {
            if ($sukses) $user->decrement('saldo', $harga + $ongkir);

            $pesanan = Pesanan::create([
                'id_user' => $user->id_user,
                'id_produk' => $produk->id_produk,
                'jumlah' => $jumlah,
                'harga' => $harga,
                'ongkir' => $ongkir,
                'kategori' => $produk->kategori->nama_kategori ?? $produk->jenis ?? 'Umum',
                'alamat_pengiriman' => $draft['alamat_pengiriman'],
                'status_pesanan' => $sukses ? 'diproses' : 'menunggu_pembayaran',
            ]);

            $pesanan->pembayaran()->create([
                'metode' => $v['metode'],
                'bukti_bayar' => $path,
                'tanggal_bayar' => now()->toDateString(),
                'status_bayar' => $sukses ? 'sukses' : 'pending',
            ]);

            $produk->decrement('stok', $jumlah);
        });

        session()->forget('draft_pesanan');

        return redirect()->route('pesanan.index')->with('success', $sukses ? 'Pembayaran via GraPay berhasil!' : 'Pesanan dibuat! Menunggu verifikasi pembayaran.');
    }

    // ===== HALAMAN BAYAR (pesanan yang udah ada) =====
    public function showPayment($id)
    {
        $pesanan = Pesanan::with('produk')->where('id_user', Auth::id())->findOrFail($id);
        $draftMode = false;
        return view('pesanan.bayar', compact('pesanan', 'draftMode'));
    }

    // ===== BAYAR (pesanan yang udah ada) =====
    public function pay(Request $request, $id)
    {
        $pesanan = Pesanan::where('id_user', Auth::id())->findOrFail($id);
        $user = Auth::user();
        $total = $pesanan->harga + ($pesanan->ongkir ?? 0);

        $v = $request->validate([
            'metode' => 'required|in:QRIS,SALDO',
            'bukti_bayar' => 'nullable|image|max:5120',
        ]);

        if ($v['metode'] == 'SALDO') {
            if ($user->saldo < $total) return back()->with('error', 'Saldo GraPay tidak cukup.');

            DB::transaction(function () use ($user, $pesanan, $total) {
                $user->decrement('saldo', $total);
                $pesanan->pembayaran()->updateOrCreate([], [
                    'metode' => 'SALDO', 'bukti_bayar' => null,
                    'tanggal_bayar' => now()->toDateString(), 'status_bayar' => 'sukses',
                ]);
                $pesanan->update(['status_pesanan' => 'diproses']);
            });
            return redirect()->route('pesanan.index')->with('success', 'Pembayaran via GraPay berhasil!');
        }

        $request->validate(['bukti_bayar' => 'required|image|max:5120']);
        $path = $request->file('bukti_bayar')->store('bukti_bayar', 'public');
        $pesanan->pembayaran()->updateOrCreate([], [
            'metode' => $v['metode'], 'bukti_bayar' => $path,
            'tanggal_bayar' => now()->toDateString(), 'status_bayar' => 'pending',
        ]);

        return redirect()->route('pesanan.index')->with('success', 'Pembayaran terkirim! Menunggu verifikasi admin.');
    }

    // ===== BATALKAN =====
    public function cancel(Request $request, $id)
    {
        $pesanan = Pesanan::where('id_user', Auth::id())->findOrFail($id);

        if (!in_array($pesanan->status_pesanan, ['menunggu_pembayaran', 'diproses'])) {
            return back()->with('error', 'Pesanan yang sudah dikirim tidak dapat dibatalkan');
        }

        $v = $request->validate(['alasan' => 'required|string|min:5|max:500'], [
            'alasan.required' => 'Alasan pembatalan wajib diisi.',
        ]);

        $sb = $pesanan->pembayaran->status_bayar ?? 'pending';
        $total = $pesanan->harga + ($pesanan->ongkir ?? 0);

        DB::transaction(function () use ($pesanan, $sb, $total, $v) {
            $pesanan->produk()->increment('stok', $pesanan->jumlah);

            if ($sb == 'sukses') {
                $pesanan->user()->increment('saldo', $total);
                $pesanan->pembayaran()->update(['status_bayar' => 'refund']);
                Notifikasi::create(['id_user' => $pesanan->id_user, 'judul' => 'Pengembalian Dana ke GraPay', 'pesan' => 'Pesanan #'.$pesanan->id_pesanan.' dibatalkan. Rp '.number_format($total, 0, ',', '.').' masuk ke GraPay Anda.', 'tipe' => 'success']);
            } else {
                Notifikasi::create(['id_user' => $pesanan->id_user, 'judul' => 'Pesanan Dibatalkan', 'pesan' => 'Pesanan #'.$pesanan->id_pesanan.' dibatalkan.', 'tipe' => 'error']);
            }

            $pesanan->update(['status_pesanan' => 'dibatalkan', 'alasan_batal' => $v['alasan']]);
        });

        return back()->with('success', 'Pesanan dibatalkan. '.($sb == 'sukses' ? 'Dana masuk ke GraPay Anda.' : ''));
    }

    public function track($id)
    {
        $pesanan = Pesanan::with(['produk', 'pembayaran'])->where('id_user', Auth::id())->findOrFail($id);
        return view('pesanan.track', compact('pesanan'));
    }

    public function invoice($id)
    {
        $pesanan = Pesanan::with(['produk', 'pembayaran', 'user'])->where('id_user', Auth::id())->findOrFail($id);
        return view('pesanan.invoice', compact('pesanan'));
    }

    public function destroy($id)
    {
        $pesanan = Pesanan::where('id_user', Auth::id())->findOrFail($id);

        if (!in_array($pesanan->status_pesanan, ['menunggu_pembayaran', 'dibatalkan'])) {
            return back()->with('error', 'Pesanan yang sudah diproses tidak dapat dihapus');
        }
        if ($pesanan->status_pesanan == 'menunggu_pembayaran') {
            $pesanan->produk()->increment('stok', $pesanan->jumlah);
        }

        $pesanan->pembayaran()->delete();
        $pesanan->delete();
        return back()->with('success', 'Pesanan berhasil dihapus');
    }
}