@extends('layouts.auth')
@section('title', 'Akun Diblokir - Graniva')

@section('content')
<style>
    .blocked-card{
        max-width:540px;margin:3.5rem auto;background:#fff;border-radius:24px;
        padding:2.6rem 2.4rem;text-align:center;position:relative;overflow:hidden;
        box-shadow:0 20px 60px rgba(35,39,44,.25);border-top:6px solid #d64545;
    }
    .blocked-icon{
        width:88px;height:88px;border-radius:50%;margin:0 auto 1.4rem;
        background:linear-gradient(135deg,#ffebee,#ffd5d5);
        display:flex;align-items:center;justify-content:center;
        font-size:2.5rem;color:#b71c1c;
    }
    .blocked-title{font-weight:800;font-size:1.65rem;color:#23272c;margin-bottom:.4rem;}
    .blocked-sub{color:#8a8f96;font-size:.9rem;line-height:1.65;margin-bottom:1.6rem;}
    .blocked-user{
        background:#faf7f2;border:1px dashed #e5ded5;border-radius:14px;
        padding:.95rem 1.3rem;font-size:.85rem;color:#495057;margin-bottom:1.4rem;text-align:left;
    }
    .blocked-user strong{color:#b45a3c;}
    .blocked-note{
        background:#fff8e1;border:1px solid #f3e2a2;border-radius:14px;
        padding:.95rem 1.2rem;font-size:.8rem;color:#8d6e00;text-align:left;
        margin-bottom:1.8rem;display:flex;gap:10px;align-items:flex-start;
    }
    .btn-leave{
        display:inline-flex;align-items:center;gap:8px;
        background:linear-gradient(135deg,#b45a3c,#96482e);color:#fff;border:none;border-radius:14px;
        font-weight:800;font-size:.92rem;padding:.85rem 2.2rem;text-decoration:none;
        box-shadow:0 10px 24px rgba(180,90,60,.35);transition:.2s;
    }
    .btn-leave:hover{transform:translateY(-2px);box-shadow:0 14px 30px rgba(180,90,60,.45);color:#fff;}
    .btn-cs{
        display:inline-flex;align-items:center;gap:8px;
        background:#fff;color:#20784a;border:1.5px solid #bcd9bc;border-radius:14px;
        font-weight:700;font-size:.92rem;padding:.85rem 1.8rem;text-decoration:none;transition:.2s;
    }
    .btn-cs:hover{background:#f4faf4;color:#20784a;}
</style>

<div class="blocked-card">
    <div class="blocked-icon"><i class="bi bi-shield-fill-x"></i></div>
    <div class="blocked-title">Akun Diblokir</div>
    <p class="blocked-sub">
        @if($user ?? null)
            Halo, <strong>{{ $user->nama }}</strong>.<br>
        @endif
        Akun Anda saat ini <strong style="color:#b71c1c">diblokir oleh admin Graniva</strong>,
        sehingga tidak dapat digunakan untuk masuk maupun bertransaksi.
    </p>

    @if($user ?? null)
    <div class="blocked-user">
        <div class="mb-1"><i class="bi bi-person-circle me-2"></i><strong>{{ $user->nama }}</strong></div>
        <div class="mb-1"><i class="bi bi-envelope me-2"></i>{{ $user->email }}</div>
        <div><i class="bi bi-hash me-2"></i>ID User: #{{ $user->id_user }}</div>
    </div>
    @endif

    <div class="blocked-note">
        <i class="bi bi-info-circle-fill" style="flex-shrink:0;margin-top:2px"></i>
        <span>Jika Anda merasa tidak melakukan pelanggaran, silakan hubungi Customer Service Graniva
        untuk mengajukan pembukaan blokir akun.</span>
    </div>

    <div class="d-flex justify-content-center gap-2 flex-wrap">
        {{-- ✅ Tombol keluar → notif hijau + berhasil.mp3 di halaman login --}}
        <a href="{{ route('blocked.leave') }}" class="btn-leave">
            <i class="bi bi-box-arrow-right"></i>Keluar
        </a>
        <a href="https://wa.me/628977298698" target="_blank" class="btn-cs">
            <i class="bi bi-whatsapp"></i>Hubungi CS
        </a>
    </div>
</div>
@endsection