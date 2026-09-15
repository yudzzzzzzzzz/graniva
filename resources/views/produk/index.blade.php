@extends('layouts.app')
@section('title', 'Katalog Keramik - Graniva')

@push('styles')
<style>
    .hero-banner{background:linear-gradient(135deg,#b45a3c 0%,#8a4530 55%,#6e3423 100%);border-radius:24px;padding:2.6rem 2.4rem;position:relative;overflow:hidden;box-shadow:0 15px 40px rgba(180,90,60,.3);margin-bottom:1.2rem;}
    .hero-banner::before{content:'';position:absolute;top:-70px;right:-70px;width:240px;height:240px;border-radius:50%;background:rgba(255,255,255,.08);}
    .hero-title{font-size:2.8rem;font-weight:800;line-height:1.1;margin-bottom:.4rem;}
    .hero-title .grani{color:#141414;}
    .hero-title .va{color:#4e2a1a;}
    .hero-tag{color:rgba(255,255,255,.9);font-size:1rem;font-weight:300;max-width:520px;}

    /* ===== RUNNING TEXT (DIKUNCI: KIRI → KANAN) ===== */
    .marquee-wrap{
        overflow:hidden;
        background:linear-gradient(135deg, #b45a3c 0%, #96482e 50%, #b45a3c 100%);
        border-radius:18px;
        padding:16px 0;
        box-shadow:0 8px 24px rgba(180,90,60,.25);
        position:relative;
        margin-bottom:1.6rem;
    }
    .marquee-wrap::before,
    .marquee-wrap::after{
        content:"";position:absolute;top:0;bottom:0;width:80px;z-index:2;pointer-events:none;
    }
    .marquee-wrap::before{ left:0;  background:linear-gradient(90deg,#b45a3c,transparent); }
    .marquee-wrap::after { right:0; background:linear-gradient(-90deg,#b45a3c,transparent); }

    /* Track: 2 grup identik, lebar secukupnya konten */
    .marquee-track{
        display:flex;
        width:max-content;
        animation: granivaMarqueeLTR 30s linear infinite;
        will-change: transform;
    }
    /* Tiap grup = 4 item + padding kanan = jarak antar grup */
    .marquee-group{
        display:flex;
        align-items:center;
        gap:3.5rem;
        padding-right:3.5rem;
    }
    .marquee-item{
        color:#fff;font-weight:700;font-size:1rem;letter-spacing:.8px;
        display:inline-flex;align-items:center;gap:10px;white-space:nowrap;
    }
    .marquee-item .dot{
        width:6px;height:6px;background:#ffd700;border-radius:50%;display:inline-block;
        box-shadow:0 0 10px #ffd700;flex-shrink:0;
    }

    /* ✅ KUNCI ARAH: nama keyframes BARU (biar nggak ketimpa CSS lama)
       -50% → 0 = track meluncur ke KANAN = teks mengalir KIRI → KANAN */
    @keyframes granivaMarqueeLTR{
        from { transform: translateX(-50%); }
        to   { transform: translateX(0); }
    }

    .marquee-wrap:hover .marquee-track{ animation-play-state: paused; }
    /* ===== END MARQUEE ===== */

    /* ===== GRAPAY STRIP (DESAIN PROFESIONAL) ===== */
    .grapay-strip{
        background:linear-gradient(120deg,#ffffff 0%,#fdf9f5 55%,#faf3ec 100%);
        border:1px solid #f0e7e0;border-radius:20px;padding:1.1rem 1.6rem;margin-bottom:1.6rem;
        display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;
        box-shadow:0 8px 30px rgba(180,90,60,.12);position:relative;overflow:hidden;
    }
    .grapay-strip::before{content:'';position:absolute;left:0;top:0;bottom:0;width:5px;background:linear-gradient(180deg,var(--primary),var(--primary-dark));}
    .gp-left{display:flex;align-items:center;gap:14px;position:relative;z-index:2;flex-wrap:wrap;}
    .gp-logo{
        width:52px;height:52px;border-radius:16px;flex-shrink:0;
        background:linear-gradient(135deg,var(--primary),var(--primary-dark));
        color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.45rem;
        box-shadow:0 8px 18px rgba(180,90,60,.35);
    }
    .gp-label{font-size:.66rem;font-weight:800;letter-spacing:1.8px;text-transform:uppercase;color:#9a928a;}
    .gp-saldo{font-size:1.5rem;font-weight:800;color:var(--dark);line-height:1.2;}
    .gp-chip{
        display:inline-flex;align-items:center;gap:5px;background:#e8f5e9;color:#1b5e20;
        border-radius:50px;padding:.35em .95em;font-size:.66rem;font-weight:800;letter-spacing:.6px;text-transform:uppercase;
    }
    .gp-actions{display:flex;gap:10px;position:relative;z-index:2;flex-wrap:wrap;}
    .gp-btn-topup{
        display:inline-flex;align-items:center;gap:8px;
        background:linear-gradient(135deg,var(--primary),var(--primary-dark));
        color:#fff;border:none;border-radius:14px;font-weight:800;font-size:.88rem;padding:.72rem 1.6rem;
        box-shadow:0 8px 20px rgba(180,90,60,.35);transition:.2s;text-decoration:none;
    }
    .gp-btn-topup:hover{transform:translateY(-2px);box-shadow:0 12px 26px rgba(180,90,60,.45);color:#fff;}
    .gp-btn-tarik{
        display:inline-flex;align-items:center;gap:8px;
        background:#fff;color:var(--primary-dark);border:1.5px solid #e5ded5;border-radius:14px;
        font-weight:700;font-size:.88rem;padding:.72rem 1.4rem;transition:.2s;text-decoration:none;
    }
    .gp-btn-tarik:hover{border-color:var(--primary);color:var(--primary);background:#fdf6f2;}
    .gp-deco{
        position:absolute;right:-16px;bottom:-34px;font-size:7.5rem;color:rgba(180,90,60,.07);
        transform:rotate(-12deg);pointer-events:none;
    }
    /* ===== END GRAPAY STRIP ===== */

    .produk-card{border:none;border-radius:20px;overflow:hidden;transition:.3s;box-shadow:0 2px 10px rgba(35,39,44,.06);background:#fff;}
    .produk-card:hover{transform:translateY(-6px);box-shadow:0 15px 35px rgba(35,39,44,.15);}
    .produk-card .img-wrap{height:200px;overflow:hidden;position:relative;}
    .produk-card .img-wrap img{width:100%;height:100%;object-fit:cover;transition:transform .5s;}
    .produk-card:hover .img-wrap img{transform:scale(1.08);}
    .produk-card .kat-badge{position:absolute;top:12px;left:12px;background:rgba(20,20,20,.85);color:#fff;border-radius:50px;padding:.3em .9em;font-size:.7rem;font-weight:600;z-index:2;}

    .stock-badge{position:absolute;top:12px;right:12px;border-radius:50px;padding:.3em .9em;font-size:.7rem;font-weight:700;z-index:2;}
    .stock-ada{background:#e8f5e9;color:#1b5e20;}
    .stock-sedikit{background:#fff3cd;color:#856404;}
    .stock-habis{background:#ffebee;color:#b71c1c;}
    .habis-overlay{position:absolute;inset:0;background:rgba(255,255,255,.6);backdrop-filter:blur(2px);display:flex;align-items:center;justify-content:center;z-index:1;}
    .habis-overlay span{background:#b71c1c;color:#fff;font-weight:800;padding:.5em 1.4em;border-radius:50px;transform:rotate(-8deg);box-shadow:0 5px 15px rgba(0,0,0,.2);}

    .wish-form{position:absolute;bottom:12px;right:12px;z-index:3;}
    .wish-circle{width:36px;height:36px;border-radius:50%;background:#fff;border:none;display:flex;align-items:center;justify-content:center;color:#b45a3c;font-size:1rem;box-shadow:0 3px 10px rgba(0,0,0,.18);transition:.2s;}
    .wish-circle:hover{transform:scale(1.1);}
    .wish-circle.active{background:#e0245e;color:#fff;}
    .filter-bar{border-radius:16px;}
</style>
@endpush

@section('content')
{{-- HERO --}}
<div class="hero-banner">
    <div class="position-relative" style="z-index:2">
        <h1 class="hero-title"><span class="grani">Grani</span><span class="va">va</span></h1>
        <p class="hero-tag mb-0">Temukan keramik & granit premium untuk setiap sudut rumah Anda.</p>
    </div>
</div>

{{-- ===== RUNNING TEXT (STRUKTUR BARU: 2 GRUP IDENTIK, ARAH KIRI → KANAN) ===== --}}
<div class="marquee-wrap">
    <div class="marquee-track">
        <div class="marquee-group">
            <span class="marquee-item"><span class="dot"></span>SELAMAT DATANG DI GRANIVA</span>
            <span class="marquee-item"><span class="dot"></span>KERAMIK & GRANIT PREMIUM</span>
            <span class="marquee-item"><span class="dot"></span>KUALITAS TERJAMIN · HARGA BERSAHABAT</span>
            <span class="marquee-item"><span class="dot"></span>PENGIRIMAN CEPAT & AMAN</span>
        </div>
        <div class="marquee-group" aria-hidden="true">
            <span class="marquee-item"><span class="dot"></span>SELAMAT DATANG DI GRANIVA</span>
            <span class="marquee-item"><span class="dot"></span>KERAMIK & GRANIT PREMIUM</span>
            <span class="marquee-item"><span class="dot"></span>KUALITAS TERJAMIN · HARGA BERSAHABAT</span>
            <span class="marquee-item"><span class="dot"></span>PENGIRIMAN CEPAT & AMAN</span>
        </div>
    </div>
</div>

{{-- ===== GRAPAY STRIP ===== --}}
@auth
<div class="grapay-strip">
    <div class="gp-left">
        <div class="gp-logo"><i class="bi bi-wallet2"></i></div>
        <div>
            <div class="gp-label">Saldo GraPay · Graniva Pay</div>
            <div class="gp-saldo">Rp {{ number_format(auth()->user()->saldo, 0, ',', '.') }}</div>
        </div>
        <span class="gp-chip"><i class="bi bi-patch-check-fill"></i>Aktif</span>
    </div>
    <div class="gp-actions">
        <a href="{{ route('topup.index') }}" class="gp-btn-topup"><i class="bi bi-plus-circle-fill"></i>Top Up Saldo</a>
        <a href="{{ route('withdraw.index') }}" class="gp-btn-tarik"><i class="bi bi-cash-coin"></i>Tarik Dana</a>
    </div>
    <i class="bi bi-wallet2 gp-deco"></i>
</div>
@endauth

@if(session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i>{{ session('error') }}</div>@endif

{{-- FILTER --}}
<div class="card filter-bar p-3 mb-4">
    <form method="GET" class="row g-2 align-items-center">
        <div class="col-md-4"><input type="text" name="q" class="form-control" placeholder="Cari keramik..." value="{{ $search }}"></div>
        <div class="col-md-3">
            <select name="kategori" class="form-select">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $k)<option value="{{ $k->id_kategori }}" {{ $kategoriFilter == $k->id_kategori ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="terbaru" {{ $sort == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                <option value="harga_asc" {{ $sort == 'harga_asc' ? 'selected' : '' }}>Harga Terendah</option>
                <option value="harga_desc" {{ $sort == 'harga_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
            <button class="btn btn-primary flex-fill"><i class="bi bi-search"></i></button>
            <a href="{{ route('produk.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

{{-- GRID PRODUK --}}
@php $wishIds = auth()->check() ? \App\Models\Wishlist::where('id_user', auth()->id())->pluck('id_produk') : collect(); @endphp
<div class="row g-4">
    @forelse($produks as $p)
    @php $rate = $p->ulasan->avg('rating') ?? 0; @endphp
    <div class="col-6 col-md-4 col-lg-3">
        <div class="produk-card h-100 d-flex flex-column {{ $p->stok <= 0 ? 'opacity-75' : '' }}">
            <div class="img-wrap">
                <a href="{{ route('produk.show', $p->id_produk) }}" class="text-decoration-none d-block h-100">
                    @if($p->gambar)
                        <img src="{{ Storage::url($p->gambar) }}" alt="{{ $p->nama_produk }}">
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100" style="background:#e8e2da;color:#b45a3c;font-size:2.5rem"><i class="bi bi-image"></i></div>
                    @endif
                </a>
                <span class="kat-badge">{{ $p->kategori->nama_kategori ?? $p->jenis ?? 'Umum' }}</span>

                @if($p->stok <= 0)
                    <span class="stock-badge stock-habis">Stok Habis</span>
                    <div class="habis-overlay"><span>STOK HABIS</span></div>
                @elseif($p->stok <= 5)
                    <span class="stock-badge stock-sedikit">Sisa {{ $p->stok }}</span>
                @else
                    <span class="stock-badge stock-ada">Stok {{ $p->stok }}</span>
                @endif

                {{-- Tombol Wishlist --}}
                <form method="POST" action="{{ route('wishlist.toggle') }}" class="wish-form">
                    @csrf
                    <input type="hidden" name="id_produk" value="{{ $p->id_produk }}">
                    <button class="wish-circle {{ $wishIds->contains($p->id_produk) ? 'active' : '' }}" title="Wishlist">
                        <i class="bi {{ $wishIds->contains($p->id_produk) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                    </button>
                </form>
            </div>
            <div class="p-3 d-flex flex-column flex-grow-1">
                <a href="{{ route('produk.show', $p->id_produk) }}" class="text-decoration-none text-dark">
                    <h6 class="fw-bold mb-1" style="font-size:.95rem">{{ $p->nama_produk }}</h6>
                </a>
                <div class="small mb-1">
                    @for($i = 1; $i <= 5; $i++)<i class="bi {{ $i <= round($rate) ? 'bi-star-fill text-warning' : 'bi-star text-muted' }}" style="font-size:.75rem"></i>@endfor
                    <span class="text-muted" style="font-size:.7rem">({{ $p->ulasan->count() }})</span>
                </div>
                <div class="harga fw-bold mb-1">Rp {{ number_format($p->harga, 0, ',', '.') }}</div>

                @if($p->stok <= 0)
                    <div class="small mb-2 fw-bold text-danger"><i class="bi bi-x-circle-fill me-1"></i>Stok Habis</div>
                @elseif($p->stok <= 5)
                    <div class="small mb-2 fw-bold" style="color:#d97706"><i class="bi bi-exclamation-circle-fill me-1"></i>Sisa {{ $p->stok }} pcs</div>
                @else
                    <div class="small mb-2 text-success"><i class="bi bi-check-circle-fill me-1"></i>Stok: {{ $p->stok }} pcs</div>
                @endif

                <div class="mt-auto d-flex gap-1">
                    <a href="{{ route('pesanan.create', ['produk' => $p->id_produk]) }}" class="btn btn-sm btn-primary flex-fill {{ $p->stok <= 0 ? 'disabled' : '' }}">
                        <i class="bi bi-bag-check me-1"></i>Pesan
                    </a>
                    <form method="POST" action="{{ route('cart.add') }}" class="flex-fill">
                        @csrf
                        <input type="hidden" name="id_produk" value="{{ $p->id_produk }}">
                        <input type="hidden" name="jumlah" value="1">
                        <button class="btn btn-sm btn-outline-secondary w-100" {{ $p->stok <= 0 ? 'disabled' : '' }}><i class="bi bi-cart-plus"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12"><div class="card"><div class="card-body text-center text-muted py-5"><i class="bi bi-search fs-1 d-block mb-2"></i>Produk tidak ditemukan.</div></div></div>
    @endforelse
</div>

<div class="mt-4">{{ $produks->links() }}</div>
@endsection