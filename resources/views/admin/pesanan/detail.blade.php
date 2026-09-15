@extends('layouts.admin')
@section('title', 'Detail Pesanan - Admin Graniva')
@section('page-title', 'Detail Pesanan #'.$pesanan->id_pesanan)

@section('content')
<a href="{{ route('admin.pesanan.index') }}" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1">Pesanan #{{ $pesanan->id_pesanan }}</h4>
        <p class="text-muted mb-0">{{ $pesanan->produk->nama_produk }} · {{ $pesanan->user->nama ?? '-' }}</p>
    </div>
    <span class="badge badge-soft fs-6">{{ str_replace('_', ' ', $pesanan->status_pesanan) }}</span>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row g-4">
    {{-- Kolom kiri: pembayaran + alamat + cancel --}}
    <div class="col-lg-6">
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-credit-card me-1"></i>Pembayaran</h6>
            <p class="small mb-1"><span class="text-muted">Metode:</span> <strong>{{ $pesanan->pembayaran->metode ?? '-' }}</strong></p>
            <p class="small mb-2"><span class="text-muted">Total:</span> <strong class="harga">Rp {{ number_format($pesanan->harga, 0, ',', '.') }}</strong></p>

            @if($pesanan->pembayaran?->bukti_bayar)
            <p class="small text-muted mb-1">Bukti Transfer:</p>
            <img src="{{ Storage::url($pesanan->pembayaran->bukti_bayar) }}" class="img-fluid rounded mb-3" style="max-height:250px" alt="Bukti">
            @endif

            @php $sb = $pesanan->pembayaran?->status_bayar ?? 'pending'; @endphp
            @if($sb === 'pending')
                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('admin.pesanan.verify', $pesanan->id_pesanan) }}">
                        @csrf
                        <input type="hidden" name="action" value="approve">
                        <button class="btn btn-success btn-sm"><i class="bi bi-check-lg me-1"></i>Setujui</button>
                    </form>
                    <form method="POST" action="{{ route('admin.pesanan.verify', $pesanan->id_pesanan) }}">
                        @csrf
                        <input type="hidden" name="action" value="reject">
                        <button class="btn btn-danger btn-sm"><i class="bi bi-x-lg me-1"></i>Tolak</button>
                    </form>
                </div>
            @elseif($sb === 'sukses')
                <span class="badge bg-success">Pembayaran Lunas</span>
            @else
                <span class="badge bg-danger">Pembayaran Ditolak</span>
            @endif
        </div>

        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt me-1"></i>Alamat Pengiriman</h6>
            @if($pesanan->alamat)
                <p class="fw-semibold mb-1">{{ $pesanan->alamat->penerima }}</p>
                <p class="small text-muted mb-1">{{ $pesanan->alamat->alamat_lengkap }}</p>
                <p class="small text-muted mb-0">{{ $pesanan->alamat->kota }}, {{ $pesanan->alamat->kode_pos }} · {{ $pesanan->alamat->no_hp }}</p>
            @else
                <p class="text-muted small mb-0">Alamat tidak tersedia.</p>
            @endif
        </div>

        <div class="card p-4 border-danger">
            <h6 class="fw-bold mb-3 text-danger"><i class="bi bi-x-circle me-1"></i>Batalkan Pesanan</h6>
            <form method="POST" action="{{ route('admin.pesanan.cancel', $pesanan->id_pesanan) }}" onsubmit="return confirm('Batalkan pesanan ini? Stok akan dikembalikan.')">
                @csrf
                <div class="mb-2">
                    <label class="form-label small">Alasan Pembatalan</label>
                    <textarea name="alasan" class="form-control" rows="2" required placeholder="Contoh: stok habis, permintaan user..."></textarea>
                </div>
                <button class="btn btn-danger btn-sm w-100"><i class="bi bi-x-circle me-1"></i>Batalkan Pesanan & Kembalikan Stok</button>
            </form>
        </div>
    </div>

    {{-- Kolom kanan: pengiriman + kendala --}}
    <div class="col-lg-6">
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-truck me-1"></i>Atur Pengiriman</h6>
            <form method="POST" action="{{ route('admin.pesanan.shipping', $pesanan->id_pesanan) }}">
                @csrf
                <div class="mb-2">
                    <label class="form-label small">Kurir</label>
                    <input type="text" name="kurir" class="form-control" value="{{ $pesanan->kurir }}" placeholder="JNE / JNT / SiCepat">
                </div>
                <div class="mb-2">
                    <label class="form-label small">No. Resi</label>
                    <input type="text" name="no_resi" class="form-control" value="{{ $pesanan->no_resi }}">
                </div>
                <div class="mb-2">
                    <label class="form-label small">Estimasi Tiba</label>
                    <input type="date" name="estimasi_datang" class="form-control" value="{{ $pesanan->estimasi_datang }}">
                </div>
                <div class="mb-2">
                    <label class="form-label small">Status Pesanan</label>
                    <select name="status_pesanan" class="form-control" required>
                        <option value="menunggu_pembayaran" {{ $pesanan->status_pesanan=='menunggu_pembayaran'?'selected':'' }}>Menunggu Pembayaran</option>
                        <option value="menunggu_konfirmasi" {{ $pesanan->status_pesanan=='menunggu_konfirmasi'?'selected':'' }}>Menunggu Konfirmasi</option>
                        <option value="diproses" {{ $pesanan->status_pesanan=='diproses'?'selected':'' }}>Diproses</option>
                        <option value="dikirim" {{ $pesanan->status_pesanan=='dikirim'?'selected':'' }}>Dikirim</option>
                        <option value="sampai" {{ $pesanan->status_pesanan=='sampai'?'selected':'' }}>Sampai</option>
                        <option value="selesai" {{ $pesanan->status_pesanan=='selesai'?'selected':'' }}>Selesai</option>
                        <option value="dibatalkan" {{ $pesanan->status_pesanan=='dibatalkan'?'selected':'' }}>Dibatalkan</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Catatan (opsional)</label>
                    <input type="text" name="catatan" class="form-control" placeholder="Paket diserahkan ke kurir">
                </div>
                <button class="btn btn-primary btn-sm w-100"><i class="bi bi-save me-1"></i>Simpan Pengiriman</button>
            </form>
        </div>

        <div class="card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-exclamation-triangle me-1"></i>Catat Kendala</h6>
            <form method="POST" action="{{ route('admin.pesanan.kendala', $pesanan->id_pesanan) }}">
                @csrf
                <textarea name="kendala_pengiriman" class="form-control mb-2" rows="2" placeholder="Deskripsi kendala pengiriman...">{{ $pesanan->kendala_pengiriman }}</textarea>
                <button class="btn btn-warning btn-sm w-100">Simpan Kendala</button>
            </form>
        </div>
    </div>
</div>
@endsection