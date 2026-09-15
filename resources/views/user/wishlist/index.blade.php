@extends('layouts.app')
@section('title', 'Wishlist - Graniva')

@section('content')
<h3 class="fw-bold mb-1">Wishlist Saya</h3>
<p class="text-muted mb-4">Produk yang kamu simpan untuk nanti</p>

@if(session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif

@forelse($wishlists as $w)
@php $p = $w->produk; @endphp
<div class="card p-3 mb-3">
    <div class="d-flex gap-3 align-items-center">
        <a href="{{ route('produk.show', $p->id_produk) }}">
            @if($p->gambar)
                <img src="{{ Storage::url($p->gambar) }}" style="width:80px;height:80px;object-fit:cover;border-radius:12px" alt="">
            @else
                <div class="d-flex align-items-center justify-content-center" style="width:80px;height:80px;border-radius:12px;background:#eee"><i class="bi bi-image text-muted"></i></div>
            @endif
        </a>
        <div class="flex-grow-1">
            <h6 class="fw-bold mb-1">{{ $p->nama_produk }}</h6>
            <div class="harga mb-1">Rp {{ number_format($p->harga, 0, ',', '.') }}</div>
            @if($p->stok <= 0)
                <span class="badge bg-danger">Stok Habis</span>
            @else
                <span class="badge bg-success">Stok {{ $p->stok }}</span>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('pesanan.create', ['produk' => $p->id_produk]) }}" class="btn btn-sm btn-primary {{ $p->stok <= 0 ? 'disabled' : '' }}"><i class="bi bi-bag-check me-1"></i>Pesan</a>
            <form method="POST" action="{{ route('wishlist.destroy', $w->id_wishlist) }}" onsubmit="return confirm('Hapus dari wishlist?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
            </form>
        </div>
    </div>
</div>
@empty
<div class="card"><div class="card-body text-center text-muted py-5">
    <i class="bi bi-heart fs-1 d-block mb-2"></i>
    Wishlist masih kosong. <a href="{{ route('produk.index') }}">Yuk simpan produk favoritmu</a>
</div></div>
@endforelse
@endsection