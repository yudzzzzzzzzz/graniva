@extends('layouts.admin')
@section('title', 'Kelola Retur - Admin Graniva')
@section('page-title', 'Manajemen Retur')

@section('content')
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr>
                <th class="ps-4">ID</th>
                <th>User</th>
                <th>Pesanan</th>
                <th>Foto</th>
                <th>Alasan</th>
                <th>Status</th>
                <th class="text-end pe-4">Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($returs as $r)
                <tr>
                    <td class="ps-4">#{{ $r->id_retur }}</td>
                    <td>{{ $r->user->nama ?? '-' }}</td>
                    <td>{{ $r->pesanan->produk->nama_produk ?? '-' }} ×{{ $r->pesanan->jumlah ?? 0 }}</td>

                    {{-- Foto retur --}}
                    <td>
                        @if($r->foto)
                            <img src="{{ Storage::url($r->foto) }}" 
                                 style="width:55px;height:55px;object-fit:cover;border-radius:8px;cursor:pointer" 
                                 onclick="lihatFoto('{{ Storage::url($r->foto) }}')"
                                 alt="Foto retur"
                                 title="Klik untuk lihat ukuran penuh">
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>

                    <td class="small">{{ Str::limit($r->alasan, 40) }}</td>
                    <td>
                        @if($r->status === 'pending') <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($r->status === 'disetujui') <span class="badge bg-success">Disetujui</span>
                        @else <span class="badge bg-danger">Ditolak</span> @endif
                    </td>
                    <td class="text-end pe-4">
                        @if($r->status === 'pending')
                        <form method="POST" action="{{ route('admin.retur.process', $r->id_retur) }}" class="d-inline">
                            @csrf
                            {{-- ✅ UPDATE: name="action" → name="status" --}}
                            <input type="hidden" name="status" value="disetujui">
                            <button class="btn btn-sm btn-success">Setujui</button>
                        </form>
                        <form method="POST" action="{{ route('admin.retur.process', $r->id_retur) }}" class="d-inline">
                            @csrf
                            {{-- ✅ UPDATE: name="action" → name="status" --}}
                            <input type="hidden" name="status" value="ditolak">
                            <button class="btn btn-sm btn-outline-danger">Tolak</button>
                        </form>
                        @else
                        <span class="text-muted small">Sudah diproses</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada pengajuan retur.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $returs->links() }}</div>

{{-- Modal Preview Foto --}}
<div class="modal fade" id="fotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Foto Bukti Retur</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-2">
                <img id="fotoModalImg" src="" class="img-fluid rounded" alt="Foto retur">
            </div>
        </div>
    </div>
</div>

<script>
function lihatFoto(src) {
    document.getElementById('fotoModalImg').src = src;
    new bootstrap.Modal(document.getElementById('fotoModal')).show();
}
</script>
@endsection