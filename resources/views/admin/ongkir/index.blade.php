@extends('layouts.admin')
@section('title', 'Kelola Ongkir - Admin Graniva')
@section('page-title', 'Manajemen Ongkos Kirim')

@section('content')
{{-- Ongkir Default (Semua Kota) --}}
<div class="card p-4 mb-4" style="border-left:5px solid var(--primary)">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h6 class="fw-bold mb-1"><i class="bi bi-globe me-1"></i>Ongkir Default (Semua Kota)</h6>
            <p class="small text-muted mb-0">
                Berlaku untuk <strong>semua kota</strong> yang belum ada di daftar bawah. 
                Cocok biar nggak perlu input kota satu-satu.
            </p>
        </div>
        <form method="POST" action="{{ route('admin.ongkir.default') }}" class="d-flex gap-2 align-items-end">
            @csrf
            <div>
                <label class="form-label small mb-1">Biaya (Rp)</label>
                <input type="number" name="biaya" class="form-control" style="width:180px" 
                       value="{{ $defaultOngkir->biaya ?? '' }}" placeholder="15000" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>Simpan Default
            </button>
        </form>
    </div>

    @if($defaultOngkir)
    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
        <span class="badge badge-soft">
            Ongkir default aktif: Rp {{ number_format($defaultOngkir->biaya, 0, ',', '.') }}
        </span>
        <form method="POST" action="{{ route('admin.ongkir.destroyDefault') }}" 
              onsubmit="return confirm('Hapus ongkir default? Kota yang belum terdaftar tidak akan punya ongkir lagi.')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash me-1"></i>Hapus Default
            </button>
        </form>
    </div>
    @endif
</div>

<div class="row g-4">
    {{-- Form Tambah Kota Spesifik --}}
    <div class="col-lg-4">
        <div class="card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle me-1"></i>Tambah Kota Spesifik</h6>
            <form method="POST" action="{{ route('admin.ongkir.store') }}">
                @csrf
                <div class="mb-2">
                    <label class="form-label small">Nama Kota</label>
                    <input type="text" name="kota" class="form-control" required placeholder="Jakarta">
                </div>
                <div class="mb-3">
                    <label class="form-label small">Biaya (Rp)</label>
                    <input type="number" name="biaya" class="form-control" required placeholder="20000">
                </div>
                <button class="btn btn-primary w-100"><i class="bi bi-save me-1"></i>Simpan Kota</button>
            </form>
            <p class="small text-muted mt-3 mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Kalau kota user <strong>tidak ada</strong> di daftar, otomatis pakai <strong>ongkir default</strong> di atas.
                Kalau default dihapus, kota yang nggak terdaftar ongkirnya jadi Rp 0.
            </p>
        </div>
    </div>

    {{-- Daftar Kota --}}
    <div class="col-lg-8">
        <div class="card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-truck me-1"></i>Daftar Ongkir Kota</h6>
            <table class="table table-hover align-middle">
                <thead><tr><th>Kota</th><th>Biaya</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse($ongkirs as $o)
                    <tr>
                        <td>{{ $o->kota }}</td>
                        <td class="harga">Rp {{ number_format($o->biaya, 0, ',', '.') }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('admin.ongkir.destroy', $o->id_ongkir) }}" class="d-inline" onsubmit="return confirm('Hapus ongkir kota {{ $o->kota }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-3">Belum ada kota spesifik.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $ongkirs->links() }}
        </div>
    </div>
</div>
@endsection