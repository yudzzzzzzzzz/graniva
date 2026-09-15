@extends('layouts.auth')
@section('title', 'Login - Graniva')

@section('content')
<img src="{{ asset('images/logo.jpeg') }}" alt="Logo Graniva" class="auth-logo" onerror="this.style.display='none'">
<h4 class="auth-title">Masuk Akun</h4>
<p class="auth-sub mb-0">Selamat datang kembali di Graniva</p>
<div class="title-accent"></div>

@if(session('success'))
    <div class="alert alert-success small">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger small">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('login') }}" data-ajax>
    @csrf
    <div class="mb-3">
        <label class="form-label">Email</label>
        <div class="field">
            <i class="bi bi-envelope bi-lead"></i>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="nama@email.com" required>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="field">
            <i class="bi bi-lock bi-lead"></i>
            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            <button type="button" class="toggle-pass" data-target="password"><i class="bi bi-eye"></i></button>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input type="checkbox" name="remember" id="remember" class="form-check-input">
            <label for="remember" class="form-check-label small">Ingat saya</label>
        </div>
    </div>
    <button type="submit" class="btn-auth">
        <i class="bi bi-box-arrow-in-right me-1"></i>Login Sekarang
    </button>
</form>
<p class="text-center small mt-4 mb-0">Belum punya akun? <a class="link-auth" href="{{ route('register') }}">Daftar di sini</a></p>
@endsection