@extends('layouts.app')
@section('title', 'Form Pemesanan - Graniva')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card p-4">
            <h4 class="fw-bold mb-4">Form Pemesanan</h4>

            <div class="border rounded-3 p-3 mb-4" style="background:#faf7f4">
                <div class="d-flex gap-3">
                    @if($produk->gambar)
                        <img src="{{ Storage::url($produk->gambar) }}" style="width:80px;height:80px;object-fit:cover;border-radius:10px" alt="">
                    @endif
                    <div>
                        <h6 class="fw-bold mb-1">{{ $produk->nama_produk }}</h6>
                        <p class="small text-muted mb-1">{{ $produk->jenis ?? '' }} {{ $produk->ukuran ? '· '.$produk->ukuran : '' }}</p>
                        <p class="harga mb-0">Rp {{ number_format($produk->harga, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            @if($produk->stok <= 0)
                <div class="alert alert-danger"><i class="bi bi-x-octagon me-2"></i><strong>Stok habis!</strong> Produk ini sudah terjual habis. Silakan pilih produk lain.</div>
                <a href="{{ route('produk.index') }}" class="btn btn-secondary w-100"><i class="bi bi-arrow-left me-1"></i>Kembali ke Katalog</a>
            @else
                <form method="POST" action="{{ route('pesanan.store') }}" id="formPesan">
                    @csrf
                    <input type="hidden" name="id_produk" value="{{ $produk->id_produk }}">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah (pcs)</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" value="1" min="1" max="{{ $produk->stok }}" required oninput="cekStok()">
                        <div class="form-text small">Stok tersedia: <strong>{{ $produk->stok }} pcs</strong></div>
                    </div>

                    {{-- Peringatan kalau jumlah > stok (muncul SEBELUM pencet tombol) --}}
                    <div id="stockWarning" class="alert alert-warning py-2" style="display:none">
                        <i class="bi bi-exclamation-triangle me-2"></i>Stok produk sisa <strong>{{ $produk->stok }}</strong> pcs. Kurangi jumlah pesanan Anda.
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold"><i class="bi bi-geo-alt me-1"></i>Alamat Pengiriman</label>
                        <textarea name="alamat_pengiriman" class="form-control" rows="3" required placeholder="Tulis alamat lengkap...">{{ auth()->user()->alamat }}</textarea>
                    </div>

                    <div class="border rounded-3 p-3 mb-4" style="background:#faf7f4">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Perkiraan Total</span>
                            <span class="harga fs-5" id="totalHarga">Rp {{ number_format($produk->harga, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button type="submit" id="btnPesan" class="btn btn-primary w-100 py-2"><i class="bi bi-bag-check me-1"></i>Lanjut ke Pembayaran</button>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
const STOK = {{ $produk->stok }};
const HARGA = {{ $produk->harga }};

function cekStok(){
    const jumlah = parseInt(document.getElementById('jumlah').value) || 0;
    const warning = document.getElementById('stockWarning');
    const btn = document.getElementById('btnPesan');

    if (jumlah > STOK) {
        warning.style.display = 'block';
        btn.disabled = true;
        btn.classList.add('disabled');
    } else {
        warning.style.display = 'none';
        btn.disabled = false;
        btn.classList.remove('disabled');
    }

    document.getElementById('totalHarga').textContent = 'Rp ' + (HARGA * (jumlah || 1)).toLocaleString('id-ID');
}
</script>
@endsection