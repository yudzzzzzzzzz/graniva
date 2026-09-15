<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Admin Graniva')</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    :root{--primary:#b45a3c;--primary-dark:#96482e;--dark:#23272c;--bg:#f4f1ed;--sidebar:#21252b;}
    body{background:var(--bg);font-family:'Plus Jakarta Sans',sans-serif;color:var(--dark);}
    .sidebar{width:250px;min-height:100vh;background:var(--sidebar);position:fixed;top:0;left:0;z-index:100;transition:.3s;}

    /* ===== BRAND: Grani HITAM + va COKLAT, background gelap nyatu sidebar ===== */
    .sidebar .brand{padding:1.2rem 1.4rem;display:flex;align-items:center;gap:.7rem;border-bottom:1px solid rgba(255,255,255,.08);}
    .sidebar .brand img{width:38px;height:38px;border-radius:10px;object-fit:cover;}
    .sidebar .brand span{color:#141414;font-weight:800;font-size:1.15rem;}
    .sidebar .brand .accent{color:#4e2a1a;}

    .sidebar .menu{padding:1rem .8rem;}
    .sidebar .menu-label{color:#6c757d;font-size:.7rem;text-transform:uppercase;letter-spacing:1px;padding:.6rem .8rem .3rem;}
    .sidebar a.link{display:flex;align-items:center;gap:.7rem;color:#aab2bd;padding:.65rem .8rem;border-radius:10px;text-decoration:none;font-weight:500;margin-bottom:.2rem;transition:.2s;}
    .sidebar a.link:hover{background:rgba(255,255,255,.06);color:#fff;}
    .sidebar a.link.active{background:var(--primary);color:#fff;}
    .main-content{margin-left:250px;padding:1.5rem;transition:.3s;}
    .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;}
    .card{border:none;border-radius:16px;box-shadow:0 2px 10px rgba(35,39,44,.06);}
    .btn-primary{background:var(--primary);border:none;border-radius:10px;font-weight:600;}
    .btn-primary:hover,.btn-primary:focus{background:var(--primary-dark);}
    .badge-soft{background:#f0e7e0;color:var(--primary-dark);font-weight:600;border-radius:8px;padding:.45em .7em;}
    .harga{font-weight:800;color:var(--primary-dark);}
    .form-label{font-weight:600;font-size:.9rem;}
    .form-control,.form-select{border-radius:10px;}
    .table th{font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#8a8f96;}
    .stat-card{border-radius:16px;padding:1.2rem;color:#fff;}
    .alert{border-radius:12px;border:none;}
    .alert-success{background:#e8f5e9;color:#1b5e20;}
    .alert-danger{background:#ffebee;color:#b71c1c;}
    #sidebarToggle{display:none;}
    @media (max-width: 992px){
        .sidebar{transform:translateX(-100%);}
        .sidebar.show{transform:translateX(0);}
        .main-content{margin-left:0;}
        #sidebarToggle{display:inline-block;}
    }

    /* ===== FLASH TOAST (BARU) ===== */
    .flash-stack{position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;max-width:360px;pointer-events:none}
    .flash{pointer-events:auto;display:flex;gap:10px;align-items:flex-start;border-radius:14px;padding:14px 18px;font-size:.88rem;font-weight:600;box-shadow:0 12px 30px rgba(0,0,0,.22);animation:flashIn .4s ease;color:#fff;cursor:pointer}
    .flash i{font-size:1.1rem;margin-top:2px;flex-shrink:0}
    .flash span{line-height:1.4}
    .flash-success{background:linear-gradient(135deg,#2e9e5b,#20784a)}
    .flash-danger{background:linear-gradient(135deg,#d64545,#a92e2e)}
    .flash.hide{animation:flashOut .4s ease forwards}
    @keyframes flashIn{from{opacity:0;transform:translateX(50px)}to{opacity:1;transform:none}}
    @keyframes flashOut{to{opacity:0;transform:translateX(50px)}}
    @media(max-width:576px){
        .flash-stack{top:auto;bottom:20px;right:10px;left:10px;max-width:none}
    }
</style>
@stack('styles')
</head>
<body>
<aside class="sidebar" id="sidebar">
    <div class="brand">
        <img src="{{ asset('images/logo.jpeg') }}" onerror="this.style.display='none'">
        <span>Grani<span class="accent">va</span></span>
    </div>
    <nav class="menu">
        <div class="menu-label">Menu Utama</div>
        <a class="link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a>
        <a class="link {{ request()->routeIs('admin.produk.*') ? 'active' : '' }}" href="{{ route('admin.produk.index') }}"><i class="bi bi-box-seam"></i>Produk</a>
        <a class="link {{ request()->routeIs('admin.kategori.*') ? 'active' : '' }}" href="{{ route('admin.kategori.index') }}"><i class="bi bi-tags"></i>Kategori</a>
        <a class="link {{ request()->routeIs('admin.pesanan.*') ? 'active' : '' }}" href="{{ route('admin.pesanan.index') }}"><i class="bi bi-bag-check"></i>Pesanan</a>
        <a class="link {{ request()->routeIs('admin.topup.*') ? 'active' : '' }}" href="{{ route('admin.topup.index') }}"><i class="bi bi-plus-circle"></i>Top Up</a>
        <a class="link {{ request()->routeIs('admin.withdraw.*') ? 'active' : '' }}" href="{{ route('admin.withdraw.index') }}"><i class="bi bi-cash"></i>Penarikan</a>
        <a class="link {{ request()->routeIs('admin.ulasan.*') ? 'active' : '' }}" href="{{ route('admin.ulasan.index') }}"><i class="bi bi-star-half"></i>Kelola Rating</a>
        <div class="menu-label">Lainnya</div>
        <a class="link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i>Pengguna</a>
        <a class="link {{ request()->routeIs('admin.retur.*') ? 'active' : '' }}" href="{{ route('admin.retur.index') }}"><i class="bi bi-arrow-return-left"></i>Retur</a>
        <a class="link {{ request()->routeIs('admin.notifikasi.*') ? 'active' : '' }}" href="{{ route('admin.notifikasi.index') }}"><i class="bi bi-bell"></i>Notifikasi</a>
    </nav>
</aside>

{{-- ===== FLASH TOAST (BARU) - otomatis muncul dari session/errors ===== --}}
<div class="flash-stack" id="flashStack">
    @if(session('success'))
    <div class="flash flash-success" onclick="this.classList.add('hide')"><i class="bi bi-check-circle-fill"></i><span>{{ session('success') }}</span></div>
    @endif
    @if(session('error'))
    <div class="flash flash-danger" onclick="this.classList.add('hide')"><i class="bi bi-x-circle-fill"></i><span>{{ session('error') }}</span></div>
    @endif
    @if($errors->any())
    <div class="flash flash-danger" onclick="this.classList.add('hide')"><i class="bi bi-x-octagon-fill"></i>
        <span>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</span>
    </div>
    @endif
</div>

<main class="main-content">
    <div class="topbar">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-secondary" id="sidebarToggle"><i class="bi bi-list"></i></button>
            <h5 class="fw-bold mb-0">@yield('page-title', 'Dashboard')</h5>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="small text-muted">Halo, <strong>{{ auth('admin')->user()->nama_admin ?? 'Admin' }}</strong></span>
            <a href="{{ route('admin.logout') }}" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-box-arrow-right me-1"></i>Keluar
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('sidebarToggle').addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('show');
});

// ===== AUTO HIDE FLASH TOAST (BARU) =====
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.flash').forEach(function(f){
        setTimeout(function(){
            f.classList.add('hide');
            setTimeout(function(){ f.remove(); }, 400);
        }, 5000);
    });
});
</script>
@stack('scripts')

{{-- ===== SOUND EFFECTS ===== --}}
<audio id="sound-success" src="{{ asset('audio/berhasil.mp3') }}" preload="auto"></audio>
<audio id="sound-error" src="{{ asset('audio/gagal.mp3') }}" preload="auto"></audio>

<script>
    @if(session('success'))
        window.addEventListener('DOMContentLoaded', () => {
            const audio = document.getElementById('sound-success');
            if(audio) audio.play().catch(e => console.log('Audio play prevented'));
        });
    @endif
    @if(session('error') || $errors->any())
        window.addEventListener('DOMContentLoaded', () => {
            const audio = document.getElementById('sound-error');
            if(audio) audio.play().catch(e => console.log('Audio play prevented'));
        });
    @endif
</script>
</body>
</html>