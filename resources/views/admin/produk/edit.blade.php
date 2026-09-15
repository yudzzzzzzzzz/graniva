@extends('layouts.admin')
@section('title', 'Edit Produk - Admin Graniva')
@section('page-title', 'Edit Produk')

@section('content')
<a href="{{ route('admin.produk.index') }}" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>

<div class="card p-4">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.produk.update', $produk->id_produk) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nama Produk</label>
                <input type="text" name="nama_produk" class="form-control" value="{{ old('nama_produk', $produk->nama_produk) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Kategori</label>
                <select name="kategori_id" class="form-select">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoris as $k)
                    <option value="{{ $k->id_kategori }}" {{ old('kategori_id', $produk->kategori_id) == $k->id_kategori ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Ukuran</label>
                <input type="text" name="ukuran" class="form-control" value="{{ old('ukuran', $produk->ukuran) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Warna</label>
                <input type="text" name="warna" class="form-control" value="{{ old('warna', $produk->warna) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Jenis</label>
                <input type="text" name="jenis" class="form-control" value="{{ old('jenis', $produk->jenis) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tekstur</label>
                <input type="text" name="tekstur" class="form-control" value="{{ old('tekstur', $produk->tekstur) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Stok</label>
                <input type="number" name="stok" class="form-control" value="{{ old('stok', $produk->stok) }}" min="0" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Harga (Rp)</label>
                <input type="number" name="harga" class="form-control" value="{{ old('harga', $produk->harga) }}" min="0" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Ongkir (Rp)</label>
                <input type="number" name="ongkir" class="form-control" value="{{ old('ongkir', $produk->ongkir) }}" placeholder="0">
                <div class="form-check mt-2">
                    <input type="checkbox" name="hapus_ongkir" value="1" class="form-check-input" id="hapus_ongkir">
                    <label class="form-check-label small" for="hapus_ongkir">Hapus ongkir (pakai default)</label>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Gambar Produk</label>
                @if($produk->gambar)
                    <div class="mb-2">
                        <img src="{{ Storage::url($produk->gambar) }}" style="max-height:120px;border-radius:10px" alt="">
                    </div>
                @endif
                <input type="file" name="gambar" class="form-control" accept="image/*">
                <div class="form-text small">Kosongkan kalau nggak mau ganti gambar</div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-4"><i class="bi bi-save me-1"></i>Update Produk</button>
    </form>
</div>
@endsection