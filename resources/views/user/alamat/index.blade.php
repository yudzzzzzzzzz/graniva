@extends('layouts.app')
@section('title', 'Alamat Saya - Graniva')

@section('content')
<h3 class="fw-bold mb-1">Alamat Saya</h3>
<p class="text-muted small mb-4">Kelola alamat pengiriman kamu</p>

@if(session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="row g-4">
    {{-- Form Tambah Alamat --}}
    <div class="col-lg-5">
        <div class="card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle me-1"></i>Tambah Alamat</h6>
            <form method="POST" action="{{ route('alamat.store') }}">
                @csrf
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label small">Label</label>
                        <input type="text" name="label" class="form-control" placeholder="Rumah / Kantor" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Nama Penerima</label>
                        <input type="text" name="penerima" class="form-control" required>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small">No. HP</label>
                    <input type="text" name="no_hp" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Alamat Lengkap</label>
                    <textarea name="alamat_lengkap" class="form-control" rows="2" required></textarea>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-8">
                        <label class="form-label small">Kota</label>
                        <input type="text" name="kota" class="form-control" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label small">Kode Pos</label>
                        <input type="text" name="kode_pos" class="form-control">
                    </div>
                </div>
                <button class="btn btn-primary w-100"><i class="bi bi-save me-1"></i>Simpan Alamat</button>
            </form>
        </div>
    </div>

    {{-- Daftar Alamat --}}
    <div class="col-lg-7">
        @forelse($alamats as $a)
        <div class="card p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="mb-1">
                        <span class="badge badge-soft">{{ $a->label }}</span>
                        @if($a->is_default)
                            <span class="badge bg-primary ms-1">Default</span>
                        @endif
                    </div>
                    <p class="fw-semibold mb-1">{{ $a->penerima }} · {{ $a->no_hp }}</p>
                    <p class="small text-muted mb-0">{{ $a->alamat_lengkap }}, {{ $a->kota }} {{ $a->kode_pos }}</p>
                </div>
                <div class="d-flex gap-1">
                    @if(!$a->is_default)
                    <form method="POST" action="{{ route('alamat.setDefault', $a->id_alamat) }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-primary" title="Jadikan default">
                            <i class="bi bi-star"></i>
                        </button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('alamat.destroy', $a->id_alamat) }}"
                          onsubmit="return confirm('Hapus alamat {{ $a->label }}?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" title="Hapus alamat">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-geo-alt fs-1 d-block mb-2"></i>
                Belum ada alamat. Tambahkan di form sebelah kiri.
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection