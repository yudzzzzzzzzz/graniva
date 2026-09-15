@extends('layouts.admin')
@section('title', 'Kategori - Admin Graniva')
@section('page-title', 'Manajemen Kategori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <form method="GET" class="d-flex gap-2" style="max-width:400px">
        <input type="text" name="q" class="form-control" placeholder="Cari kategori..." value="{{ request('q') }}">
        <button class="btn btn-primary"><i class="bi bi-search"></i></button>
    </form>
    <a href="{{ route('admin.kategori.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Tambah Kategori</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr>
                <th class="ps-4">Kategori</th><th>Deskripsi</th><th>Produk</th><th class="text-end pe-4">Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($kategoris as $k)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-2">
                            @if($k->gambar)<img src="{{ Storage::url($k->gambar) }}" style="width:40px;height:40px;object-fit:cover;border-radius:8px">@endif
                            <span class="fw-semibold">{{ $k->nama_kategori }}</span>
                        </div>
                    </td>
                    <td class="small text-muted">{{ Str::limit($k->deskripsi, 50) }}</td>
                    <td><span class="badge badge-soft">{{ $k->produk_count }} produk</span></td>
                    <td class="text-end pe-4">
                        <a href="{{ route('admin.kategori.edit', $k->id_kategori) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.kategori.destroy', $k->id_kategori) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $kategoris->links() }}</div>
@endsection