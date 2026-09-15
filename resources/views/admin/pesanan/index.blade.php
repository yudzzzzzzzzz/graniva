@extends('layouts.admin')
@section('title', 'Pesanan - Admin Graniva')
@section('page-title', 'Manajemen Pesanan')

@section('content')
@php
    $statusColor = [
        'menunggu_pembayaran' => 'warning',
        'diproses' => 'info',
        'dikirim' => 'primary',
        'sampai' => 'success',
        'dibatalkan' => 'danger',
    ];
@endphp

{{-- Kartu Statistik --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md">
        <div class="stat-card" style="background:linear-gradient(135deg,#b45a3c,#96482e)">
            <div class="small opacity-75">Total Pesanan</div>
            <h3 class="fw-bold mb-0">{{ $stats['total'] }}</h3>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
            <div class="small opacity-75">Menunggu</div>
            <h3 class="fw-bold mb-0">{{ $stats['menunggu_pembayaran'] }}</h3>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card" style="background:linear-gradient(135deg,#0ea5e9,#0284c7)">
            <div class="small opacity-75">Diproses</div>
            <h3 class="fw-bold mb-0">{{ $stats['diproses'] }}</h3>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed)">
            <div class="small opacity-75">Dikirim</div>
            <h3 class="fw-bold mb-0">{{ $stats['dikirim'] }}</h3>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="stat-card" style="background:linear-gradient(135deg,#28a745,#1e7e34)">
            <div class="small opacity-75">Selesai</div>
            <h3 class="fw-bold mb-0">{{ $stats['sampai'] }}</h3>
        </div>
    </div>
</div>

{{-- Filter Status --}}
<div class="card p-3 mb-3">
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <a href="{{ route('admin.pesanan.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">Semua</a>
        @foreach($statusColor as $key => $color)
        <a href="{{ route('admin.pesanan.index', ['status' => $key]) }}"
           class="btn btn-sm {{ request('status') == $key ? 'btn-primary' : 'btn-outline-secondary' }}">
            {{ ucfirst(str_replace('_', ' ', $key)) }}
        </a>
        @endforeach
    </div>
</div>

{{-- Tabel Pesanan --}}
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Pesanan</th>
                    <th>User</th>
                    <th>Produk</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesanans as $p)
                <tr>
                    <td class="ps-4">
                        <strong class="small">#{{ $p->id_pesanan }}</strong>
                        <div class="text-muted" style="font-size:.72rem">{{ $p->created_at->format('d M Y, H:i') }}</div>
                    </td>
                    <td class="small">{{ $p->user->nama ?? '-' }}</td>
                    <td class="small">{{ $p->produk->nama_produk ?? '-' }} <span class="text-muted">×{{ $p->jumlah }}</span></td>
                    <td class="harga small">Rp {{ number_format($p->harga + ($p->ongkir ?? 0), 0, ',', '.') }}</td>
                    <td>
                        @php $sb = $p->pembayaran->status_bayar ?? 'pending'; @endphp
                        @if($sb == 'sukses') <span class="badge bg-success">Lunas</span>
                        @elseif($sb == 'gagal') <span class="badge bg-danger">Gagal</span>
                        @else <span class="badge bg-warning text-dark">Pending</span> @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $statusColor[$p->status_pesanan] ?? 'secondary' }}">
                            {{ ucfirst(str_replace('_', ' ', $p->status_pesanan)) }}
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <a href="{{ route('admin.pesanan.show', $p->id_pesanan) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-eye me-1"></i>Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada pesanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $pesanans->links() }}</div>
@endsection