<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Graniva')</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root{--primary:#b45a3c;--primary-dark:#96482e;--dark:#23272c;}
    *{box-sizing:border-box;}
    body{
        margin:0;
        min-height:100vh;
        font-family:'Plus Jakarta Sans',sans-serif;
        overflow-x:hidden;
    }

    /* ===== VIDEO BACKGROUND FULL SCREEN ===== */
    .bg-video{
        position:fixed;
        top:0;left:0;
        width:100vw;height:100vh;
        object-fit:cover;
        z-index:-2;
    }
    .video-overlay{
        position:fixed;
        top:0;left:0;
        width:100vw;height:100vh;
        background:linear-gradient(135deg, rgba(35,39,44,.75) 0%, rgba(150,72,46,.45) 100%);
        z-index:-1;
    }

    /* ===== LAYOUT ===== */
    .auth-wrapper{
        min-height:100vh;
        display:flex;
        align-items:center;
        padding:30px 0;
    }

    /* ===== TAGLINE GRANIVA ===== */
    .hero-text{
        color:#fff;
        padding-right:40px;
    }
    .hero-text .brand-logo{
        width:80px;height:80px;
        object-fit:cover;
        border-radius:20px;
        box-shadow:0 8px 30px rgba(0,0,0,.3);
        margin-bottom:1.5rem;
    }
    .hero-text h1{
        font-size:3.2rem;
        font-weight:800;
        margin-bottom:.8rem;
        line-height:1.1;
    }
    .hero-text h1 .accent{color:#ffb088;}
    .hero-text p.tagline{
        font-size:1.15rem;
        opacity:.9;
        font-weight:300;
        line-height:1.6;
        margin-bottom:1.5rem;
    }
    .hero-text .features{
        display:flex;
        gap:1.5rem;
        flex-wrap:wrap;
    }
    .hero-text .feature-item{
        display:flex;
        align-items:center;
        gap:.5rem;
        font-size:.9rem;
        opacity:.85;
    }
    .hero-text .feature-item i{
        color:#ffb088;
        font-size:1.1rem;
    }

    /* ===== FORM CARD ===== */
    .auth-container{
        width:100%;
        max-width:430px;
        background:#fff;
        border-radius:24px;
        padding:2.5rem 2.2rem;
        box-shadow:0 25px 70px rgba(0,0,0,.35);
        animation:slideUp .5s ease;
        margin:0 auto;
    }
    @keyframes slideUp{
        from{opacity:0;transform:translateY(25px);}
        to{opacity:1;transform:translateY(0);}
    }
    .auth-logo{
        display:block;
        width:70px;height:70px;
        object-fit:cover;
        border-radius:18px;
        margin:0 auto 1.2rem;
        box-shadow:0 4px 15px rgba(180,90,60,.25);
    }
    .auth-title{
        font-weight:800;
        color:var(--dark);
        margin-bottom:.3rem;
        text-align:center;
    }
    .auth-sub{
        color:#8a8f96;
        font-size:.9rem;
        text-align:center;
    }
    .title-accent{
        width:45px;height:4px;
        background:var(--primary);
        border-radius:2px;
        margin:.8rem auto 1.8rem;
    }
    .form-label{
        font-weight:600;
        font-size:.85rem;
        color:#495057;
        margin-bottom:.4rem;
    }
    .field{position:relative;display:flex;align-items:center;}
    .field .bi-lead{
        position:absolute;left:14px;
        color:var(--primary);
        font-size:1rem;z-index:2;
    }
    .field .form-control{
        padding-left:42px;padding-right:42px;
        border-radius:12px;
        border:1.5px solid #e5ded5;
        background:#faf8f5;
        height:48px;
        transition:.2s;
        width:100%;
    }
    .field .form-control:focus{
        border-color:var(--primary);
        background:#fff;
        box-shadow:0 0 0 3px rgba(180,90,60,.12);
        outline:none;
    }
    .toggle-pass{
        position:absolute;right:12px;
        background:none;border:none;
        color:#8a8f96;cursor:pointer;
        padding:4px;z-index:2;
    }
    .toggle-pass:hover{color:var(--primary);}
    .btn-auth{
        width:100%;
        background:var(--primary);
        color:#fff;border:none;
        border-radius:12px;
        padding:.85rem;
        font-weight:700;font-size:.95rem;
        transition:.2s;
        margin-top:.5rem;
    }
    .btn-auth:hover{
        background:var(--primary-dark);
        transform:translateY(-1px);
        box-shadow:0 6px 20px rgba(180,90,60,.3);
    }
    .btn-auth:disabled{
        opacity:.7;
        cursor:not-allowed;
        transform:none;
    }
    .link-auth{color:var(--primary);font-weight:700;text-decoration:none;}
    .link-auth:hover{color:var(--primary-dark);text-decoration:underline;}
    .alert{border-radius:12px;border:none;font-size:.85rem;}
    .alert-success{background:#e8f5e9;color:#1b5e20;}
    .alert-danger{background:#ffebee;color:#b71c1c;}

    /* ===== FLASH TOAST (BARU) ===== */
    .flash-stack{position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;max-width:360px;pointer-events:none}
    .flash{pointer-events:auto;display:flex;gap:10px;align-items:flex-start;border-radius:14px;padding:14px 18px;font-size:.88rem;font-weight:600;box-shadow:0 12px 30px rgba(0,0,0,.35);animation:flashIn .4s ease;color:#fff;cursor:pointer;backdrop-filter:blur(10px)}
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

{{-- ===== VIDEO BACKGROUND (full screen, autoplay, loop, no pause) ===== --}}
<video class="bg-video" autoplay muted loop playsinline>
    <source src="{{ asset('videos/1.mp4') }}" type="video/mp4">
</video>
<div class="video-overlay"></div>

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

{{-- ===== KONTEN ===== --}}
<div class="container auth-wrapper">
    <div class="row align-items-center justify-content-center w-100">

        {{-- Tagline Graniva (layar besar) --}}
        <div class="col-lg-6 d-none d-lg-block">
            <div class="hero-text">
                <img src="{{ asset('images/logo.jpeg') }}" class="brand-logo" onerror="this.style.display='none'">
                <h1>Grani<span class="accent">va</span></h1>
                <p class="tagline">
                    "Hadirkan keindahan di setiap sudut rumah.
                    Keramik & granit premium dengan kualitas terbaik,
                    harga bersahabat."
                </p>
                <div class="features">
                    <div class="feature-item"><i class="bi bi-patch-check-fill"></i>Kualitas Terjamin</div>
                    <div class="feature-item"><i class="bi bi-truck"></i>Pengiriman Cepat</div>
                    <div class="feature-item"><i class="bi bi-shield-check"></i>Aman & Terpercaya</div>
                </div>
            </div>
        </div>

        {{-- Form Login / Register --}}
        <div class="col-lg-5 col-md-8">
            <div class="auth-container">
                @yield('content')
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ===== Kalau halaman dibuka dari cache (back button), reload biar token CSRF baru =====
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });

    // ===== Show / Hide password =====
    document.querySelectorAll('.toggle-pass').forEach(btn => {
        btn.addEventListener('click', function() {
            const target = document.getElementById(this.dataset.target);
            if (!target) return;
            const icon = this.querySelector('i');
            if (target.type === 'password') {
                target.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                target.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    });

    // ===== PLAY SOUND =====
    function playSound(type) {
        const audio = document.getElementById('sound-' + type);
        if (audio) {
            audio.currentTime = 0;
            audio.play().catch(e => console.log('Audio blocked:', e));
        }
    }

    // ===== TAMPILKAN ALERT =====
    function showAlert(message, type) {
        let existing = document.querySelector('.ajax-alert');
        if (existing) existing.remove();

        const alert = document.createElement('div');
        alert.className = 'ajax-alert alert alert-' + (type === 'error' ? 'danger' : 'success') + ' alert-dismissible fade show';
        alert.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';

        const container = document.querySelector('.auth-container');
        if (container) container.prepend(alert);
    }

    // ===== AUTO PLAY SOUND + HIDE FLASH TOAST (BARU) =====
    document.addEventListener('DOMContentLoaded', function(){
        const hasSuccess = @json(session('success') ? true : false);
        const hasError   = @json((session('error') || $errors->any()) ? true : false);

        if (hasSuccess) playSound('success');
        if (hasError)   playSound('error');

        document.querySelectorAll('.flash').forEach(function(f){
            setTimeout(function(){
                f.classList.add('hide');
                setTimeout(function(){ f.remove(); }, 400);
            }, 5000);
        });
    });

    // ===== AJAX FORM HANDLER (login & register) =====
    document.querySelectorAll('form[data-ajax]').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const btn = this.querySelector('button[type="submit"]');
            const originalHTML = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                // ===== Kalau 419 (token basi) → reload otomatis biar token baru =====
                if (response.status === 419) {
                    showAlert('Sesi berakhir, memuat ulang halaman...', 'error');
                    setTimeout(() => window.location.reload(), 800);
                    return;
                }

                const data = await response.json();

                if (response.ok && data.success) {
                    // ===== BERHASIL → berhasil.mp3 =====
                    playSound('success');
                    showAlert(data.message || 'Berhasil!', 'success');

                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 900);

                } else {
                    // ===== GAGAL → gagal.mp3 =====
                    playSound('error');
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;

                    if (data.blocked && data.redirect) {
                        window.location.href = data.redirect;
                        return;
                    }

                    let errorMsg = data.message || 'Terjadi kesalahan';
                    if (data.errors) {
                        errorMsg = Object.values(data.errors)[0][0];
                    }
                    showAlert(errorMsg, 'error');
                }

            } catch (error) {
                playSound('error');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
                showAlert('Terjadi kesalahan. Coba lagi.', 'error');
            }
        });
    });
</script>
@stack('scripts')

{{-- ===== AUDIO ===== --}}
<audio id="sound-success" src="{{ asset('audio/berhasil.mp3') }}" preload="auto"></audio>
<audio id="sound-error" src="{{ asset('audio/gagal.mp3') }}" preload="auto"></audio>
</body>
</html>