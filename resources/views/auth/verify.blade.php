@extends('layouts.auth')
@section('title', 'Verifikasi Email - Graniva')

@section('content')
<style>
    .reg-badge{width:76px;height:76px;border-radius:24px;background:linear-gradient(135deg,var(--primary),#a06b46);display:inline-flex;align-items:center;justify-content:center;box-shadow:0 12px 26px rgba(122,74,43,.28);margin-bottom:14px}
    .steps{display:flex;justify-content:center;gap:6px;margin:16px 0 18px;flex-wrap:wrap}
    .step{display:flex;align-items:center;gap:5px;font-size:.7rem;font-weight:700;color:#b9aea3;background:#f6f1ec;padding:6px 11px;border-radius:999px}
    .step.active{color:#fff;background:linear-gradient(135deg,var(--primary),#a06b46);box-shadow:0 6px 14px rgba(122,74,43,.25)}
    .btn-reg{width:100%;border:none;border-radius:14px;padding:13px;font-weight:700;color:#fff;background:linear-gradient(135deg,var(--primary),#8a5a34);box-shadow:0 10px 22px rgba(122,74,43,.28);transition:.2s}
    .btn-reg:hover{transform:translateY(-2px);color:#fff}
    .code-input{text-align:center;letter-spacing:10px;font-size:1.5rem;font-weight:800;color:var(--primary)}
</style>

<div class="text-center">
    <div class="reg-badge"><i class="bi bi-envelope-check-fill" style="font-size:1.9rem;color:#fff"></i></div>
    <h4 class="auth-title mb-1">Verifikasi Email</h4>
    <p class="auth-sub mb-0">Masukkan kode 8 digit yang kami kirim ke email Anda</p>
</div>

<div class="steps">
    <span class="step"><i class="bi bi-1-circle"></i> Daftar</span>
    <span class="step active"><i class="bi bi-2-circle"></i> Verifikasi Email</span>
    <span class="step"><i class="bi bi-3-circle"></i> Belanja</span>
</div>

@if(session('success'))
<div class="alert alert-success small py-2"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
@endif

@if($errors->any())
<div class="alert alert-danger small py-2">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

<form method="POST" action="{{ route('verify') }}">
    @csrf
    <div class="field mb-3">
        <i class="bi bi-shield-lock"></i>
        <input type="text" name="code" class="form-control code-input" placeholder="00000000"
               maxlength="8" required inputmode="numeric" autocomplete="one-time-code" value="{{ old('code') }}">
    </div>
    <button type="submit" class="btn-reg">
        <i class="bi bi-check-circle me-1"></i>Verifikasi Sekarang
    </button>
</form>

<form method="POST" action="{{ route('resend') }}" class="text-center mt-3">
    @csrf
    <button type="submit" class="btn btn-link small text-decoration-none" style="color:var(--primary)">
        <i class="bi bi-arrow-repeat me-1"></i>Kirim Ulang Kode
    </button>
</form>

<p class="text-center small text-muted mt-2 mb-0">Kode berlaku 10 menit. Cek folder spam jika email tidak ditemukan.</p>

{{-- ===== SOUND ===== --}}
<audio id="snd-ok" src="{{ asset('audio/berhasil.mp4') }}" preload="auto"></audio>
<audio id="snd-no" src="{{ asset('audio/gagal.mp4') }}" preload="auto"></audio>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ok = document.getElementById('snd-ok');
    const no = document.getElementById('snd-no');
    @if($errors->any())
        no && no.play().catch(()=>{});   // 🔴 kode salah / kedaluwarsa
    @endif
    @if(session('success'))
        ok && ok.play().catch(()=>{});   // 🟢 kode terkirim
    @endif
});
</script>
@endsection