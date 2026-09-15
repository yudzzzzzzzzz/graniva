@extends('layouts.admin')
@section('title', 'Notifikasi - Admin Graniva')
@section('page-title', 'Manajemen Notifikasi')

@section('content')
<div class="row g-4">
    {{-- Form Kirim Notifikasi --}}
    <div class="col-lg-5">
        <div class="card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-send me-1"></i>Kirim Notifikasi</h6>
            <form method="POST" action="{{ route('admin.notifikasi.send') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small">Target</label>
                    <select name="target" id="target" class="form-select">
                        <option value="semua">Semua User</option>
                        <option value="user">User Tertentu</option>
                    </select>
                </div>
                <div class="mb-3" id="userSelect" style="display:none">
                    <label class="form-label small">Pilih User</label>
                    <select name="id_user" class="form-select">
                        <option value="">-- Pilih User --</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id_user }}">{{ $u->nama }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Judul</label>
                    <input type="text" name="judul" class="form-control" placeholder="Judul notifikasi" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Pesan</label>
                    <textarea name="pesan" class="form-control" rows="3" placeholder="Isi notifikasi..." required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small">Tipe</label>
                    <select name="tipe" class="form-select">
                        <option value="success">Sukses</option>
                        <option value="info">Info</option>
                        <option value="error">Error</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send me-1"></i>Kirim</button>
            </form>
        </div>
    </div>

    {{-- Daftar Notifikasi --}}
    <div class="col-lg-7">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-bell me-1"></i>Daftar Notifikasi</h6>
                <form method="POST" action="{{ route('admin.notifikasi.deleteAll') }}" 
                      onsubmit="return confirm('Yakin hapus SEMUA notifikasi untuk semua user? Tindakan ini tidak bisa dibatalkan.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="bi bi-trash me-1"></i>Hapus Semua
                    </button>
                </form>
            </div>

            @if(session('success'))
                <div class="alert alert-success small">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger small">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr>
                        <th>User</th>
                        <th>Judul</th>
                        <th>Tipe</th>
                        <th>Tanggal</th>
                        <th class="text-end">Aksi</th>
                    </tr></thead>
                    <tbody>
                        @forelse($notifikasis as $n)
                        <tr>
                            <td class="small fw-semibold">{{ $n->user->nama ?? '-' }}</td>
                            <td class="small">{{ $n->judul }}</td>
                            <td>
                                @if($n->tipe === 'success') <span class="badge bg-success">Sukses</span>
                                @elseif($n->tipe === 'error') <span class="badge bg-danger">Error</span>
                                @else <span class="badge bg-info">Info</span> @endif
                            </td>
                            <td class="small text-muted">{{ $n->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <form method="POST" action="{{ route('admin.notifikasi.deleteUser', $n->id_user) }}" 
                                          onsubmit="return confirm('Hapus semua notifikasi user {{ $n->user->nama ?? 'ini' }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-warning" title="Hapus semua notifikasi user ini">
                                            <i class="bi bi-person-dash"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.notifikasi.deleteOne', $n->id_notifikasi) }}" 
                                          onsubmit="return confirm('Hapus notifikasi ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus notifikasi ini">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada notifikasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $notifikasis->links() }}</div>
        </div>
    </div>
</div>

<script>
document.getElementById('target').addEventListener('change', function() {
    const userSelect = document.getElementById('userSelect');
    userSelect.style.display = this.value === 'user' ? 'block' : 'none';
});
</script>
@endsection