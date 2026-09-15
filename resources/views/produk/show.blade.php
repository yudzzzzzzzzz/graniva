@extends('layouts.app')
@section('title', $produk->nama_produk.' - Graniva')

@push('styles')
<style>
    .produk-img-wrap{position:relative;border-radius:24px;overflow:hidden;box-shadow:0 15px 40px rgba(35,39,44,.15);}
    .produk-img-wrap img{width:100%;height:480px;object-fit:cover;transition:transform .5s;}
    .produk-img-wrap:hover img{transform:scale(1.05);}
    .badge-kategori{background:var(--primary);color:#fff;border-radius:50px;padding:.5em 1.2em;font-weight:600;font-size:.8rem;}
    .harga-besar{font-size:2rem;font-weight:800;color:var(--primary-dark);}
    .spec-chip{background:#faf7f4;border:1px solid #f0e7e0;border-radius:12px;padding:.6rem .9rem;}
    .spec-chip .label{display:block;font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;color:#9a928a;}
    .spec-chip .value{font-weight:700;font-size:.9rem;}
    .trust-item{display:flex;align-items:center;gap:.5rem;font-size:.8rem;color:#6c757d;}
    .trust-item i{color:var(--primary);font-size:1rem;}
    .review-card{border-radius:16px;}
    .star-rating i{transition:.15s;}
    .star-rating i:hover{transform:scale(1.2);}

    /* ===== KOTAK DESKRIPSI PRODUK (BARU - RAPI & PROFESIONAL) ===== */
    .deskripsi-box{
        background:linear-gradient(180deg, #faf7f2 0%, #f5ef e7 100%);
        background:#faf7f2;
        border:1px solid #eee7dc;
        border-left:4px solid var(--primary);
        border-radius:0 16px 16px 0;
        padding:20px 24px;
        margin-top:8px;
        max-height:280px;
        overflow-y:auto;
    }
    .deskripsi-box::-webkit-scrollbar{ width:6px; }
    .deskripsi-box::-webkit-scrollbar-track{ background:#f5ef e7; background:#f5efe7; border-radius:6px; }
    .deskripsi-box::-webkit-scrollbar-thumb{ background:#d9cfc2; border-radius:6px; }
    .deskripsi-box::-webkit-scrollbar-thumb:hover{ background:var(--primary); }
    .deskripsi-produk{
        white-space: pre-line;        /* enter dari admin kebaca sebagai baris baru */
        text-align: justify;           /* rata kiri-kanan */
        line-height: 1.85;             /* spasi antar baris nyaman */
        font-size: .9rem;
        color: #4a4a4a;
        margin: 0;
        word-wrap: break-word;
    }
    .deskripsi-produk:first-letter{
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--primary-dark);
        font-family: Georgia, serif;
    }
    .deskripsi-title{
        font-weight:800;
        font-size:.95rem;
        color:var(--dark);
        margin-bottom:10px;
        display:flex;
        align-items:center;
        gap:8px;
    }
    .deskripsi-title i{ color:var(--primary); font-size:1.1rem; }
</style>
@endpush

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="{{ route('produk.index') }}" class="text-decoration-none">Katalog</a></li>
        <li class="breadcrumb-item active">{{ $produk->nama_produk }}</li>
    </ol>
</nav>

@if(session('success'))<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@if(session('error'))<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-x-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@if($errors->any())<div class="alert alert-danger alert-dismissible fade show"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="produk-img-wrap">
            @if($produk->gambar)
                <img src="{{ Storage::url($produk->gambar) }}" alt="{{ $produk->nama_produk }}">
            @else
                <div class="d-flex align-items-center justify-content-center" style="height:480px;background:#e8e2da;color:#b45a3c;font-size:4rem"><i class="bi bi-image"></i></div>
            @endif
        </div>
    </div>

    <div class="col-lg-7">
        <span class="badge-kategori">{{ $produk->kategori->nama_kategori ?? $produk->jenis ?? 'Umum' }}</span>

        <h2 class="fw-bold mt-3 mb-2">{{ $produk->nama_produk }}</h2>

        <div class="d-flex align-items-center gap-2 mb-3">
            <div>
                @for($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $i <= round($rating) ? 'bi-star-fill text-warning' : 'bi-star text-muted' }}"></i>
                @endfor
            </div>
            <span class="text-muted small">{{ number_format($rating, 1) }} ({{ $jumlahUlasan }} ulasan)</span>
        </div>

        <div class="harga-besar mb-3">Rp {{ number_format($produk->harga, 0, ',', '.') }}</div>

        <div class="mb-3">
            @if($produk->stok == 0)
                <span class="badge bg-danger px-3 py-2">Stok Habis</span>
            @elseif($produk->stok <= 5)
                <span class="badge bg-warning text-dark px-3 py-2">Stok Terbatas: {{ $produk->stok }} pcs</span>
            @else
                <span class="badge bg-success px-3 py-2">Stok Tersedia: {{ $produk->stok }} pcs</span>
            @endif
        </div>

        {{-- Spesifikasi --}}
        @if($produk->ukuran || $produk->warna || $produk->jenis || $produk->tekstur)
        <div class="row g-2 mb-3">
            @if($produk->ukuran)<div class="col-6 col-md-3"><div class="spec-chip"><span class="label">Ukuran</span><span class="value">{{ $produk->ukuran }}</span></div></div>@endif
            @if($produk->warna)<div class="col-6 col-md-3"><div class="spec-chip"><span class="label">Warna</span><span class="value">{{ $produk->warna }}</span></div></div>@endif
            @if($produk->jenis)<div class="col-6 col-md-3"><div class="spec-chip"><span class="label">Jenis</span><span class="value">{{ $produk->jenis }}</span></div></div>@endif
            @if($produk->tekstur)<div class="col-6 col-md-3"><div class="spec-chip"><span class="label">Tekstur</span><span class="value">{{ $produk->tekstur }}</span></div></div>@endif
        </div>
        @endif

        {{-- ===== DESKRIPSI (UPDATE: RAPI & PROFESIONAL) ===== --}}
        @if($produk->deskripsi)
        <div class="border-top pt-3 mb-3">
            <div class="deskripsi-title"><i class="bi bi-text-paragraph"></i>Deskripsi Produk</div>
            <div class="deskripsi-box">
                <div class="deskripsi-produk">{{ $produk->deskripsi }}</div>
            </div>
        </div>
        @endif

        {{-- Tombol aksi + Wishlist --}}
        <div class="d-flex gap-2 mb-3">
            <a href="{{ route('pesanan.create', ['produk' => $produk->id_produk]) }}" class="btn btn-primary flex-fill py-2" {{ $produk->stok == 0 ? 'disabled' : '' }}>
                <i class="bi bi-bag-check me-1"></i>Pesan Sekarang
            </a>
            <form method="POST" action="{{ route('cart.add') }}" class="flex-fill">
                @csrf
                <input type="hidden" name="id_produk" value="{{ $produk->id_produk }}">
                <input type="hidden" name="jumlah" value="1">
                <button class="btn btn-outline-secondary w-100 py-2" {{ $produk->stok == 0 ? 'disabled' : '' }}>
                    <i class="bi bi-cart-plus me-1"></i>Keranjang
                </button>
            </form>
            @php $inWish = \App\Models\Wishlist::where('id_user', auth()->id())->where('id_produk', $produk->id_produk)->exists(); @endphp
            <form method="POST" action="{{ route('wishlist.toggle') }}">
                @csrf
                <input type="hidden" name="id_produk" value="{{ $produk->id_produk }}">
                <button class="btn {{ $inWish ? 'btn-danger' : 'btn-outline-danger' }} py-2" title="Wishlist">
                    <i class="bi {{ $inWish ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                </button>
            </form>
        </div>

        {{-- Trust badges --}}
        <div class="d-flex gap-3 flex-wrap">
            <div class="trust-item"><i class="bi bi-truck"></i>Pengiriman Cepat</div>
            <div class="trust-item"><i class="bi bi-patch-check-fill"></i>Kualitas Terjamin</div>
            <div class="trust-item"><i class="bi bi-shield-check"></i>Pembayaran Aman</div>
        </div>
    </div>
</div>

{{-- ===== ULASAN & RATING ===== --}}
<div class="card p-4 review-card">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="fw-bold mb-0"><i class="bi bi-star me-1"></i>Ulasan Produk</h5>
        <span class="badge badge-soft">{{ number_format($rating, 1) }} / 5 ({{ $jumlahUlasan }} ulasan)</span>
    </div>

    @auth
        @if($sudahBeli)
        <div class="border rounded-3 p-3 mb-4" style="background:#faf7f4">
            <h6 class="fw-semibold mb-3">Tulis Ulasan Anda</h6>
            <form method="POST" action="{{ route('ulasan.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id_produk" value="{{ $produk->id_produk }}">
                <div class="mb-3">
                    <label class="form-label small">Rating Anda</label>
                    <div class="star-rating d-inline-flex gap-1" style="cursor:pointer;font-size:1.6rem">
                        <i class="bi bi-star" data-value="1"></i><i class="bi bi-star" data-value="2"></i><i class="bi bi-star" data-value="3"></i><i class="bi bi-star" data-value="4"></i><i class="bi bi-star" data-value="5"></i>
                    </div>
                    <input type="hidden" name="rating" id="ratingInput" required>
                    <div class="small text-muted" id="ratingText">Klik bintang untuk memberi rating</div>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Komentar</label>
                    <textarea name="komentar" class="form-control" rows="3" placeholder="Ceritakan pengalaman Anda..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Upload Gambar <span class="text-muted">(opsional)</span></label>
                    <input type="file" name="gambar" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-send me-1"></i>Kirim Ulasan</button>
            </form>
        </div>
        @else
        <div class="alert alert-light border small mb-4">
            <i class="bi bi-lock me-2"></i>Anda harus <strong>membeli produk ini</strong> untuk menulis ulasan.
            <a href="{{ route('pesanan.create', ['produk' => $produk->id_produk]) }}">Pesan sekarang</a>
        </div>
        @endif
    @else
    <p class="text-muted small mb-4"><a href="{{ route('login') }}">Login</a> untuk menulis ulasan.</p>
    @endauth

    @forelse($produk->ulasan as $u)
    <div class="border-bottom py-3">
        <div class="d-flex justify-content-between align-items-start gap-2">
            <div class="flex-grow-1" style="min-width:0">
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <span class="fw-semibold">{{ $u->user->nama ?? 'User' }}</span>
                    <span class="small">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= $u->rating ? 'bi-star-fill text-warning' : 'bi-star text-muted' }}"></i>
                        @endfor
                    </span>
                </div>
                <div class="small text-muted mb-1">{{ $u->created_at->diffForHumans() }}</div>
                @if($u->komentar)<p class="small mb-2" style="word-wrap:break-word">{{ $u->komentar }}</p>@endif
                @if($u->gambar)
                    <img src="{{ Storage::url($u->gambar) }}" style="max-width:150px;max-height:120px;border-radius:10px;cursor:pointer;object-fit:cover" onclick="window.open(this.src,'_blank')" alt="Foto ulasan">
                @endif
                @if($u->balasan_admin)
                <div class="mt-2 p-3 rounded-3" style="background:#f0e7e0;border-left:3px solid var(--primary)">
                    <p class="small fw-bold mb-1"><i class="bi bi-patch-check-fill me-1" style="color:var(--primary)"></i>Balasan Graniva</p>
                    <p class="small mb-0">{{ $u->balasan_admin }}</p>
                </div>
                @endif
            </div>
            @auth
                @if(auth()->id() == $u->id_user)
                <form method="POST" action="{{ route('ulasan.destroy', $u->id_ulasan) }}" onsubmit="return confirm('Hapus ulasan ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
                @endif
            @endauth
        </div>
    </div>
    @empty
    <p class="text-muted small mb-0">Belum ada ulasan. Jadilah yang pertama!</p>
    @endforelse
</div>

<script>
document.querySelectorAll('.star-rating i').forEach(star => {
    star.addEventListener('click', function() {
        const value = parseInt(this.dataset.value);
        document.getElementById('ratingInput').value = value;
        const labels = ['', 'Sangat Buruk', 'Buruk', 'Cukup', 'Baik', 'Sangat Baik'];
        document.getElementById('ratingText').textContent = value + ' bintang - ' + labels[value];
        document.querySelectorAll('.star-rating i').forEach(s => {
            if (parseInt(s.dataset.value) <= value) { s.classList.remove('bi-star'); s.classList.add('bi-star-fill','text-warning'); }
            else { s.classList.remove('bi-star-fill','text-warning'); s.classList.add('bi-star'); }
        });
    });
});
</script>
@endsection