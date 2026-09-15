@extends('layouts.admin')
@section('title', 'Pengguna - Admin Graniva')
@section('page-title', 'Manajemen Pengguna')

@section('content')
<div class="card p-3 mb-3">
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="q" class="form-control" style="max-width:280px" placeholder="Cari nama / email..." value="{{ request('q') }}">
        <button class="btn btn-primary"><i class="bi bi-search"></i></button>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">User</th>
                    <th>No. HP</th>
                    <th>Saldo</th>
                    <th>Status</th>
                    <th>Terdaftar</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td class="ps-4">
                        <strong class="small d-block">{{ $u->nama }}</strong>
                        <span class="text-muted" style="font-size:.75rem">{{ $u->email }}</span>
                    </td>
                    <td class="small">{{ $u->no_hp ?? '-' }}</td>
                    <td class="harga small">Rp {{ number_format($u->saldo ?? 0, 0, ',', '.') }}</td>
                    <td>
                        @if($u->is_blocked)
                            <span class="badge bg-danger">Diblokir</span>
                        @else
                            <span class="badge bg-success">Aktif</span>
                        @endif
                    </td>
                    <td class="small text-muted">{{ $u->created_at->format('d M Y') }}</td>
                    <td class="text-end pe-4">
                        <div class="d-inline-flex gap-1">
                            <a href="{{ route('admin.users.show', $u->id_user) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.users.block', $u->id_user) }}">
                                @csrf
                                <button class="btn btn-sm {{ $u->is_blocked ? 'btn-outline-success' : 'btn-outline-warning' }}" title="{{ $u->is_blocked ? 'Buka blokir' : 'Blokir' }}">
                                    <i class="bi bi-{{ $u->is_blocked ? 'unlock' : 'slash-circle' }}"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.destroy', $u->id_user) }}"
                                  onsubmit="return confirm('Yakin hapus akun {{ $u->nama }}? Semua data (pesanan, ulasan, saldo) akan hilang permanen!')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Hapus akun">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">User tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $users->links() }}</div>
@endsection