@extends('layouts.admin')
@section('title', 'Detail Pesanan - Admin Graniva')
@section('page-title', 'Detail Pesanan #'. $pesanan->id_pesanan)

@push('styles')
<style>
    .step-circle{width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1rem;}
    .info-box{background:#faf7f4;border-radius:12px;padding:14px;}
</style>
@endpush

@section('content')
@php
    $statusColor = [
        'menunggu_pembayaran' => 'warning',
        'diproses' => 'info',
        'dikirim' => 'primary',
        'sampai' => 'success',
        'dibatalkan' => 'danger',
    ];
    $statusOptions = [
        'menunggu_pembayaran' => 'Menunggu Pembayaran',
        'diproses' => 'Diproses',
        'dikirim' => 'Dikirim',
        'sampai' => 'Sampai Tujuan',
        'dibatalkan' => 'Dibatalkan',
    ];
    $sb = $pesanan->pembayaran->status_bayar ?? 'pending';
@endphp

<a href="{{ route('admin.pesanan.index') }}" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>

{{-- ===== HEADER PESANAN + UBAH STATUS ===== --}}
<div class="card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h5 class="fw-bold mb-1">Pesanan #{{ $pesanan->id_pesanan }}</h5>
            <p class="small text-muted mb-0">
                <i class="bi bi-person me-1"></i>{{ $pesanan->user->nama ?? '-' }} ·
                <i class="bi bi-calendar me-1"></i>{{ $pesanan->created_at->format('d M Y, H:i') }}
            </p>
        </div>
        <span class="badge bg-{{ $statusColor[$pesanan->status_pesanan] ?? 'secondary' }} fs-6 px-3 py-2">
            {{ ucfirst(str_replace('_', ' ', $pesanan->status_pesanan)) }}
        </span>
    </div>

    <div class="border-top mt-3 pt-3">
        <form method="POST" action="{{ route('admin.pesanan.status', $pesanan->id_pesanan) }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-5">
                <label class="form-label small mb-1"><i class="bi bi-arrow-repeat me-1"></i>Ubah Status Pesanan</label>
                <select name="status" class="form-select">
                    @foreach($statusOptions as $key => $label)
                    <option value="{{ $key }}" {{ $pesanan->status_pesanan == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Status</button>
            </div>
            <div class="col-12"><small class="text-muted">Memilih "Dibatalkan" otomatis mengembalikan stok.</small></div>
        </form>
    </div>
</div>

<div class="row g-4">
    {{-- ===== KOLOM KIRI ===== --}}
    <div class="col-lg-6">
        {{-- Produk --}}
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-box-seam me-1"></i>Produk</h6>
            <div class="d-flex gap-3">
                @if($pesanan->produk->gambar)
                    <img src="{{ Storage::url($pesanan->produk->gambar) }}" style="width:70px;height:70px;object-fit:cover;border-radius:10px" alt="">
                @else
                    <div class="d-flex align-items-center justify-content-center" style="width:70px;height:70px;border-radius:10px;background:#eee"><i class="bi bi-image text-muted"></i></div>
                @endif
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-1">{{ $pesanan->produk->nama_produk ?? '-' }}</h6>
                    <p class="small text-muted mb-1">{{ $pesanan->jumlah }} pcs × Rp {{ number_format($pesanan->produk->harga ?? 0, 0, ',', '.') }}</p>
                    <p class="harga mb-0">Rp {{ number_format($pesanan->harga + ($pesanan->ongkir ?? 0), 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Alamat --}}
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-2"><i class="bi bi-geo-alt me-1"></i>Alamat Tujuan</h6>
            <div class="info-box">
                @if($pesanan->alamat_pengiriman)
                    <p class="small mb-0">{{ $pesanan->alamat_pengiriman }}</p>
                @elseif($pesanan->alamat)
                    <p class="small mb-1"><strong>{{ $pesanan->alamat->penerima }}</strong> · {{ $pesanan->alamat->no_hp }}</p>
                    <p class="small text-muted mb-0">{{ $pesanan->alamat->alamat_lengkap }}, {{ $pesanan->alamat->kota }}</p>
                @else
                    <p class="small text-muted mb-0">-</p>
                @endif
            </div>
        </div>

        {{-- ===== ALASAN PEMBATALAN (BARU) ===== --}}
        @if($pesanan->alasan_batal)
        <div class="card p-4 mb-3" style="border-left:4px solid #dc3545">
            <h6 class="fw-bold mb-2"><i class="bi bi-chat-left-text me-1"></i>Alasan Pembatalan</h6>
            <div class="alert alert-danger small mb-0">{{ $pesanan->alasan_batal }}</div>
        </div>
        @endif

        {{-- Kendala --}}
        <div class="card p-4">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle me-1"></i>Kendala Pengiriman</h6>
            <form method="POST" action="{{ route('admin.pesanan.kendala', $pesanan->id_pesanan) }}">
                @csrf
                <div class="input-group input-group-sm">
                    <input type="text" name="kendala" class="form-control" placeholder="Catat kendala..." value="{{ $pesanan->kendala_pengiriman }}">
                    <button class="btn btn-warning">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== KOLOM KANAN ===== --}}
    <div class="col-lg-6">
        {{-- Verifikasi Pembayaran --}}
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-credit-card me-1"></i>Verifikasi Pembayaran</h6>
            <p class="small mb-2">Metode: <strong>{{ $pesanan->pembayaran->metode ?? '-' }}</strong> ·
                @if($sb == 'sukses') <span class="badge bg-success">Lunas</span>
                @elseif($sb == 'gagal') <span class="badge bg-danger">Gagal</span>
                @elseif($sb == 'refund') <span class="badge bg-info">Refund GraPay</span>
                @else <span class="badge bg-warning text-dark">Pending</span> @endif
            </p>

            @if($pesanan->pembayaran && $pesanan->pembayaran->bukti_bayar)
                <img src="{{ Storage::url($pesanan->pembayaran->bukti_bayar) }}" style="max-height:180px;border-radius:10px;cursor:pointer" onclick="window.open(this.src,'_blank')" class="mb-3">
            @endif

            @if($sb == 'pending')
            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('admin.pesanan.verify', $pesanan->id_pesanan) }}">
                    @csrf
                    <input type="hidden" name="aksi" value="terima">
                    <button class="btn btn-sm btn-success"><i class="bi bi-check me-1"></i>Terima</button>
                </form>
                <form method="POST" action="{{ route('admin.pesanan.verify', $pesanan->id_pesanan) }}" onsubmit="return confirm('Tolak pembayaran ini?')">
                    @csrf
                    <input type="hidden" name="aksi" value="tolak">
                    <button class="btn btn-sm btn-danger"><i class="bi bi-x me-1"></i>Tolak</button>
                </form>
            </div>
            @endif
        </div>

        {{-- Pengiriman --}}
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-truck me-1"></i>Pengiriman</h6>

            @if($pesanan->kurir)
            <div class="info-box mb-3">
                <div class="row small g-2">
                    <div class="col-6"><span class="text-muted d-block">Kurir</span><strong>{{ $pesanan->kurir }}</strong></div>
                    <div class="col-6"><span class="text-muted d-block">No. Resi</span><strong>{{ $pesanan->no_resi }}</strong></div>
                    <div class="col-6"><span class="text-muted d-block">Estimasi</span><strong>{{ $pesanan->estimasi_datang ? \Carbon\Carbon::parse($pesanan->estimasi_datang)->format('d M Y') : '-' }}</strong></div>
                </div>
            </div>
            @endif

            @if($sb == 'sukses' && $pesanan->status_pesanan == 'diproses')
            <form method="POST" action="{{ route('admin.pesanan.shipping', $pesanan->id_pesanan) }}">
                @csrf
                <div class="row g-2 mb-2">
                    <div class="col-6"><input type="text" name="kurir" class="form-control" placeholder="Kurir (JNE/Sicepat...)" required></div>
                    <div class="col-6"><input type="text" name="no_resi" class="form-control" placeholder="No. Resi" required></div>
                </div>
                <div class="mb-2"><input type="date" name="estimasi_datang" class="form-control"></div>
                <button class="btn btn-primary btn-sm w-100"><i class="bi bi-truck me-1"></i>Tandai Dikirim</button>
            </form>
            @endif

            @if($pesanan->status_pesanan == 'dikirim')
            <form method="POST" action="{{ route('admin.pesanan.arrive', $pesanan->id_pesanan) }}" onsubmit="return confirm('Tandai pesanan sampai tujuan?')">
                @csrf
                <button class="btn btn-success btn-sm w-100"><i class="bi bi-house-check me-1"></i>Tandai Sampai Tujuan</button>
            </form>
            @endif
        </div>

        {{-- Timeline --}}
        <div class="card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-1"></i>Timeline</h6>
            @php
                $urutan = ['menunggu_pembayaran', 'diproses', 'dikirim', 'sampai'];
                $currentIndex = array_search($pesanan->status_pesanan, $urutan);
                if ($currentIndex === false) $currentIndex = -1;
            @endphp
            @foreach($urutan as $i => $key)
            @php $aktif = $i <= $currentIndex; @endphp
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="step-circle {{ $aktif ? 'text-white' : 'bg-light text-muted border' }}" style="{{ $aktif ? 'background:var(--primary)' : '' }}">
                    <i class="bi {{ ['bi-wallet2','bi-box-seam','bi-truck','bi-house-check'][$i] }}"></i>
                </div>
                <p class="fw-semibold mb-0 small {{ $aktif ? '' : 'text-muted' }}">{{ $statusOptions[$key] }}</p>
                @if($aktif && $i == $currentIndex)
                <span class="badge bg-success ms-auto">Saat ini</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection