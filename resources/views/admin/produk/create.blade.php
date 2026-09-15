@extends('layouts.admin')
@section('title', 'Tambah Produk - Admin Graniva')
@section('page-title', 'Tambah Produk')

@section('content')
<a href="{{ route('admin.produk.index') }}" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>

<div class="card p-4">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.produk.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nama Produk</label>
                <input type="text" name="nama_produk" class="form-control" value="{{ old('nama_produk') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Kategori</label>
                <select name="kategori_id" class="form-select">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoris as $k)
                    <option value="{{ $k->id_kategori }}" {{ old('kategori_id') == $k->id_kategori ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Ukuran</label>
                <input type="text" name="ukuran" class="form-control" value="{{ old('ukuran') }}" placeholder="60x60">
            </div>
            <div class="col-md-3">
                <label class="form-label">Warna</label>
                <input type="text" name="warna" class="form-control" value="{{ old('warna') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Jenis</label>
                <input type="text" name="jenis" class="form-control" value="{{ old('jenis') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tekstur</label>
                <input type="text" name="tekstur" class="form-control" value="{{ old('tekstur') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Stok</label>
                <input type="number" name="stok" class="form-control" value="{{ old('stok', 0) }}" min="0" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Harga (Rp)</label>
                <input type="number" name="harga" class="form-control" value="{{ old('harga') }}" min="0" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Ongkir (Rp) <span class="text-muted">— kosongkan untuk default</span></label>
                <input type="number" name="ongkir" class="form-control" value="{{ old('ongkir') }}" placeholder="0">
            </div>
            <div class="col-12">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Gambar Produk</label>
                <input type="file" name="gambar" class="form-control" accept="image/*">
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-4"><i class="bi bi-save me-1"></i>Simpan Produk</button>
    </form>
</div>
@endsection