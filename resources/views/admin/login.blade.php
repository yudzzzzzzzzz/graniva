@extends('layouts.auth')
@section('title', 'Login Admin - Granivaa')

@section('content')
<img src="{{ asset('images/logo.jpeg') }}" alt="Logo Granivaa" class="auth-logo" onerror="this.style.display='none'">
<h4 class="auth-title">Masuk Admin</h4>
<p class="auth-sub mb-0">Area khusus pengelola Granivaa</p>
<div class="title-accent"></div>

<form method="POST" action="{{ route('admin.login') }}" data-ajax data-redirect="{{ route('admin.dashboard') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Email</label>
        <div class="field">
            <i class="bi bi-envelope bi-lead"></i>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="admin@granivaa.com" required autofocus>
        </div>
    </div>
    <div class="mb-4">
        <label class="form-label">Password</label>
        <div class="field">
            <i class="bi bi-lock bi-lead"></i>
            <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
            <button type="button" class="toggle-pass" data-target="password"><i class="bi bi-eye"></i></button>
        </div>
    </div>
    <button type="submit" class="btn-auth">
        <i class="bi bi-shield-lock me-1"></i>Masuk sebagai Admin
    </button>
</form>
<p class="text-center small mt-4 mb-0 text-muted">Bukan admin? <a class="link-auth" href="{{ route('login') }}">Masuk sebagai user</a></p>
@endsection