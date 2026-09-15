@extends('layouts.app')
@section('title', 'Profil - Graniva')

@section('content')
<h3 class="fw-bold mb-1">Profil Saya</h3>
<p class="text-muted mb-4">Kelola informasi akun kamu</p>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-person me-1"></i>Informasi Profil</h6>
            <form method="POST" action="{{ route('profil.update') }}">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">No. HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $user->no_hp) }}">
                </div>
                <div class="mb-4">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $user->alamat) }}</textarea>
                </div>
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
            </form>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-shield-lock me-1"></i>Ganti Password</h6>
            <form method="POST" action="{{ route('profil.password') }}">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Password Lama</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button class="btn btn-primary"><i class="bi bi-key me-1"></i>Ganti Password</button>
            </form>
        </div>
    </div>
</div>
@endsection