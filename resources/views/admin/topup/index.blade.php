@extends('layouts.admin')
@section('title', 'Verifikasi Top Up - Admin Graniva')
@section('page-title', 'Verifikasi Top Up GraPay')

@section('content')
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">User</th>
                    <th>Nominal</th>
                    <th>Bukti</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topups as $t)
                <tr>
                    <td class="ps-4 small">{{ $t->user->nama ?? '-' }}</td>
                    <td class="harga small">Rp {{ number_format($t->nominal, 0, ',', '.') }}</td>
                    <td>
                        @if($t->bukti_bayar)
                        {{-- Klik = buka modal lightbox, BUKAN tab baru --}}
                        <img src="{{ Storage::url($t->bukti_bayar) }}"
                             style="width:50px;height:50px;object-fit:cover;border-radius:8px;cursor:pointer;border:2px solid var(--primary)"
                             data-bs-toggle="modal" data-bs-target="#buktiModal"
                             onclick="document.getElementById('buktiImg').src = this.src"
                             title="Klik untuk memperbesar">
                        @else <span class="text-muted">-</span> @endif
                    </td>
                    <td class="small text-muted">{{ $t->created_at->format('d M Y') }}</td>
                    <td>
                        @if($t->status == 'sukses') <span class="badge bg-success">Berhasil</span>
                        @elseif($t->status == 'gagal') <span class="badge bg-danger">Ditolak</span>
                        @else <span class="badge bg-warning text-dark">Menunggu</span> @endif
                    </td>
                    <td class="text-end pe-4">
                        @if($t->status == 'pending')
                        <div class="d-inline-flex gap-1">
                            <form method="POST" action="{{ route('admin.topup.process', $t->id_topup) }}">
                                @csrf<input type="hidden" name="aksi" value="terima">
                                <button class="btn btn-sm btn-success"><i class="bi bi-check"></i></button>
                            </form>
                            <form method="POST" action="{{ route('admin.topup.process', $t->id_topup) }}" onsubmit="return confirm('Tolak top up ini?')">
                                @csrf<input type="hidden" name="aksi" value="tolak">
                                <button class="btn btn-sm btn-danger"><i class="bi bi-x"></i></button>
                            </form>
                        </div>
                        @else <span class="text-muted small">—</span> @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada top up.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $topups->links() }}</div>

{{-- ===== MODAL LIGHTBOX BUKTI ===== --}}
<div class="modal fade" id="buktiModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden">
            <div class="modal-header">
                <h6 class="modal-title fw-bold"><i class="bi bi-image me-2"></i>Bukti Pembayaran</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-3" style="background:#1a1a1a">
                <img id="buktiImg" src="" style="max-width:100%;max-height:70vh;border-radius:10px" alt="Bukti Pembayaran">
            </div>
            <div class="modal-footer">
                <a id="buktiLink" href="#" target="_blank" class="btn btn-sm btn-outline-light" style="background:#333;color:#fff;border:none">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Buka di Tab Baru
                </a>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('buktiModal').addEventListener('show.bs.modal', function () {
    const img = document.getElementById('buktiImg');
    setTimeout(() => {
        document.getElementById('buktiLink').href = img.src;
    }, 100);
});
</script>
@endsection