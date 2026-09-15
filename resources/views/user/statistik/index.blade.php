@extends('layouts.app')
@section('title', 'Statistik Belanja - Graniva')

@section('content')
<h3 class="fw-bold mb-1">Statistik Belanja</h3>
<p class="text-muted mb-4">Ringkasan aktivitas belanja kamu di Graniva</p>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card p-3 text-center">
            <i class="bi bi-bag fs-3" style="color:var(--primary)"></i>
            <p class="small text-muted mt-2 mb-1">Total Pesanan</p>
            <h3 class="fw-bold mb-0">{{ $stats['total_pesanan'] }}</h3>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3 text-center">
            <i class="bi bi-wallet2 fs-3" style="color:var(--primary)"></i>
            <p class="small text-muted mt-2 mb-1">Total Belanja</p>
            <h5 class="fw-bold mb-0 harga">Rp {{ number_format($stats['total_belanja'], 0, ',', '.') }}</h5>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3 text-center">
            <i class="bi bi-check2-circle fs-3" style="color:var(--primary)"></i>
            <p class="small text-muted mt-2 mb-1">Pesanan Selesai</p>
            <h3 class="fw-bold mb-0">{{ $stats['pesanan_selesai'] }}</h3>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card p-3 text-center">
            <i class="bi bi-graph-up-arrow fs-3" style="color:var(--primary)"></i>
            <p class="small text-muted mt-2 mb-1">Rata-rata / Pesanan</p>
            <h5 class="fw-bold mb-0 harga">Rp {{ $stats['total_pesanan'] ? number_format($stats['total_belanja'] / $stats['total_pesanan'], 0, ',', '.') : 0 }}</h5>
        </div>
    </div>
</div>

<div class="card p-4">
    <h6 class="fw-bold mb-3">Riwayat Belanja</h6>
    @forelse($pesanans as $p)
    <div class="d-flex justify-content-between align-items-center border-bottom py-2 small">
        <span>{{ $p->produk->nama_produk }} ×{{ $p->jumlah }}</span>
        <span class="harga">Rp {{ number_format($p->harga, 0, ',', '.') }}</span>
    </div>
    @empty
    <p class="text-muted small mb-0">Belum ada riwayat belanja.</p>
    @endforelse
</div>
@endsection