@extends('layouts.app')
@section('title', 'Notifikasi - Graniva')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Notifikasi</h3>
        <p class="text-muted mb-0">Update pesanan & aktivitas akunmu</p>
    </div>
    <form method="POST" action="{{ route('notifikasi.markAll') }}">
        @csrf
        <button class="btn btn-sm btn-outline-primary"><i class="bi bi-check-all me-1"></i>Tandai semua dibaca</button>
    </form>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@forelse($notifs as $n)
<div class="card p-3 mb-2 {{ $n->is_read ? '' : 'border-start border-4' }}" style="{{ $n->is_read ? '' : 'border-color:var(--primary) !important' }}">
    <div class="d-flex justify-content-between align-items-start gap-2">
        <div>
            <p class="fw-bold mb-1">
                <i class="bi {{ $n->tipe === 'success' ? 'bi-check-circle-fill text-success' : ($n->tipe === 'error' ? 'bi-x-circle-fill text-danger' : 'bi-bell-fill text-warning') }} me-1"></i>
                {{ $n->judul }}
            </p>
            <p class="small text-muted mb-1">{{ $n->pesan }}</p>
            <p class="small text-muted mb-0"><i class="bi bi-clock me-1"></i>{{ $n->created_at->diffForHumans() }}</p>
        </div>
        @unless($n->is_read)
        <form method="POST" action="{{ route('notifikasi.read', $n->id_notifikasi) }}">
            @csrf
            <button class="btn btn-sm btn-outline-secondary">Baca</button>
        </form>
        @endunless
    </div>
</div>
@empty
<div class="card"><div class="card-body text-center text-muted py-5">
    <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>Tidak ada notifikasi.
</div></div>
@endforelse
@endsection