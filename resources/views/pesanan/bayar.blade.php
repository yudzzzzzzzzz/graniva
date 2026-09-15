@extends('layouts.app')
@section('title', 'Pembayaran - Graniva')

@push('styles')
<style>
    .qris-hero{background:linear-gradient(135deg,#ffffff 0%,#fdf3ec 100%);border-radius:20px;overflow:hidden;box-shadow:0 10px 40px rgba(180,90,60,.15);}
    .qris-video{width:100%;border-radius:14px;box-shadow:0 8px 25px rgba(0,0,0,.15);background:#000;}
    .qris-code{padding:16px;background:#fff;border-radius:16px;border:3px solid var(--primary);box-shadow:0 4px 15px rgba(180,90,60,.12);}
    .qris-code img{width:100%;border-radius:10px;}
    .step-num{width:28px;height:28px;background:var(--primary);color:#fff;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:.85rem;flex-shrink:0;}
    .step-item{display:flex;gap:.8rem;align-items:flex-start;padding:.6rem 0;}
    .countdown-box{background:#fff3cd;border:1px solid #ffc107;border-radius:12px;padding:10px 14px;display:flex;align-items:center;gap:.6rem;}
    .metode-card{border:2px solid #e6d8ca;border-radius:14px;padding:1rem;text-align:center;cursor:pointer;transition:.2s;background:#fff;height:100%;}
    .metode-card:hover{border-color:var(--primary);}
    .metode-card:has(input:checked){border-color:var(--primary);background:#fdf3ec;box-shadow:0 4px 15px rgba(180,90,60,.15);}
</style>
@endpush

@section('content')
@php $draft = $draftMode ?? false; @endphp
@php $total = $pesanan->harga + ($pesanan->ongkir ?? 0); @endphp

<div class="row justify-content-center">
    <div class="col-lg-9">
        <h3 class="fw-bold mb-4">Pembayaran {{ $draft ? 'Pesanan Baru' : 'Pesanan #'.$pesanan->id_pesanan }}</h3>

        @if(session('error'))<div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i>{{ session('error') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

        {{-- Ringkasan --}}
        <div class="card p-4 mb-3">
            <div class="d-flex gap-3 align-items-center">
                @if($pesanan->produk->gambar)
                    <img src="{{ Storage::url($pesanan->produk->gambar) }}" style="width:70px;height:70px;object-fit:cover;border-radius:10px" alt="">
                @endif
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-1">{{ $pesanan->produk->nama_produk }}</h6>
                    <p class="small text-muted mb-0">{{ $pesanan->jumlah }} pcs × Rp {{ number_format($pesanan->produk->harga, 0, ',', '.') }}</p>
                </div>
                <div class="text-end">
                    <div class="small text-muted">Subtotal: Rp {{ number_format($pesanan->harga, 0, ',', '.') }}</div>
                    <div class="small text-muted">Ongkir: Rp {{ number_format($pesanan->ongkir ?? 0, 0, ',', '.') }}</div>
                    <div class="harga fs-5">Rp {{ number_format($total, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ $draft ? route('pesanan.payDraft') : route('pesanan.pay', $pesanan->id_pesanan) }}" enctype="multipart/form-data">
        @csrf

        {{-- Metode (QRIS + GraPay) --}}
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-credit-card me-1"></i>Pilih Metode Pembayaran</h6>
            <div class="row g-2">
                <div class="col-6">
                    <label class="metode-card d-block">
                        <input type="radio" name="metode" value="QRIS" class="metode-radio d-none" required checked>
                        <i class="bi bi-qr-code-scan fs-2 d-block mb-1" style="color:var(--primary)"></i>
                        <strong>QRIS</strong>
                        <div class="small text-muted">Scan & upload bukti</div>
                    </label>
                </div>
                <div class="col-6">
                    <label class="metode-card d-block">
                        <input type="radio" name="metode" value="SALDO" class="metode-radio d-none">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle fw-bold mt-1" style="width:34px;height:34px;background:var(--primary);color:#fff">G</span>
                        <strong class="d-block">GraPay</strong>
                        <div class="small text-muted">Saldo: Rp {{ number_format(auth()->user()->saldo, 0, ',', '.') }}</div>
                    </label>
                </div>
            </div>
        </div>

        {{-- Detail QRIS --}}
        <div id="detail-QRIS" class="detail-metode">
            <div class="qris-hero mb-3">
                <div class="row g-0 align-items-stretch">
                    <div class="col-md-5 p-3 d-flex align-items-center"><div class="w-100">
                        <p class="small fw-bold mb-2" style="color:var(--primary)"><i class="bi bi-play-circle-fill me-1"></i>Cara Scan QRIS</p>
                        <video autoplay loop muted playsinline class="qris-video"><source src="{{ asset('videos/tutor.mp4') }}" type="video/mp4"></video>
                    </div></div>
                    <div class="col-md-7 p-4 d-flex flex-column justify-content-center">
                        <span class="badge bg-success mb-2 align-self-start"><i class="bi bi-lightning-charge-fill me-1"></i>Scan untuk Membayar</span>
                        <h5 class="fw-bold mb-1">Total Pembayaran</h5>
                        <h3 class="harga mb-3">Rp {{ number_format($total, 0, ',', '.') }}</h3>
                        <div class="qris-code mb-3 mx-auto" style="max-width:220px">
                            <img src="{{ asset('images/qris.jpeg') }}" alt="QRIS Graniva">
                            <p class="small text-center fw-bold mt-2 mb-0" style="color:var(--primary)">GRANIVA OFFICIAL</p>
                        </div>
                        <div class="countdown-box"><i class="bi bi-clock-history fs-5 text-warning"></i>
                            <div class="small"><strong class="d-block">Selesaikan dalam 24 jam</strong><span class="text-muted">Pesanan otomatis batal jika belum dibayar</span></div></div>
                    </div>
                </div>
            </div>
            <div class="card p-4 mb-3">
                <h6 class="fw-bold mb-2"><i class="bi bi-list-ol me-1"></i>Langkah Pembayaran</h6>
                <div class="step-item"><span class="step-num">1</span><div class="small">Buka aplikasi <strong>m-banking</strong> atau <strong>e-wallet</strong> Anda</div></div>
                <div class="step-item"><span class="step-num">2</span><div class="small">Pilih menu <strong>Scan QR</strong> atau <strong>Bayar</strong></div></div>
                <div class="step-item"><span class="step-num">3</span><div class="small">Arahkan kamera ke QR code atau upload dari galeri</div></div>
                <div class="step-item"><span class="step-num">4</span><div class="small">Konfirmasi nominal lalu bayar</div></div>
                <div class="step-item"><span class="step-num">5</span><div class="small">Upload <strong>bukti pembayaran</strong> di bawah</div></div>
            </div>
        </div>

        {{-- Detail GraPay (tanpa bukti, otomatis) --}}
        <div id="detail-SALDO" class="detail-metode card p-4 mb-3" style="display:none">
            <h6 class="fw-bold mb-2"><span class="badge me-1" style="background:var(--primary)">G</span> Bayar Pakai GraPay</h6>
            <p class="small mb-2">Saldo Anda: <strong class="harga">Rp {{ number_format(auth()->user()->saldo, 0, ',', '.') }}</strong> · Tagihan: <strong class="harga">Rp {{ number_format($total, 0, ',', '.') }}</strong></p>
            <div id="saldoCukup" class="alert alert-success small py-2 mb-0" style="display:none">
                <i class="bi bi-check-circle me-1"></i>Saldo mencukupi — pembayaran <strong>otomatis berhasil</strong>. Admin hanya mengatur status pesanan.
            </div>
            <div id="saldoKurang" class="alert alert-danger small py-2 mb-0" style="display:none">
                <i class="bi bi-x-circle me-1"></i>Saldo tidak cukup, transaksi tidak bisa dilanjutkan. Silakan <a href="{{ route('topup.index') }}" class="fw-bold">Top Up</a> dulu.
            </div>
        </div>

        {{-- Upload bukti (CUMA muncul kalau QRIS) --}}
        <div id="buktiWrap" class="card p-4 mb-3">
            <label class="form-label fw-bold"><i class="bi bi-cloud-upload me-1"></i>Upload Bukti Pembayaran <span class="text-danger">*</span></label>
            <input type="file" name="bukti_bayar" class="form-control" accept="image/*">
            <div class="form-text small">Format gambar (JPG/PNG), maksimal 2MB</div>
        </div>

        <button type="submit" id="btnBayar" class="btn btn-primary w-100"><i class="bi bi-check-circle me-1"></i>Konfirmasi Pembayaran</button>
        </form>
    </div>
</div>

<script>
const SALDO = {{ auth()->user()->saldo }};
const TOTAL = {{ $total }};

function updateMetode(){
    const metode = document.querySelector('input[name="metode"]:checked').value;
    document.querySelectorAll('.detail-metode').forEach(d => d.style.display = 'none');
    const detail = document.getElementById('detail-' + metode);
    if (detail) detail.style.display = 'block';

    const buktiWrap = document.getElementById('buktiWrap');
    const bukti = document.querySelector('input[name="bukti_bayar"]');
    const btn = document.getElementById('btnBayar');

    if (metode === 'SALDO') {
        buktiWrap.style.display = 'none';
        if (bukti) bukti.required = false;
        if (SALDO >= TOTAL) {
            document.getElementById('saldoCukup').style.display = 'block';
            document.getElementById('saldoKurang').style.display = 'none';
            btn.disabled = false;
        } else {
            document.getElementById('saldoCukup').style.display = 'none';
            document.getElementById('saldoKurang').style.display = 'block';
            btn.disabled = true;
        }
    } else {
        buktiWrap.style.display = 'block';
        if (bukti) bukti.required = true;
        btn.disabled = false;
    }
}
document.querySelectorAll('.metode-radio').forEach(r => r.addEventListener('change', updateMetode));
updateMetode();
</script>
@endsection