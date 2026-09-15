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
    :root{--primary:#b45a3c;--primary-dark:#96482e;--dark:#23272c;--bg:#faf7f4;--krem:#f0e7e0;}
    body{background:var(--bg);font-family:'Plus Jakarta Sans',sans-serif;color:var(--dark);}
    .navbar{background:#fff;box-shadow:0 2px 15px rgba(35,39,44,.08);padding:.8rem 0;}
    .navbar-brand{font-weight:800;font-size:1.4rem;color:var(--dark);}
    .navbar-brand .accent{color:var(--primary);}
    .navbar-brand img{width:38px;height:38px;border-radius:10px;object-fit:cover;}
    .nav-link{font-weight:600;color:#5a5f66;font-size:.92rem;}
    .nav-link:hover,.nav-link.active{color:var(--primary);}
    .cart-badge{position:absolute;top:-6px;right:-8px;background:var(--primary);color:#fff;border-radius:50px;font-size:.65rem;font-weight:700;padding:.15em .5em;}
    .notif-bell{position:relative;width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:var(--krem);color:var(--primary-dark);transition:.2s;}
    .notif-bell:hover{background:#e9dccd;}
    .notif-count{position:absolute;top:-4px;right:-4px;background:#dc3545;color:#fff;border-radius:50px;font-size:.65rem;font-weight:700;padding:.15em .5em;}
    .card{border:none;border-radius:16px;box-shadow:0 2px 10px rgba(35,39,44,.06);}
    .btn-primary{background:var(--primary);border:none;border-radius:10px;font-weight:600;}
    .btn-primary:hover,.btn-primary:focus{background:var(--primary-dark);}
    .btn-outline-primary{color:var(--primary);border-color:var(--primary);}
    .btn-outline-primary:hover{background:var(--primary);border-color:var(--primary);}
    .badge-soft{background:var(--krem);color:var(--primary-dark);font-weight:600;border-radius:8px;padding:.45em .7em;}
    .harga{font-weight:800;color:var(--primary-dark);}
    .form-label{font-weight:600;font-size:.9rem;}
    .form-control,.form-select{border-radius:10px;}
    .alert{border-radius:12px;border:none;}
    .alert-success{background:#e8f5e9;color:#1b5e20;}
    .alert-danger{background:#ffebee;color:#b71c1c;}
    .dropdown-menu{border:none;border-radius:14px;box-shadow:0 10px 30px rgba(35,39,44,.12);}
    .dropdown-item{font-weight:500;font-size:.9rem;border-radius:8px;}
    .dropdown-item:hover{background:var(--krem);color:var(--primary-dark);}
    footer{background:var(--dark);color:#aab2bd;margin-top:4rem;padding:3rem 0 1.5rem;}
    footer h6{color:#fff;font-weight:700;}
    footer a{color:#aab2bd;text-decoration:none;font-size:.9rem;}
    footer a:hover{color:#e0906f;}
    .footer-brand{font-weight:800;font-size:1.3rem;color:#fff;}
    .footer-brand .accent{color:#e0906f;}
    .sosmed{width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.08);color:#fff;font-size:1rem;transition:.2s;text-decoration:none;}
    .sosmed:hover{background:var(--primary);color:#fff;transform:translateY(-3px);}
    .pay-badge{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:8px;padding:.3em .9em;font-size:.72rem;font-weight:700;color:#cfd6dd;}

    /* ===== FLASH TOAST (NOTIF + SOUND GLOBAL) ===== */
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

{{-- ===== FLASH TOAST - otomatis muncul dari session/errors ===== --}}
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

@php
    $cartCount = \App\Models\CartItem::where('id_user', auth()->id())->sum('jumlah');
    $wishCount = \App\Models\Wishlist::where('id_user', auth()->id())->count();
    $unread = 0;
    try {
        $unread = \App\Models\Notifikasi::where('id_user', auth()->id())->where('is_read', 0)->count();
    } catch (\Exception $e) { $unread = 0; }
@endphp

{{-- ===== NAVBAR ===== --}}
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('produk.index') }}">
            <img src="{{ asset('images/logo.jpeg') }}" onerror="this.style.display='none'" alt="Graniva">
            <span>Grani<span class="accent">va</span></span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }}" href="{{ route('produk.index') }}">
                        <i class="bi bi-shop me-1"></i>Katalog
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pesanan.*') ? 'active' : '' }}" href="{{ route('pesanan.index') }}">
                        <i class="bi bi-bag me-1"></i>Pesanan
                    </a>
                </li>
                <li class="nav-item position-relative">
                    <a class="nav-link {{ request()->routeIs('cart.*') ? 'active' : '' }}" href="{{ route('cart.index') }}">
                        <i class="bi bi-cart3 me-1"></i>Keranjang
                        @if($cartCount > 0)<span class="cart-badge">{{ $cartCount }}</span>@endif
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav align-items-lg-center gap-2">
                {{-- Wishlist --}}
                <li class="nav-item">
                    <a href="{{ route('wishlist.index') }}" class="notif-bell" title="Wishlist">
                        <i class="bi bi-heart"></i>
                        @if($wishCount > 0)<span class="notif-count">{{ $wishCount }}</span>@endif
                    </a>
                </li>

                {{-- Notifikasi --}}
                <li class="nav-item">
                    <a href="{{ route('notifikasi.index') }}" class="notif-bell" title="Notifikasi">
                        <i class="bi bi-bell"></i>
                        @if($unread > 0)<span class="notif-count">{{ $unread }}</span>@endif
                    </a>
                </li>

                {{-- Dropdown User --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle"
                              style="width:34px;height:34px;background:var(--primary);color:#fff;font-weight:700">
                            {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                        </span>
                        <span class="d-none d-lg-inline">{{ auth()->user()->nama }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profil.edit') }}"><i class="bi bi-person me-2"></i>Profil Saya</a></li>
                        <li><a class="dropdown-item" href="{{ route('statistik.index') }}"><i class="bi bi-graph-up me-2"></i>Statistik Belanja</a></li>
                        <li><a class="dropdown-item" href="{{ route('wishlist.index') }}"><i class="bi bi-heart me-2"></i>Wishlist</a></li>
                        <li><a class="dropdown-item" href="{{ route('retur.index') }}"><i class="bi bi-arrow-return-left me-2"></i>Retur Saya</a></li>
                        <li><a class="dropdown-item" href="{{ route('topup.index') }}"><i class="bi bi-plus-circle me-2"></i>Top Up GraPay</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
    @yield('content')
</main>

{{-- ===== FOOTER ===== --}}
<footer>
    <div class="container">
        <div class="row g-4">
            {{-- Brand + Sosmed (tanpa Facebook) --}}
            <div class="col-lg-4">
                <div class="footer-brand mb-2">Grani<span class="accent">va</span></div>
                <p class="small mb-3">Hadirkan keindahan di setiap sudut rumah. Keramik & granit premium dengan kualitas terbaik, harga bersahabat.</p>
                <div class="d-flex gap-2">
                    <a href="https://www.instagram.com/graniva_?stkn=MTd0NW9ubGc4dHNqaA%3D%3D" class="sosmed" title="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="https://www.tiktok.com/@graniva_?_r=1&_t=ZS-99hLAZxDDvc" class="sosmed" title="TikTok"><i class="bi bi-tiktok"></i></a>
                    <a href="https://youtube.com/@gemoy_store?si=yGBC3X1KTygdLACn" class="sosmed" title="YouTube"><i class="bi bi-youtube"></i></a>
                    <a href="https://wa.me/628977298698" class="sosmed" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>

            {{-- Menu --}}
            <div class="col-6 col-lg-2">
                <h6 class="mb-3">Menu</h6>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('produk.index') }}">Katalog</a>
                    <a href="{{ route('pesanan.index') }}">Pesanan</a>
                    <a href="{{ route('cart.index') }}">Keranjang</a>
                    <a href="{{ route('topup.index') }}">Top Up GraPay</a>
                </div>
            </div>

            {{-- Bantuan --}}
            <div class="col-6 col-lg-2">
                <h6 class="mb-3">Bantuan</h6>
                <div class="d-flex flex-column gap-2">
                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalCara">Cara Pemesanan</a>
                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalRetur">Kebijakan Retur</a>
                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalSyarat">Syarat & Ketentuan</a>
                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalPrivasi">Kebijakan Privasi</a>
                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalFaq">FAQ</a>
                </div>
            </div>

            {{-- Kontak + Jam --}}
            <div class="col-lg-4">
                <h6 class="mb-3">Hubungi Kami</h6>
                <div class="d-flex flex-column gap-2 small">
                    <span><i class="bi bi-geo-alt me-2"></i>Jl. Pembangunan 3</span>
                    <span><i class="bi bi-envelope me-2"></i>admingraniva@gmail.com</span>
                    <span><i class="bi bi-whatsapp me-2"></i>08977298698</span>
                    <span><i class="bi bi-clock me-2"></i>Senin–Sabtu: 08.00–17.00 · Minggu: Tutup</span>
                </div>
            </div>
        </div>

        {{-- ===== METODE PEMBAYARAN (CUMA QRIS & GRAPAY) ===== --}}
        <div class="row g-3 mt-2 pt-3" style="border-top:1px solid rgba(255,255,255,.1)">
            <div class="col-12">
                <div class="small mb-2" style="color:#7d8590">Metode Pembayaran</div>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="pay-badge">QRIS</span>
                    <span class="pay-badge">GraPay</span>
                </div>
            </div>
        </div>

        <hr style="border-color:rgba(255,255,255,.1)">
        <p class="small text-center mb-0">© {{ date('Y') }} Graniva. Semua hak dilindungi.</p>
    </div>
</footer>

{{-- ===== MODAL BANTUAN ===== --}}
<div class="modal fade" id="modalCara" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border:none;border-radius:20px">
            <div class="modal-header" style="background:var(--primary);color:#fff">
                <h6 class="modal-title fw-bold"><i class="bi bi-bag-check me-2"></i>Cara Pemesanan</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 small">
                <ol class="mb-0">
                    <li class="mb-2">Pilih produk di <strong>Katalog</strong>, lalu klik <strong>Pesan</strong> atau tambah ke <strong>Keranjang</strong>.</li>
                    <li class="mb-2">Isi <strong>jumlah</strong> dan <strong>alamat pengiriman</strong> dengan lengkap.</li>
                    <li class="mb-2">Pilih metode pembayaran: <strong>QRIS atau GraPay</strong>.</li>
                    <li class="mb-2">Upload <strong>bukti pembayaran</strong> (tidak perlu jika pakai GraPay).</li>
                    <li class="mb-2">Tunggu verifikasi admin — pesanan langsung diproses.</li>
                    <li>Pesanan diantar langsung oleh <strong>tim Graniva</strong> ke alamat Anda.</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRetur" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border:none;border-radius:20px">
            <div class="modal-header" style="background:var(--primary);color:#fff">
                <h6 class="modal-title fw-bold"><i class="bi bi-arrow-return-left me-2"></i>Kebijakan Retur</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 small">
                <p class="fw-bold mb-1">Syarat Retur</p>
                <ul class="mb-3">
                    <li class="mb-2">Retur diajukan maksimal <strong>3 hari</strong> setelah pesanan berstatus <strong>sampai</strong>.</li>
                    <li class="mb-2">Produk belum dipasang, tidak rusak karena kelalaian pembeli, dan kemasan masih lengkap.</li>
                    <li class="mb-2">Wajib melampirkan <strong>foto bukti</strong> kondisi produk saat diterima.</li>
                </ul>
                <p class="fw-bold mb-1">Ketentuan Biaya</p>
                <ul class="mb-3">
                    <li class="mb-2">Kesalahan dari pihak Graniva (produk cacat, salah kirim, pecah saat pengiriman) → ongkir retur <strong>ditanggung Graniva</strong>.</li>
                    <li class="mb-2">Kesalahan dari pembeli (salah pilih ukuran/warna) → ongkir retur ditanggung pembeli.</li>
                </ul>
                <p class="fw-bold mb-1">Proses Pengembalian Dana</p>
                <ul class="mb-0">
                    <li class="mb-2">Setelah retur disetujui admin, dana dikembalikan dalam bentuk <strong>saldo GraPay</strong>.</li>
                    <li class="mb-0">Cara ajukan: menu <strong>Pesanan</strong> → pilih pesanan berstatus sampai → klik <strong>Retur</strong>.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSyarat" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border:none;border-radius:20px">
            <div class="modal-header" style="background:var(--primary);color:#fff">
                <h6 class="modal-title fw-bold"><i class="bi bi-file-text me-2"></i>Syarat & Ketentuan</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 small">
                <ul class="mb-0">
                    <li class="mb-2">Akun Graniva wajib menggunakan data yang valid.</li>
                    <li class="mb-2">Pesanan yang sudah dibayar dapat dibatalkan selama status belum <strong>dikirim</strong>, dengan mengisi alasan pembatalan.</li>
                    <li class="mb-2">Dana pembatalan dikembalikan ke <strong>GraPay</strong>, bukan tunai.</li>
                    <li class="mb-2">Top Up & Tarik saldo GraPay diverifikasi oleh admin.</li>
                    <li class="mb-2">Harga dan stok dapat berubah sewaktu-waktu tanpa pemberitahuan.</li>
                    <li>Retur hanya dapat diajukan setelah pesanan berstatus <strong>sampai</strong>.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPrivasi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border:none;border-radius:20px">
            <div class="modal-header" style="background:var(--primary);color:#fff">
                <h6 class="modal-title fw-bold"><i class="bi bi-shield-lock me-2"></i>Kebijakan Privasi</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 small">
                <ul class="mb-0">
                    <li class="mb-2">Data pribadi (nama, email, alamat, no. HP) hanya digunakan untuk keperluan transaksi dan pengiriman.</li>
                    <li class="mb-2">Graniva tidak membagikan data pengguna kepada pihak ketiga.</li>
                    <li class="mb-2">Bukti pembayaran hanya digunakan untuk verifikasi transaksi.</li>
                    <li class="mb-2">Saldo GraPay tersimpan aman di akun masing-masing pengguna.</li>
                    <li>Pengguna dapat meminta penghapusan akun dengan menghubungi customer service.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFaq" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border:none;border-radius:20px">
            <div class="modal-header" style="background:var(--primary);color:#fff">
                <h6 class="modal-title fw-bold"><i class="bi bi-question-circle me-2"></i>FAQ</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 small">
                <p class="fw-bold mb-1">Apa itu GraPay?</p>
                <p class="text-muted mb-3">GraPay (Graniva Pay) adalah saldo akun untuk belanja, menerima refund, top up, dan tarik saldo.</p>
                <p class="fw-bold mb-1">Berapa lama pesanan sampai?</p>
                <p class="text-muted mb-3">Pesanan diantar tim Graniva biasanya 3–7 hari setelah pembayaran terverifikasi.</p>
                <p class="fw-bold mb-1">Bagaimana cara refund?</p>
                <p class="text-muted mb-3">Batalkan pesanan (isi alasan) selama status belum dikirim — dana otomatis masuk GraPay.</p>
                <p class="fw-bold mb-1">Apakah bisa retur barang?</p>
                <p class="text-muted mb-0">Bisa, ajukan retur setelah pesanan berstatus sampai melalui menu Pesanan → Retur.</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ===== AUTO HIDE FLASH TOAST 5 DETIK =====
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