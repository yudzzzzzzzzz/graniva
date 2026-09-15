@extends('layouts.admin')
@section('title', 'Produk - Admin Graniva')
@section('page-title', 'Manajemen Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="q" class="form-control" placeholder="Cari produk..." value="{{ request('q') }}">
        <select name="kategori" class="form-select" style="width:180px">
            <option value="">Semua Kategori</option>
            @foreach($kategoris as $k)
            <option value="{{ $k->id_kategori }}" {{ request('kategori') == $k->id_kategori ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary"><i class="bi bi-search"></i></button>
    </form>
    <a href="{{ route('admin.produk.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Tambah Produk</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr>
                <th class="ps-4">Produk</th><th>Kategori</th><th>Ukuran</th><th>Stok</th><th>Harga</th><th class="text-end pe-4">Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($produks as $produk)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-2">
                            @if($produk->gambar)<img src="{{ Storage::url($produk->gambar) }}" style="width:44px;height:44px;object-fit:cover;border-radius:8px">@endif
                            <span class="fw-semibold">{{ $produk->nama_produk }}</span>
                        </div>
                    </td>
                    <td><span class="badge badge-soft">{{ $produk->kategori->nama_kategori ?? $produk->jenis ?? '-' }}</span></td>
                    <td>{{ $produk->ukuran ?? '-' }}</td>
                    <td><span class="badge {{ $produk->stok <= 5 ? 'bg-warning text-dark' : 'bg-light text-dark' }}">{{ $produk->stok }}</span></td>
                    <td class="harga">Rp {{ number_format($produk->harga,0,',','.') }}</td>
                    <td class="text-end pe-4">
                        <a href="{{ route('admin.produk.edit', $produk->id_produk) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.produk.destroy', $produk->id_produk) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada produk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $produks->links() }}</div>
@endsection