@extends('layouts.app')
@section('title', 'Pesanan Saya - Graniva')

@push('styles')
<style>
    .modal-header.grapay{background:linear-gradient(135deg,#c62828,#8e1b1b);color:#fff;border:none;}
    .pesanan-card{border:none;border-radius:16px;box-shadow:0 2px 10px rgba(35,39,44,.06);transition:.2s;}
    .pesanan-card:hover{box-shadow:0 8px 25px rgba(35,39,44,.12);}
</style>
@endpush

@section('content')
<div class="mb-4">
    <h3 class="fw-bold mb-1">Pesanan Saya</h3>
    <p class="text-muted mb-0">Kelola semua pesanan kamu di sini</p>
</div>

@if(session('success'))
    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i>{{ session('error') }}</div>
@endif

@forelse($pesanans as $p)
<div class="card pesanan-card p-3 mb-3">
    <div class="d-flex gap-3">
        @if($p->produk->gambar)
            <img src="{{ Storage::url($p->produk->gambar) }}" style="width:90px;height:90px;object-fit:cover;border-radius:12px" alt="">
        @else
            <div class="d-flex align-items-center justify-content-center" style="width:90px;height:90px;border-radius:12px;background:#eee">
                <i class="bi bi-image text-muted"></i>
            </div>
        @endif

        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="fw-bold mb-1">{{ $p->produk->nama_produk }}</h6>
                    <p class="small text-muted mb-1">Pesanan #{{ $p->id_pesanan }} · {{ $p->jumlah }} pcs · {{ $p->created_at->format('d M Y') }}</p>
                    <p class="harga mb-1">Rp {{ number_format($p->harga + ($p->ongkir ?? 0), 0, ',', '.') }}</p>
                </div>
                <div class="text-end">
                    <span class="badge badge-soft">{{ str_replace('_', ' ', $p->status_pesanan) }}</span>
                    @php $sb = $p->pembayaran?->status_bayar ?? 'pending'; @endphp
                    <div class="small mt-1">
                        @if($sb === 'sukses') <span class="text-success">Lunas</span>
                        @elseif($sb === 'gagal') <span class="text-danger">Gagal</span>
                        @elseif($sb === 'refund') <span class="text-info">Refund GraPay</span>
                        @else <span class="text-warning">Belum bayar</span> @endif
                    </div>
                </div>
            </div>

            @if($p->alasan_batal)
            <div class="alert alert-light border small py-2 mt-2 mb-0">
                <i class="bi bi-chat-left-text me-1"></i><strong>Alasan batal:</strong> {{ $p->alasan_batal }}
            </div>
            @endif

            <div class="d-flex gap-2 mt-2 flex-wrap">
                @if($sb === 'pending' && $p->status_pesanan === 'menunggu_pembayaran')
                <a href="{{ route('pesanan.bayar', $p->id_pesanan) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-credit-card me-1"></i>Bayar
                </a>
                @endif

                @if(in_array($p->status_pesanan, ['menunggu_pembayaran', 'diproses']))
                <button type="button" class="btn btn-sm btn-outline-danger"
                        data-bs-toggle="modal" data-bs-target="#cancelModal"
                        data-url="{{ route('pesanan.cancel', $p->id_pesanan) }}"
                        data-id="{{ $p->id_pesanan }}"
                        data-paid="{{ $sb === 'sukses' ? 1 : 0 }}"
                        data-total="{{ $p->harga + ($p->ongkir ?? 0) }}">
                    <i class="bi bi-x-circle me-1"></i>Batalkan
                </button>
                @endif

                <a href="{{ route('pesanan.track', $p->id_pesanan) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-geo-alt me-1"></i>Tracking
                </a>

                <a href="{{ route('pesanan.invoice', $p->id_pesanan) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-receipt me-1"></i>Struk
                </a>

                @if($p->status_pesanan === 'sampai')
                <a href="{{ route('retur.create', ['pesanan' => $p->id_pesanan]) }}" class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-arrow-return-left me-1"></i>Retur
                </a>
                @endif

                @if($p->status_pesanan === 'dibatalkan')
                <form method="POST" action="{{ route('pesanan.destroy', $p->id_pesanan) }}"
                      onsubmit="return confirm('Hapus pesanan ini? Data akan hilang permanen.')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Hapus</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@empty
<div class="card">
    <div class="card-body text-center text-muted py-5">
        <i class="bi bi-bag-x fs-1 d-block mb-2"></i>
        Belum ada pesanan. <a href="{{ route('produk.index') }}">Yuk belanja dulu</a>
    </div>
</div>
@endforelse

{{-- ===== MODAL BATALKAN (wajib alasan) ===== --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:none;border-radius:20px;overflow:hidden">
            <div class="modal-header grapay">
                <h6 class="modal-title fw-bold"><i class="bi bi-x-octagon me-2"></i>Batalkan Pesanan #<span id="cancelId"></span></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="cancelForm">
                @csrf
                <div class="modal-body p-4">
                    <div id="refundInfo" class="alert alert-success small py-2" style="display:none">
                        <i class="bi bi-wallet2 me-1"></i>Dana yang sudah dibayar akan dikembalikan ke <strong>GraPay</strong> Anda sebesar <strong id="refundAmount"></strong>.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Alasan Pembatalan <span class="text-danger">*</span></label>
                        <textarea name="alasan" class="form-control" rows="3" required
                                  placeholder="Contoh: Saya berubah pikiran / salah pilih produk / ingin ganti alamat..."></textarea>
                        <div class="form-text small">Alasan wajib diisi dan akan ditinjau admin.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Kembali</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle me-1"></i>Ya, Batalkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('[data-bs-target="#cancelModal"]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('cancelForm').action = this.dataset.url;
        document.getElementById('cancelId').textContent = this.dataset.id;
        const refund = document.getElementById('refundInfo');
        if (this.dataset.paid == '1') {
            refund.style.display = 'block';
            document.getElementById('refundAmount').textContent = 'Rp ' + Number(this.dataset.total).toLocaleString('id-ID');
        } else {
            refund.style.display = 'none';
        }
    });
});
</script>
@endsection