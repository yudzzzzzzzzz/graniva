@extends('layouts.admin')
@section('title', 'Verifikasi Penarikan - Admin Graniva')
@section('page-title', 'Verifikasi Penarikan Saldo')

@section('content')
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr>
                <th class="ps-4">User</th><th>Nominal</th><th>Biaya</th><th>Tujuan</th><th>Status</th><th class="text-end pe-4">Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($withdraws as $w)
                <tr>
                    <td class="ps-4 small">{{ $w->user->nama ?? '-' }}</td>
                    <td class="harga small">Rp {{ number_format($w->nominal, 0, ',', '.') }}</td>
                    <td class="small text-muted">Rp {{ number_format($w->biaya_admin, 0, ',', '.') }}</td>
                    <td class="small">
                        <strong>{{ $w->metode }}</strong><br>
                        <span class="text-muted">{{ $w->no_rekening }}</span>
                        @if($w->nama_bank)<br><span class="text-muted">({{ $w->nama_bank }})</span>@endif
                    </td>
                    <td>
                        @if($w->status=='sukses') <span class="badge bg-success">Berhasil</span>
                        @elseif($w->status=='gagal') <span class="badge bg-danger">Ditolak</span>
                        @else <span class="badge bg-warning text-dark">Menunggu</span> @endif
                    </td>
                    <td class="text-end pe-4">
                        @if($w->status=='pending')
                        <div class="d-inline-flex gap-1">
                            <form method="POST" action="{{ route('admin.withdraw.process', $w->id_withdrawal) }}">
                                @csrf<input type="hidden" name="aksi" value="terima">
                                <button class="btn btn-sm btn-success"><i class="bi bi-check"></i></button>
                            </form>
                            <form method="POST" action="{{ route('admin.withdraw.process', $w->id_withdrawal) }}" onsubmit="return confirm('Tolak? Saldo dikembalikan.')">
                                @csrf<input type="hidden" name="aksi" value="tolak">
                                <button class="btn btn-sm btn-danger"><i class="bi bi-x"></i></button>
                            </form>
                        </div>
                        @else <span class="text-muted small">—</span> @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada penarikan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $withdraws->links() }}</div>
@endsection