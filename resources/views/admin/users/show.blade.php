@extends('layouts.admin')
@section('title', 'Detail User - Admin Graniva')
@section('page-title', 'Detail User')

@section('content')
<a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>

<div class="row g-4">
    {{-- ===== KOLOM KIRI: PROFIL ===== --}}
    <div class="col-lg-4">
        {{-- Info User --}}
        <div class="card p-4 mb-3 text-center">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto mb-3"
                 style="width:80px;height:80px;background:var(--primary);color:#fff;font-size:2rem;font-weight:800">
                {{ strtoupper(substr($user->nama, 0, 1)) }}
            </div>
            <h5 class="fw-bold mb-1">{{ $user->nama }}</h5>
            <p class="small text-muted mb-2">{{ $user->email }}</p>
            @if($user->is_blocked)
                <span class="badge bg-danger">Diblokir</span>
            @else
                <span class="badge bg-success">Aktif</span>
            @endif

            <div class="text-start mt-3">
                <div class="d-flex justify-content-between border-bottom py-2 small">
                    <span class="text-muted">No. HP</span><strong>{{ $user->no_hp ?? '-' }}</strong>
                </div>
                <div class="d-flex justify-content-between border-bottom py-2 small">
                    <span class="text-muted">Saldo</span><strong class="harga">Rp {{ number_format($user->saldo ?? 0, 0, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-content-between border-bottom py-2 small">
                    <span class="text-muted">Terdaftar</span><strong>{{ $user->created_at->format('d M Y') }}</strong>
                </div>
                <div class="py-2 small">
                    <span class="text-muted d-block mb-1">Alamat</span>
                    <strong>{{ $user->alamat ?? '-' }}</strong>
                </div>
            </div>
        </div>

        {{-- Edit Profil --}}
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-pencil me-1"></i>Edit Profil</h6>
            <form method="POST" action="{{ route('admin.users.update', $user->id_user) }}">
                @csrf @method('PUT')
                <div class="mb-2">
                    <label class="form-label small">Nama</label>
                    <input type="text" name="nama" class="form-control" value="{{ $user->nama }}" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                </div>
                <div class="mb-2">
                    <label class="form-label small">No. HP</label>
                    <input type="text" name="no_hp" class="form-control" value="{{ $user->no_hp }}">
                </div>
                <div class="mb-3">
                    <label class="form-label small">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2">{{ $user->alamat }}</textarea>
                </div>
                <button class="btn btn-primary btn-sm w-100"><i class="bi bi-save me-1"></i>Simpan</button>
            </form>
        </div>

        {{-- Reset Password --}}
        <div class="card p-4 mb-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-key me-1"></i>Reset Password</h6>
            <form method="POST" action="{{ route('admin.users.reset', $user->id_user) }}">
                @csrf
                <div class="mb-2">
                    <label class="form-label small">Password Baru</label>
                    <input type="text" name="password" class="form-control" placeholder="Min. 6 karakter" required>
                </div>
                <button class="btn btn-warning btn-sm w-100"><i class="bi bi-arrow-repeat me-1"></i>Reset Password</button>
            </form>
        </div>

        {{-- Blokir / Hapus --}}
        <div class="card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-shield-lock me-1"></i>Aksi Akun</h6>
            <div class="d-flex gap-2">
                <form method="POST" action="{{ route('admin.users.block', $user->id_user) }}" class="flex-fill">
                    @csrf
                    <button class="btn btn-sm w-100 {{ $user->is_blocked ? 'btn-success' : 'btn-outline-warning' }}">
                        <i class="bi bi-{{ $user->is_blocked ? 'unlock' : 'slash-circle' }} me-1"></i>
                        {{ $user->is_blocked ? 'Buka Blokir' : 'Blokir' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.users.destroy', $user->id_user) }}" class="flex-fill"
                      onsubmit="return confirm('Yakin hapus akun {{ $user->nama }}? Semua datanya hilang permanen!')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger w-100"><i class="bi bi-trash me-1"></i>Hapus</button>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== KOLOM KANAN: RIWAYAT PESANAN ===== --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-bag-check me-1"></i>Riwayat Pesanan ({{ $pesanans->count() }})</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Produk</th>
                                <th>Total</th>
                                <th>Pembayaran</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pesanans as $p)
                            <tr>
                                <td class="small">#{{ $p->id_pesanan }}</td>
                                <td class="small">{{ $p->produk->nama_produk ?? '-' }} ×{{ $p->jumlah }}</td>
                                <td class="harga small">Rp {{ number_format($p->harga + ($p->ongkir ?? 0), 0, ',', '.') }}</td>
                                <td>
                                    @php $sb = $p->pembayaran->status_bayar ?? 'pending'; @endphp
                                    @if($sb == 'sukses') <span class="badge bg-success">Lunas</span>
                                    @elseif($sb == 'gagal') <span class="badge bg-danger">Gagal</span>
                                    @elseif($sb == 'refund') <span class="badge bg-info">Refund</span>
                                    @else <span class="badge bg-warning text-dark">Pending</span> @endif
                                </td>
                                <td><span class="badge badge-soft">{{ ucfirst(str_replace('_', ' ', $p->status_pesanan)) }}</span></td>
                                <td class="small text-muted">{{ $p->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection