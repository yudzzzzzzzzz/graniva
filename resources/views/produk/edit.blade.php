@extends('layouts.app')
@section('title', 'Edit Produk - Granivaa')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="{{ route('produk.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
            <h3 class="fw-bold mb-0">Edit Produk</h3>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="card p-4">
            @if($produk->gambar)
                <img src="{{ Storage::url($produk->gambar) }}" class="rounded mb-3" style="height:180px;object-fit:cover" alt="">
            @endif
            <form action="{{ route('produk.update', $produk->id_produk) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="nama_produk" class="form-control" value="{{ old('nama_produk', $produk->nama_produk) }}" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Ukuran</label><input type="text" name="ukuran" class="form-control" value="{{ old('ukuran', $produk->ukuran) }}"></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Warna</label><input type="text" name="warna" class="form-control" value="{{ old('warna', $produk->warna) }}"></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Jenis</label><input type="text" name="jenis" class="form-control" value="{{ old('jenis', $produk->jenis) }}"></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Tekstur</label><input type="text" name="tekstur" class="form-control" value="{{ old('tekstur', $produk->tekstur) }}"></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Stok</label><input type="number" name="stok" class="form-control" value="{{ old('stok', $produk->stok) }}" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Harga (Rp)</label><input type="number" name="harga" class="form-control" value="{{ old('harga', $produk->harga) }}" required></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                </div>
                <div class="mb-4">
                    <label class="form-label">Gambar (kosongkan jika tidak diganti)</label>
                    <input type="file" name="gambar" class="form-control">
                </div>
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Update</button>
                <a href="{{ route('produk.index') }}" class="btn btn-outline-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection