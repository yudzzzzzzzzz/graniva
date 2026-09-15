@extends('layouts.app')
@section('title', 'Top Up GraPay - Graniva')

@push('styles')
<style>
    .qris-hero{background:linear-gradient(135deg,#ffffff 0%,#fdf3ec 100%);border-radius:20px;overflow:hidden;box-shadow:0 10px 40px rgba(180,90,60,.15);}
    .qris-video{width:100%;border-radius:14px;box-shadow:0 8px 25px rgba(0,0,0,.15);background:#000;}
    .qris-code{padding:16px;background:#fff;border-radius:16px;border:3px solid var(--primary);box-shadow:0 4px 15px rgba(180,90,60,.12);}
    .qris-code img{width:100%;border-radius:10px;}
    .step-num{width:28px;height:28px;background:var(--primary);color:#fff;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:.85rem;flex-shrink:0;}
    .step-item{display:flex;gap:.8rem;align-items:flex-start;padding:.6rem 0;}
    .chip-nominal{border:1.5px solid #e6d8ca;background:#fff;border-radius:50px;padding:.4em 1.1em;font-size:.8rem;font-weight:700;color:var(--primary-dark);cursor:pointer;transition:.2s;}
    .chip-nominal:hover{background:var(--krem);}
    .grapay-widget{background:#fff;border:1px solid #f0e7e0;border-radius:20px;padding:1.2rem 1.4rem;position:relative;overflow:hidden;box-shadow:0 8px 30px rgba(180,90,60,.12);}
    .grapay-widget::after{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--primary),var(--primary-dark));}
    .g-logo{width:44px;height:44px;background:var(--primary);color:#fff;border-radius:14px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.2rem;}
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h3 class="fw-bold mb-4">Top Up GraPay</h3>

        @if(session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

        {{-- Saldo sekarang --}}
        <div class="grapay-widget d-flex align-items-center gap-3 mb-4">
            <span class="g-logo">G</span>
            <div>
                <div style="font-size:.7rem;color:#9a928a;letter-spacing:1px">SALDO GRAPAY ANDA</div>
                <div class="fw-bold fs-4" style="color:var(--primary-dark)">Rp {{ number_format(auth()->user()->saldo, 0, ',', '.') }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('topup.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- Nominal --}}
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-cash-coin me-1"></i>Masukkan Nominal</h6>
            <div class="input-group mb-3">
                <span class="input-group-text fw-bold">Rp</span>
                <input type="number" name="nominal" id="nominal" class="form-control form-control-lg fw-bold" placeholder="Minimal 1.000" min="1000" required>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="chip-nominal" data-n="10000">10rb</button>
                <button type="button" class="chip-nominal" data-n="20000">20rb</button>
                <button type="button" class="chip-nominal" data-n="50000">50rb</button>
                <button type="button" class="chip-nominal" data-n="100000">100rb</button>
                <button type="button" class="chip-nominal" data-n="200000">200rb</button>
            </div>
            <div class="form-text small mt-2">Nominal bebas, minimal Rp 1.000</div>
        </div>

        {{-- QRIS --}}
        <div class="qris-hero mb-3">
            <div class="row g-0 align-items-stretch">
                <div class="col-md-5 p-3 d-flex align-items-center"><div class="w-100">
                    <p class="small fw-bold mb-2" style="color:var(--primary)"><i class="bi bi-play-circle-fill me-1"></i>Cara Scan QRIS</p>
                    <video autoplay loop muted playsinline class="qris-video"><source src="{{ asset('videos/tutor.mp4') }}" type="video/mp4"></video>
                </div></div>
                <div class="col-md-7 p-4 d-flex flex-column justify-content-center">
                    <span class="badge bg-success mb-2 align-self-start"><i class="bi bi-lightning-charge-fill me-1"></i>Scan untuk Top Up</span>
                    <div class="qris-code mb-3 mx-auto" style="max-width:220px">
                        <img src="{{ asset('images/qris.jpeg') }}" alt="QRIS Graniva">
                        <p class="small text-center fw-bold mt-2 mb-0" style="color:var(--primary)">GRANIVA OFFICIAL</p>
                    </div>
                    <div class="small text-muted"><i class="bi bi-info-circle me-1"></i>Scan sesuai nominal yang kamu isi, lalu upload buktinya.</div>
                </div>
            </div>
        </div>

        {{-- Langkah --}}
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-2"><i class="bi bi-list-ol me-1"></i>Langkah Top Up</h6>
            <div class="step-item"><span class="step-num">1</span><div class="small">Isi nominal top up (minimal Rp 1.000)</div></div>
            <div class="step-item"><span class="step-num">2</span><div class="small">Scan QRIS di atas sesuai nominal</div></div>
            <div class="step-item"><span class="step-num">3</span><div class="small">Upload bukti pembayaran</div></div>
            <div class="step-item"><span class="step-num">4</span><div class="small">Tunggu verifikasi admin — saldo masuk otomatis</div></div>
        </div>

        {{-- Bukti --}}
        <div class="card p-4 mb-3">
            <label class="form-label fw-bold"><i class="bi bi-cloud-upload me-1"></i>Upload Bukti Pembayaran <span class="text-danger">*</span></label>
            <input type="file" name="bukti_bayar" class="form-control" accept="image/*" required>
        </div>

        <button class="btn btn-primary w-100 py-2"><i class="bi bi-wallet2 me-1"></i>Top Up Sekarang</button>
        </form>

        {{-- Riwayat --}}
        @if($riwayat->isNotEmpty())
        <div class="card p-4 mt-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-1"></i>Riwayat Top Up</h6>
            @foreach($riwayat as $t)
            <div class="d-flex justify-content-between align-items-center border-bottom py-2 small">
                <span>Rp {{ number_format($t->nominal, 0, ',', '.') }} <span class="text-muted">· {{ $t->created_at->format('d M Y') }}</span></span>
                @if($t->status == 'sukses') <span class="badge bg-success">Berhasil</span>
                @elseif($t->status == 'gagal') <span class="badge bg-danger">Ditolak</span>
                @else <span class="badge bg-warning text-dark">Menunggu</span> @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<script>
document.querySelectorAll('.chip-nominal').forEach(c => {
    c.addEventListener('click', () => document.getElementById('nominal').value = c.dataset.n);
});
</script>
@endsection