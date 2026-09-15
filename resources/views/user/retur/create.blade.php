@extends('layouts.app')
@section('title', 'Ajukan Retur - Graniva')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card p-4">
            <h4 class="fw-bold mb-3">Ajukan Retur</h4>

            <div class="border rounded-3 p-3 mb-3" style="background:#faf7f4">
                <p class="fw-bold mb-1">{{ $pesanan->produk->nama_produk }}</p>
                <p class="small text-muted mb-0">Pesanan #{{ $pesanan->id_pesanan }} · {{ $pesanan->jumlah }} pcs</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('retur.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id_pesanan" value="{{ $pesanan->id_pesanan }}">

                <div class="mb-3">
                    <label class="form-label">Alasan Retur</label>
                    <textarea name="alasan" class="form-control" rows="3" required placeholder="Contoh: barang pecah, ukuran salah..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Foto Bukti (opsional)</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>
                <button class="btn btn-primary w-100"><i class="bi bi-send me-1"></i>Kirim Pengajuan</button>
            </form>
        </div>
    </div>
</div>
@endsection