@extends('layouts.auth')
@section('title', 'Daftar - Graniva')

@section('content')
<style>
    .reg-badge{width:76px;height:76px;border-radius:24px;background:linear-gradient(135deg,var(--primary),#a06b46);display:inline-flex;align-items:center;justify-content:center;box-shadow:0 12px 26px rgba(122,74,43,.28);margin-bottom:14px}
    .steps{display:flex;justify-content:center;gap:6px;margin:16px 0 18px;flex-wrap:wrap}
    .step{display:flex;align-items:center;gap:5px;font-size:.7rem;font-weight:700;color:#b9aea3;background:#f6f1ec;padding:6px 11px;border-radius:999px}
    .step.active{color:#fff;background:linear-gradient(135deg,var(--primary),#a06b46);box-shadow:0 6px 14px rgba(122,74,43,.25)}
    .reg-note{background:#faf7f4;border:1px dashed #e0d4c8;border-radius:12px;padding:10px 14px;font-size:.76rem;color:#8a7f74;display:flex;gap:8px;align-items:center;margin-bottom:18px;text-align:left}
    .btn-reg{width:100%;border:none;border-radius:14px;padding:13px;font-weight:700;color:#fff;background:linear-gradient(135deg,var(--primary),#8a5a34);box-shadow:0 10px 22px rgba(122,74,43,.28);transition:.2s}
    .btn-reg:hover{transform:translateY(-2px);box-shadow:0 14px 28px rgba(122,74,43,.36);color:#fff}
    .lbl{font-size:.75rem;font-weight:700;color:#6d645c;margin-bottom:5px}
</style>

<div class="text-center">
    <div class="reg-badge"><i class="bi bi-person-plus-fill" style="font-size:1.9rem;color:#fff"></i></div>
    <h4 class="auth-title mb-1">Buat Akun Graniva</h4>
    <p class="auth-sub mb-0">Satu akun untuk semua kebutuhan keramik Anda</p>
</div>

<div class="steps">
    <span class="step active"><i class="bi bi-1-circle"></i> Daftar</span>
    <span class="step"><i class="bi bi-2-circle"></i> Verifikasi Email</span>
    <span class="step"><i class="bi bi-3-circle"></i> Belanja</span>
</div>

<div class="reg-note">
    <i class="bi bi-shield-check" style="color:var(--primary);font-size:1rem"></i>
    Akun aktif setelah verifikasi email — data Anda aman bersama kami.
</div>

@if($errors->any())
<div class="alert alert-danger small py-2">
    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf
    <div class="mb-3">
        <div class="lbl">Nama Lengkap</div>
        <div class="field"><i class="bi bi-person"></i>
            <input type="text" name="nama" class="form-control" placeholder="Contoh: Yudzz Starboy" required value="{{ old('nama') }}">
        </div>
    </div>
    <div class="mb-3">
        <div class="lbl">Alamat Email</div>
        <div class="field"><i class="bi bi-envelope"></i>
            <input type="email" name="email" class="form-control" placeholder="nama@gmail.com" required value="{{ old('email') }}">
        </div>
    </div>
    <div class="mb-3">
        <div class="lbl">Nomor HP (WhatsApp)</div>
        <div class="field"><i class="bi bi-phone"></i>
            <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx" required value="{{ old('no_hp') }}">
        </div>
    </div>
    <div class="mb-3">
        <div class="lbl">Alamat Lengkap</div>
        <div class="field"><i class="bi bi-geo-alt"></i>
            <textarea name="alamat" class="form-control" rows="2" placeholder="Jalan, kota, kode pos" required style="padding-left:2.6rem">{{ old('alamat') }}</textarea>
        </div>
    </div>
    <div class="mb-3">
        <div class="lbl">Password</div>
        <div class="field"><i class="bi bi-lock"></i>
            <input type="password" name="password" id="reg-pass" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
            <button type="button" class="toggle-pass" onclick="rpToggle(event,'reg-pass')"><i class="bi bi-eye"></i></button>
        </div>
    </div>
    <div class="mb-3">
        <div class="lbl">Ulangi Password</div>
        <div class="field"><i class="bi bi-lock-fill"></i>
            <input type="password" name="password_confirmation" id="reg-conf" class="form-control" placeholder="Ketik ulang password" required minlength="6">
            <button type="button" class="toggle-pass" onclick="rpToggle(event,'reg-conf')"><i class="bi bi-eye"></i></button>
        </div>
    </div>
    <button type="submit" class="btn-reg mt-2">
        <i class="bi bi-person-plus me-1"></i>Daftar Sekarang
    </button>
</form>

<p class="text-center small mt-4 mb-0 text-muted">
    Sudah punya akun? <a href="{{ route('login') }}" class="auth-link">Masuk di sini</a>
</p>

{{-- ===== SOUND BERHASIL & GAGAL ===== --}}
<audio id="snd-ok" src="{{ asset('audio/berhasil.mp4') }}" preload="auto"></audio>
<audio id="snd-no" src="{{ asset('audio/gagal.mp4') }}" preload="auto"></audio>

<script>
window.rpToggle = function(e, id){
    e.preventDefault(); e.stopPropagation();
    if (e.stopImmediatePropagation) e.stopImmediatePropagation();
    var input = document.getElementById(id);
    var icon = e.currentTarget.querySelector('i');
    if (input.type === 'password') { input.type='text'; icon.className='bi bi-eye-slash'; }
    else { input.type='password'; icon.className='bi bi-eye'; }
};
document.addEventListener('DOMContentLoaded', () => {
    const ok = document.getElementById('snd-ok');
    const no = document.getElementById('snd-no');
    @if($errors->any())
        no && no.play().catch(()=>{});   // 🔴 gagal: validasi/SMTP error
    @endif
    @if(session('success'))
        ok && ok.play().catch(()=>{});   // 🟢 berhasil
    @endif
});
</script>
@endsection