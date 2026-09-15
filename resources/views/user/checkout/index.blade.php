@extends('layouts.app')
@section('title', 'Checkout - Graniva')

@push('styles')
<style>
    .metode-card{border:2px solid #e6d8ca;border-radius:14px;padding:1rem;text-align:center;cursor:pointer;transition:.2s;background:#fff;height:100%;}
    .metode-card:hover{border-color:var(--primary);}
    .metode-card:has(input:checked){border-color:var(--primary);background:#fdf3ec;box-shadow:0 4px 15px rgba(180,90,60,.15);}
    .qris-code{padding:12px;background:#fff;border-radius:14px;border:3px solid var(--primary);max-width:200px;}
    .qris-code img{width:100%;border-radius:8px;}
</style>
@endpush

@section('content')
<div class="row justify-content-center g-4">
    <div class="col-lg-8">
        <h3 class="fw-bold mb-4">Checkout</h3>

        @if(session('error'))<div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i>{{ session('error') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

        <form method="POST" action="{{ route('checkout.process') }}" enctype="multipart/form-data">
        @csrf

        {{-- Daftar item --}}
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-cart3 me-1"></i>Produk di Keranjang</h6>
            @foreach($items as $i)
            <div class="d-flex gap-3 align-items-center border-bottom py-2">
                @if($i->produk->gambar)
                    <img src="{{ Storage::url($i->produk->gambar) }}" style="width:60px;height:60px;object-fit:cover;border-radius:10px" alt="">
                @endif
                <div class="flex-grow-1">
                    <div class="fw-bold small">{{ $i->produk->nama_produk }}</div>
                    <div class="text-muted small">{{ $i->jumlah }} pcs × Rp {{ number_format($i->produk->harga, 0, ',', '.') }}</div>
                </div>
                <div class="harga small">Rp {{ number_format($i->produk->harga * $i->jumlah, 0, ',', '.') }}</div>
            </div>
            @endforeach
        </div>

        {{-- Alamat --}}
        <div class="card p-4 mb-3">
            <label class="form-label fw-bold"><i class="bi bi-geo-alt me-1"></i>Alamat Pengiriman</label>
            <textarea name="alamat_pengiriman" class="form-control" rows="3" required placeholder="Tulis alamat lengkap...">{{ auth()->user()->alamat }}</textarea>
        </div>

        {{-- Metode (QRIS + GraPay) --}}
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-credit-card me-1"></i>Metode Pembayaran</h6>
            <div class="row g-2 mb-3">
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

            {{-- Detail QRIS --}}
            <div id="detail-QRIS" class="detail-metode">
                <div class="d-flex gap-3 align-items-center flex-wrap">
                    <div class="qris-code">
                        <img src="{{ asset('images/qris.jpeg') }}" alt="QRIS Graniva">
                        <p class="small text-center fw-bold mt-2 mb-0" style="color:var(--primary)">GRANIVA OFFICIAL</p>
                    </div>
                    <div class="small text-muted" style="max-width:300px">
                        <i class="bi bi-info-circle me-1"></i>Scan QRIS sesuai total tagihan lewat m-banking/e-wallet, lalu upload bukti pembayarannya.
                    </div>
                </div>
            </div>

            {{-- Detail GraPay --}}
            <div id="detail-SALDO" class="detail-metode" style="display:none">
                <p class="small mb-2">Saldo Anda: <strong class="harga">Rp {{ number_format(auth()->user()->saldo, 0, ',', '.') }}</strong> · Tagihan: <strong class="harga">Rp {{ number_format($total, 0, ',', '.') }}</strong></p>
                <div id="saldoCukup" class="alert alert-success small py-2 mb-0" style="display:none">
                    <i class="bi bi-check-circle me-1"></i>Saldo mencukupi — pembayaran <strong>otomatis berhasil</strong>. Admin hanya mengatur status pesanan.
                </div>
                <div id="saldoKurang" class="alert alert-danger small py-2 mb-0" style="display:none">
                    <i class="bi bi-x-circle me-1"></i>Saldo tidak cukup, transaksi tidak bisa dilanjutkan. Silakan <a href="{{ route('topup.index') }}" class="fw-bold">Top Up</a> dulu.
                </div>
            </div>
        </div>

        {{-- Bukti (CUMA QRIS) --}}
        <div id="buktiWrap" class="card p-4 mb-3">
            <label class="form-label fw-bold"><i class="bi bi-cloud-upload me-1"></i>Upload Bukti Pembayaran <span class="text-danger">*</span></label>
            <input type="file" name="bukti_bayar" class="form-control" accept="image/*">
            <div class="form-text small">Format gambar (JPG/PNG), maksimal 2MB</div>
        </div>

        <button type="submit" id="btnBayar" class="btn btn-primary w-100 py-2"><i class="bi bi-bag-check me-1"></i>Buat Pesanan</button>
        </form>
    </div>

    {{-- Ringkasan --}}
    <div class="col-lg-4">
        <div class="card p-4" style="position:sticky;top:90px">
            <h6 class="fw-bold mb-3"><i class="bi bi-receipt me-1"></i>Ringkasan Belanja</h6>
            <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Subtotal</span><span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div>
            <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Total Ongkir</span><span>Rp {{ number_format($ongkir, 0, ',', '.') }}</span></div>
            <hr>
            <div class="d-flex justify-content-between fw-bold"><span>Total</span><span class="harga fs-5">Rp {{ number_format($total, 0, ',', '.') }}</span></div>
        </div>
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