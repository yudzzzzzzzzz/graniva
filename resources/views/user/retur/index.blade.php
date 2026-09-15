@extends('layouts.app')
@section('title', 'Retur Saya - Graniva')

@section('content')
<h3 class="fw-bold mb-4">Pengajuan Retur Saya</h3>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@forelse($returs as $r)
<div class="card p-3 mb-3">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h6 class="fw-bold mb-1">Retur #{{ $r->id_retur }} · {{ $r->pesanan->produk->nama_produk }}</h6>
            <p class="small text-muted mb-1">Pesanan #{{ $r->id_pesanan }} · {{ $r->created_at->diffForHumans() }}</p>
            <p class="small mb-0">{{ $r->alasan }}</p>
        </div>
        @if($r->status === 'pending')
            <span class="badge bg-warning text-dark">Pending</span>
        @elseif($r->status === 'disetujui')
            <span class="badge bg-success">Disetujui</span>
        @else
            <span class="badge bg-danger">Ditolak</span>
        @endif
    </div>
</div>
@empty
<div class="card"><div class="card-body text-center text-muted py-5">
    <i class="bi bi-arrow-return-left fs-1 d-block mb-2"></i>Belum ada pengajuan retur.
</div></div>
@endforelse
@endsection