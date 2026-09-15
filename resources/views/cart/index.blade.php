@extends('layouts.app')
@section('title', 'Keranjang - Graniva')

@section('content')
<h3 class="fw-bold mb-1">Keranjang Belanja</h3>
<p class="text-muted mb-4">Review pesanan sebelum checkout</p>

@if(session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i>{{ session('error') }}</div>
@endif

@if($items->isEmpty())
    <div class="card">
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-cart-x fs-1 d-block mb-2"></i>
            Keranjang kosong. <a href="{{ route('produk.index') }}">Yuk belanja</a>
        </div>
    </div>
@else
<div class="row g-4">
    {{-- Daftar Item --}}
    <div class="col-lg-8">
        @foreach($items as $i)
        <div class="card p-3 mb-3">
            <div class="d-flex gap-3 align-items-center">
                @if($i->produk->gambar)
                    <img src="{{ Storage::url($i->produk->gambar) }}" style="width:80px;height:80px;object-fit:cover;border-radius:10px" alt="">
                @else
                    <div class="d-flex align-items-center justify-content-center" style="width:80px;height:80px;border-radius:10px;background:#eee"><i class="bi bi-image text-muted"></i></div>
                @endif
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-1">{{ $i->produk->nama_produk }}</h6>
                    <p class="small text-muted mb-1">Rp {{ number_format($i->produk->harga, 0, ',', '.') }} / pcs</p>
                    <p class="harga mb-0">Rp {{ number_format($i->produk->harga * $i->jumlah, 0, ',', '.') }}</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form method="POST" action="{{ route('cart.update', $i->id) }}" class="d-flex align-items-center gap-1">
                        @csrf @method('PUT')
                        <input type="number" name="jumlah" value="{{ $i->jumlah }}" min="1" max="{{ $i->produk->stok }}" class="form-control form-control-sm" style="width:70px">
                        <button class="btn btn-sm btn-outline-primary"><i class="bi bi-arrow-repeat"></i></button>
                    </form>
                    <form method="POST" action="{{ route('cart.remove', $i->id) }}" onsubmit="return confirm('Hapus item ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Ringkasan + Alamat + Checkout --}}
    <div class="col-lg-4">
        <div class="card p-4">
            <h6 class="fw-bold mb-3">Ringkasan</h6>
            <div class="d-flex justify-content-between small mb-3">
                <span>Subtotal</span>
                <span class="harga">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>

            {{-- ALAMAT PENGIRIMAN --}}
            <form method="POST" action="{{ route('cart.toCheckout') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-geo-alt me-1"></i>Alamat Pengiriman</label>
                    <textarea name="alamat" class="form-control" rows="3" 
                              placeholder="Tulis alamat lengkap kamu di sini..." required>{{ old('alamat', $alamatPrefill) }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-bag-check me-1"></i>Lanjut Checkout
                </button>
            </form>
        </div>
    </div>
</div>
@endif
@endsection