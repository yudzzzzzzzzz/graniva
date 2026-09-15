@extends('layouts.admin')
@section('title', 'Detail User - Admin Graniva')
@section('page-title', 'Detail Pengguna')

@section('content')
<a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card p-4 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">{{ $user->nama }}</h6>
                @if($user->is_blocked)
                    <span class="badge bg-danger">Diblokir</span>
                @else
                    <span class="badge bg-success">Aktif</span>
                @endif
            </div>

            @if($user->is_blocked && $user->blocked_at)
            <div class="alert alert-danger small py-2">
                <i class="bi bi-clock me-1"></i>Diblokir pada: <strong>{{ $user->blocked_at->format('d M Y, H:i:s') }}</strong>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.users.update', $user->id_user) }}">
                @csrf @method('PUT')
                <div class="mb-2"><label class="form-label small">Nama</label><input type="text" name="nama" class="form-control" value="{{ $user->nama }}"></div>
                <div class="mb-2"><label class="form-label small">Email</label><input type="email" name="email" class="form-control" value="{{ $user->email }}"></div>
                <div class="mb-2"><label class="form-label small">No. HP</label><input type="text" name="no_hp" class="form-control" value="{{ $user->no_hp }}"></div>
                <div class="mb-3"><label class="form-label small">Alamat</label><textarea name="alamat" class="form-control">{{ $user->alamat }}</textarea></div>
                <button class="btn btn-primary btn-sm">Simpan</button>
            </form>
        </div>

        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3">Reset Password</h6>
            <form method="POST" action="{{ route('admin.users.reset', $user->id_user) }}" class="d-flex gap-2">
                @csrf
                <input type="password" name="password" class="form-control" placeholder="Password baru" required>
                <button class="btn btn-warning btn-sm">Reset</button>
            </form>
        </div>

        <div class="d-flex gap-2">
            {{-- Tombol Blokir / Buka Blokir (WAJIB form POST) --}}
            <form method="POST" action="{{ route('admin.users.block', $user->id_user) }}" 
                  onsubmit="return confirm('{{ $user->is_blocked ? 'Buka blokir' : 'Blokir' }} user ini?')">
                @csrf
                @if($user->is_blocked)
                    <button class="btn btn-sm btn-success"><i class="bi bi-unlock me-1"></i>Buka Blokir</button>
                @else
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-slash-circle me-1"></i>Blokir</button>
                @endif
            </form>

            <form method="POST" action="{{ route('admin.users.destroy', $user->id_user) }}" 
                  onsubmit="return confirm('Hapus user ini beserta semua datanya?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger"><i class="bi bi-trash me-1"></i>Hapus User</button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card p-4 mb-3">
            <div class="row text-center">
                <div class="col-6"><p class="small text-muted mb-1">Total Pesanan</p><h4 class="fw-bold">{{ $user->pesanan->count() }}</h4></div>
                <div class="col-6"><p class="small text-muted mb-1">Total Belanja</p><h4 class="fw-bold harga">Rp {{ number_format($totalBelanja, 0, ',', '.') }}</h4></div>
            </div>
        </div>

        <div class="card p-4">
            <h6 class="fw-bold mb-3">Riwayat Transaksi</h6>
            @forelse($user->pesanan as $p)
            <div class="d-flex justify-content-between align-items-center border-bottom py-2 small">
                <div>
                    <p class="mb-0 fw-semibold">#{{ $p->id_pesanan }} · {{ $p->produk->nama_produk ?? '-' }} ×{{ $p->jumlah }}</p>
                    <p class="mb-0 text-muted">{{ $p->created_at->format('d M Y') }} · {{ $p->pembayaran->status_bayar ?? 'pending' }}</p>
                </div>
                <span class="harga">Rp {{ number_format($p->harga, 0, ',', '.') }}</span>
            </div>
            @empty
            <p class="text-muted small mb-0">Belum ada transaksi.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection