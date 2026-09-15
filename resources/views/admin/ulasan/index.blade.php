@extends('layouts.admin')
@section('title', 'Kelola Rating & Ulasan - Admin Graniva')
@section('page-title', 'Kelola Rating & Ulasan')

@push('styles')
<style>
    :root{--gold:#c9a227;--ink:#23272c;--muted:#8a8f96;--cream:#faf7f2;}

    /* ===== RINGKASAN ===== */
    .sum-card{
        background:#fff;border:none;border-radius:18px;padding:20px 26px;margin-bottom:22px;
        box-shadow:0 6px 22px rgba(35,39,44,.07);display:flex;align-items:center;gap:26px;flex-wrap:wrap;
        border-left:5px solid var(--gold);
    }
    .sum-card .big{font-family:'Playfair Display',serif;font-weight:800;font-size:2.2rem;color:var(--ink);line-height:1}
    .sum-card .stars i{color:#f5b301;font-size:1.05rem}
    .sum-card .stars i.off{color:#e2ddd4}
    .sum-card .meta{font-size:.8rem;color:var(--muted);font-weight:600}

    /* ===== KARTU ULASAN ===== */
    .rev-card{
        background:#fff;border:none;border-radius:18px;margin-bottom:18px;overflow:hidden;
        box-shadow:0 6px 22px rgba(35,39,44,.07);
    }
    .rev-head{
        padding:18px 24px;display:flex;justify-content:space-between;align-items:flex-start;gap:14px;
        border-bottom:1px solid #f0ebe3;background:linear-gradient(180deg,#fffdf9,#fff);
    }
    .rev-prod{font-weight:800;font-size:1rem;color:var(--ink)}
    .rev-meta{font-size:.78rem;color:var(--muted);margin-top:3px}
    .rev-meta strong{color:#b45a3c}
    .rev-stars i{color:#f5b301;font-size:.95rem}
    .rev-stars i.off{color:#e2ddd4}
    .rate-badge{display:inline-flex;align-items:center;border-radius:8px;padding:3px 10px;font-size:.72rem;font-weight:800;background:#e8f5e9;color:#1b5e20;margin-left:8px}

    .rev-body{padding:20px 24px}
    .rev-comment{
        border-left:3px solid var(--gold);padding:10px 16px;background:var(--cream);
        border-radius:0 12px 12px 0;font-size:.9rem;color:#495057;margin-bottom:16px;
    }

    /* ===== FOTO: thumbnail proporsional ===== */
    .rev-photos{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px}
    .rev-photos img{
        width:150px;height:110px;object-fit:cover;border-radius:12px;cursor:zoom-in;
        border:1px solid #eee7dc;box-shadow:0 4px 12px rgba(35,39,44,.10);transition:.2s;
    }
    .rev-photos img:hover{transform:scale(1.04);box-shadow:0 10px 22px rgba(35,39,44,.18)}

    /* ===== BALASAN ADMIN ===== */
    .admin-reply{
        background:#f4f8f4;border:1px solid #dcead c;border:1px solid #dceadc;border-radius:12px;
        padding:12px 16px;font-size:.85rem;color:#3c5a3c;margin-bottom:14px;
    }
    .admin-reply .who{font-weight:800;font-size:.72rem;letter-spacing:1px;text-transform:uppercase;color:#2e7d32;margin-bottom:4px;display:flex;align-items:center;gap:6px}

    /* ===== FORM BALAS ===== */
    .reply-form{display:flex;gap:10px}
    .reply-form input{
        flex:1;border:1.5px solid #e5ded5;border-radius:12px;padding:10px 16px;font-size:.86rem;background:#faf8f5;
    }
    .reply-form input:focus{outline:none;border-color:#b45a3c;background:#fff;box-shadow:0 0 0 3px rgba(180,90,60,.12)}
    .btn-reply{
        border:none;border-radius:12px;padding:10px 20px;font-weight:700;font-size:.84rem;color:#fff;
        background:linear-gradient(135deg,#b45a3c,#96482e);box-shadow:0 6px 16px rgba(180,90,60,.3);
        display:inline-flex;align-items:center;gap:7px;cursor:pointer;transition:.2s;
    }
    .btn-reply:hover{transform:translateY(-1px)}
    .btn-del{
        border:1.5px solid #e5b8b8;background:#fff;color:#c0392b;border-radius:10px;padding:7px 14px;
        font-size:.78rem;font-weight:700;display:inline-flex;align-items:center;gap:6px;cursor:pointer;transition:.2s;
    }
    .btn-del:hover{background:#ffebee;border-color:#c0392b}
</style>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
@endpush

@section('content')
@php
    $list = $ulasans ?? $reviews ?? $ulasan ?? collect();
    $avg  = $list->count() ? round($list->avg('rating'), 1) : 0;
@endphp

{{-- ===== RINGKASAN RATING ===== --}}
<div class="sum-card">
    <div>
        <div class="big">{{ number_format($avg, 1) }}</div>
        <div class="meta">RATING RATA-RATA</div>
    </div>
    <div class="stars">
        @for($i = 1; $i <= 5; $i++)
            <i class="bi bi-star-fill {{ $i <= round($avg) ? '' : 'off' }}"></i>
        @endfor
    </div>
    <div class="meta ms-auto"><i class="bi bi-chat-square-text me-1"></i>{{ $list->count() }} ulasan masuk</div>
</div>

{{-- ===== DAFTAR ULASAN ===== --}}
@forelse($list as $u)
<div class="rev-card">
    <div class="rev-head">
        <div>
            <div class="rev-prod">{{ $u->produk->nama_produk ?? 'Produk' }}</div>
            <div class="rev-meta">Oleh: <strong>{{ $u->user->nama ?? '-' }}</strong> · {{ $u->created_at?->format('d M Y, H:i') }}</div>
            <div class="rev-stars mt-2">
                @for($i = 1; $i <= 5; $i++)
                    <i class="bi bi-star-fill {{ $i <= $u->rating ? '' : 'off' }}"></i>
                @endfor
                <span class="rate-badge">{{ $u->rating }}/5</span>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.ulasan.destroy', $u->id_ulasan) }}"
              onsubmit="return confirm('Hapus ulasan ini secara permanen?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-del"><i class="bi bi-trash"></i>Hapus Ulasan</button>
        </form>
    </div>

    <div class="rev-body">
        {{-- Komentar --}}
        @if($u->komentar)
            <div class="rev-comment">{{ $u->komentar }}</div>
        @endif

        {{-- Foto: thumbnail proporsional, klik = preview --}}
        @if($u->gambar)
            <div class="rev-photos">
                <img src="{{ Storage::url($u->gambar) }}" alt="Foto ulasan"
                     onclick="lihatFoto('{{ Storage::url($u->gambar) }}')" title="Klik untuk lihat ukuran penuh">
            </div>
        @endif

        {{-- Balasan admin sebelumnya --}}
        @php $balasan = $u->balasan_admin ?? $u->balasan ?? $u->reply_admin ?? null; @endphp
        @if($balasan)
            <div class="admin-reply">
                <div class="who"><i class="bi bi-patch-check-fill"></i>Balasan Admin</div>
                {{ $balasan }}
            </div>
        @endif

        {{-- Form balas --}}
        <form method="POST" action="{{ route('admin.ulasan.reply', $u->id_ulasan) }}" class="reply-form">
            @csrf
            <input type="text" name="balasan" placeholder="Tulis balasan untuk ulasan ini..." value="{{ old('balasan') }}">
            <button type="submit" class="btn-reply"><i class="bi bi-reply-fill"></i>Balas</button>
        </form>
    </div>
</div>
@empty
<div class="rev-card">
    <div class="rev-body text-center text-muted py-5">
        <i class="bi bi-star-half d-block mb-2" style="font-size:2.2rem;color:#e2ddd4"></i>
        Belum ada ulasan masuk.
    </div>
</div>
@endforelse

{{-- ===== MODAL PREVIEW FOTO ===== --}}
<div class="modal fade" id="fotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border:none;border-radius:18px;overflow:hidden">
            <div class="modal-header" style="background:#23272c;color:#fff">
                <h6 class="modal-title fw-bold"><i class="bi bi-image me-2"></i>Foto Ulasan</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-2" style="background:#111">
                <img id="fotoModalImg" src="" class="img-fluid" alt="Foto ulasan" style="max-height:70vh;object-fit:contain">
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