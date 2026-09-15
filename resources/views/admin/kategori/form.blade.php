@extends('layouts.admin')
@section('title', 'Form Kategori - Admin Graniva')
@section('page-title', $kategori ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
<div class="card p-4" style="max-width:600px">
    <form method="POST" enctype="multipart/form-data"
          action="{{ $kategori ? route('admin.kategori.update', $kategori->id_kategori) : route('admin.kategori.store') }}">
        @csrf
        @if($kategori) @method('PUT') @endif

        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <div class="mb-3">
            <label class="form-label">Nama Kategori</label>
            <input type="text" name="nama_kategori" class="form-control" value="{{ old('nama_kategori', $kategori->nama_kategori ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $kategori->deskripsi ?? '') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Gambar</label>
            <input type="file" name="gambar" class="form-control" accept="image/*">
            @if($kategori?->gambar)
                <img src="{{ Storage::url($kategori->gambar) }}" class="img-fluid rounded mt-2" style="max-height:120px">
            @endif
        </div>
        <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
        <a href="{{ route('admin.kategori.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </form>
</div>
@endsection