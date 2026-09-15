@extends('layouts.app')
@section('title', 'Tarik Saldo - Graniva')

@push('styles')
<style>
    .metode-card{border:1.5px solid #e6d8ca;border-radius:14px;padding:1rem;text-align:center;cursor:pointer;transition:.2s;background:#fff;height:100%;}
    .metode-card:hover{border-color:var(--primary);}
    .metode-card:has(input:checked){border-color:var(--primary);background:#fdf3ec;box-shadow:0 4px 15px rgba(180,90,60,.15);}
    .summary-box{background:#faf7f4;border:1px dashed #e0d4c8;border-radius:14px;padding:1rem;}
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <h3 class="fw-bold mb-4">Tarik Saldo GraPay</h3>

        @if(session('success'))<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

        {{-- Saldo tersedia --}}
        <div class="card p-4 mb-3">
            <div class="d-flex align-items-center gap-3">
                <span class="d-inline-flex align-items-center justify-content-center rounded-3 fw-bold" style="width:46px;height:46px;background:var(--primary);color:#fff;font-size:1.2rem">G</span>
                <div>
                    <div style="font-size:.7rem;color:#9a928a;letter-spacing:1px">SALDO TERSEDIA</div>
                    <div class="fw-bold fs-5" style="color:var(--primary-dark)">Rp {{ number_format(auth()->user()->saldo, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('withdraw.store') }}">
        @csrf

        {{-- Nominal --}}
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-cash me-1"></i>Nominal Penarikan</h6>
            <div class="input-group mb-2">
                <span class="input-group-text fw-bold">Rp</span>
                <input type="number" name="nominal" id="nominal" class="form-control form-control-lg fw-bold" placeholder="Minimal 1.000" min="1000" required oninput="hitung()">
            </div>
            <div class="form-text small">Nominal bebas, minimal Rp 1.000</div>
        </div>

        {{-- Metode --}}
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-wallet2 me-1"></i>Metode Pencairan</h6>
            <div class="row g-2 mb-3">
                @foreach(['DANA','GOPAY','OVO','BANK'] as $m)
                <div class="col-6 col-md-3">
                    <label class="metode-card d-block">
                        <input type="radio" name="metode" value="{{ $m }}" class="d-none" required onchange="toggleBank(this)">
                        <i class="bi {{ $m=='BANK' ? 'bi-bank' : 'bi-wallet2' }} fs-3 d-block mb-1" style="color:var(--primary)"></i>
                        <strong class="small">{{ $m }}</strong>
                    </label>
                </div>
                @endforeach
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold">No. Rekening / No. HP Tujuan <span class="text-danger">*</span></label>
                <input type="text" name="no_rekening" class="form-control" placeholder="Contoh: 0812xxxxxxx / 1234567890" required>
            </div>

            <div id="bankField" class="mb-3" style="display:none">
                <label class="form-label small fw-bold">Nama Bank <span class="text-danger">*</span></label>
                <input type="text" name="nama_bank" id="nama_bank" class="form-control" placeholder="Contoh: BCA / BRI / Mandiri">
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="summary-box mb-3">
            <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Nominal</span><span id="sumNominal">Rp 0</span></div>
            <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Biaya Admin</span><span>Rp 500</span></div>
            <hr class="my-2">
            <div class="d-flex justify-content-between fw-bold"><span>Total Dipotong</span><span class="harga" id="sumTotal">Rp 500</span></div>
        </div>

        <button class="btn btn-primary w-100 py-2"><i class="bi bi-cash-coin me-1"></i>Ajukan Penarikan</button>
        </form>

        {{-- Riwayat --}}
        @if($riwayat->isNotEmpty())
        <div class="card p-4 mt-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-1"></i>Riwayat Penarikan</h6>
            @foreach($riwayat as $w)
            <div class="d-flex justify-content-between align-items-center border-bottom py-2 small">
                <span>Rp {{ number_format($w->nominal, 0, ',', '.') }} → {{ $w->metode }} <span class="text-muted">· {{ $w->created_at->format('d M Y') }}</span></span>
                @if($w->status=='sukses') <span class="badge bg-success">Berhasil</span>
                @elseif($w->status=='gagal') <span class="badge bg-danger">Ditolak</span>
                @else <span class="badge bg-warning text-dark">Menunggu</span> @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<script>
function fmt(n){return 'Rp '+Number(n||0).toLocaleString('id-ID');}
function hitung(){
    const n = parseInt(document.getElementById('nominal').value)||0;
    document.getElementById('sumNominal').textContent = fmt(n);
    document.getElementById('sumTotal').textContent = fmt(n+500);
}
function toggleBank(r){
    document.getElementById('bankField').style.display = (r.value=='BANK')?'block':'none';
    document.getElementById('nama_bank').required = (r.value=='BANK');
}
</script>
@endsection