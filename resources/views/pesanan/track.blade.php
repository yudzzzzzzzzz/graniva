@extends('layouts.app')
@section('title', 'Tracking Pesanan - Graniva')

@push('styles')
<style>
    .shipping-hero{border:none;border-radius:20px;overflow:hidden;box-shadow:0 8px 30px rgba(35,39,44,.12);}
    .shipping-hero .video-box{padding:28px;display:flex;align-items:center;justify-content:center;}
    .shipping-hero video{width:100%;max-width:260px;border-radius:18px;box-shadow:0 6px 24px rgba(0,0,0,.15);}
    .badge-shipping{display:inline-flex;align-items:center;gap:.4rem;color:#fff;font-weight:700;font-size:.8rem;padding:.5rem 1.1rem;border-radius:50px;letter-spacing:.3px;}
    .info-tile{background:#fff;border:1px solid #f0e7e0;border-radius:14px;padding:14px 16px;height:100%;}
    .info-tile .label{display:block;font-size:.72rem;text-transform:uppercase;letter-spacing:.5px;color:#9a928a;margin-bottom:4px;}
    .info-tile .value{display:block;font-weight:700;color:var(--dark);font-size:.95rem;}
    .pulse-dot{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:6px;animation:pulse 1.6s infinite;}
    @keyframes pulse{0%{box-shadow:0 0 0 0 rgba(255,255,255,.5);}70%{box-shadow:0 0 0 9px rgba(255,255,255,0);}100%{box-shadow:0 0 0 0 rgba(255,255,255,0);}}
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <a href="{{ route('pesanan.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Pesanan
        </a>

        {{-- ===== HERO STATUS (video ganti sesuai status) ===== --}}
        @php
            $status = $pesanan->status_pesanan;
            $hero = null;

            if (in_array($status, ['menunggu_pembayaran', 'diproses'])) {
                $hero = [
                    'video'   => 'videos/menunggu.mp4',
                    'badge'   => $status == 'menunggu_pembayaran' ? 'MENUNGU VERIFIKASI' : 'SEDANG DIPROSES',
                    'title'   => $status == 'menunggu_pembayaran' ? 'Pembayaran Sedang Diproses' : 'Pesanan Sedang Dikemas',
                    'desc'    => $status == 'menunggu_pembayaran'
                                 ? 'Admin sedang memverifikasi pembayaran Anda. Mohon tunggu sebentar lagi.'
                                 : 'Pembayaran diterima! Pesanan Anda sedang dikemas oleh tim kami.',
                    'grad'    => 'linear-gradient(135deg,#ffffff 0%,#fdf6e3 100%)',
                    'color'   => '#f59e0b',
                    'tiles'   => [
                        ['Metode Bayar', 'bi-credit-card', $pesanan->pembayaran->metode ?? '-'],
                        ['Tanggal Bayar', 'bi-calendar-event', $pesanan->pembayaran->tanggal_bayar ?? '-'],
                        ['Total', 'bi-cash-stack', 'Rp '.number_format($pesanan->harga + ($pesanan->ongkir ?? 0), 0, ',', '.')],
                    ],
                ];
            } elseif (in_array($status, ['dikirim', 'dalam_pengiriman'])) {
                $hero = [
                    'video'   => 'videos/truk.mp4',
                    'badge'   => 'SEDANG DIKIRIM',
                    'title'   => 'Pesanan Dalam Perjalanan',
                    'desc'    => 'Kurir sedang mengantar pesanan #'.$pesanan->id_pesanan.' menuju alamat Anda.',
                    'grad'    => 'linear-gradient(135deg,#ffffff 0%,#fdf3ec 100%)',
                    'color'   => '#b45a3c',
                    'tiles'   => [
                        ['Estimasi Sampai', 'bi-calendar-check', $pesanan->estimasi_datang ? \Carbon\Carbon::parse($pesanan->estimasi_datang)->format('d M Y') : '-'],
                        ['Kurir', 'bi-truck', $pesanan->kurir ?? '-'],
                        ['No. Resi', 'bi-upc-scan', $pesanan->no_resi ?? '-'],
                    ],
                ];
            } elseif ($status == 'sampai') {
                $hero = [
                    'video'   => 'videos/sampai.mp4',
                    'badge'   => 'SAMPAI TUJUAN',
                    'title'   => 'Pesanan Telah Sampai',
                    'desc'    => 'Pesanan #'.$pesanan->id_pesanan.' sudah diterima. Terima kasih telah berbelanja di Graniva!',
                    'grad'    => 'linear-gradient(135deg,#ffffff 0%,#eaf7ee 100%)',
                    'color'   => '#28a745',
                    'tiles'   => [
                        ['Kurir', 'bi-truck', $pesanan->kurir ?? '-'],
                        ['No. Resi', 'bi-upc-scan', $pesanan->no_resi ?? '-'],
                        ['Status', 'bi-house-check', 'Selesai'],
                    ],
                ];
            }
        @endphp

        @if($hero)
        <div class="card shipping-hero mb-4" style="background:{{ $hero['grad'] }}">
            <div class="row g-0 align-items-center">
                <div class="col-md-5">
                    <div class="video-box">
                        <video autoplay loop muted playsinline>
                            <source src="{{ asset($hero['video']) }}" type="video/mp4">
                        </video>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="p-4 ps-md-2">
                        <span class="badge-shipping" style="background:{{ $hero['color'] }}">
                            <span class="pulse-dot" style="background:#fff"></span>{{ $hero['badge'] }}
                        </span>
                        <h4 class="fw-bold mt-3 mb-1">{{ $hero['title'] }}</h4>
                        <p class="text-muted small mb-4">{{ $hero['desc'] }}</p>
                        <div class="row g-2">
                            @foreach($hero['tiles'] as $tile)
                            <div class="col-sm-6 {{ $loop->last && count($hero['tiles']) % 2 == 1 ? 'col-sm-12' : '' }}">
                                <div class="info-tile">
                                    <span class="label">{{ $tile[0] }}</span>
                                    <span class="value"><i class="bi {{ $tile[1] }} me-1"></i>{{ $tile[2] }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ===== CARD UTAMA ===== --}}
        <div class="card p-4">
            <h5 class="fw-bold mb-1">Detail Pesanan</h5>
            <p class="small text-muted mb-4">#{{ $pesanan->id_pesanan }} · {{ $pesanan->created_at->format('d M Y') }}</p>

            {{-- Produk --}}
            <div class="border rounded-3 p-3 mb-4" style="background:#faf7f4">
                <div class="d-flex gap-3">
                    @if($pesanan->produk->gambar)
                        <img src="{{ Storage::url($pesanan->produk->gambar) }}" style="width:70px;height:70px;object-fit:cover;border-radius:10px" alt="">
                    @else
                        <div class="d-flex align-items-center justify-content-center" style="width:70px;height:70px;border-radius:10px;background:#eee"><i class="bi bi-image text-muted"></i></div>
                    @endif
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-1">{{ $pesanan->produk->nama_produk }}</h6>
                        <p class="small text-muted mb-0">{{ $pesanan->jumlah }} pcs · Rp {{ number_format($pesanan->harga, 0, ',', '.') }}</p>
                    </div>
                    <span class="badge badge-soft align-self-start">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                </div>
            </div>

            {{-- Timeline --}}
            <h6 class="fw-bold mb-3">Status Pesanan</h6>
            @php
                $urutan = ['menunggu_pembayaran', 'diproses', 'dikirim', 'sampai'];
                $labels = [
                    'menunggu_pembayaran' => ['Menunggu Pembayaran', 'bi-wallet2'],
                    'diproses'            => ['Diproses', 'bi-box-seam'],
                    'dikirim'             => ['Dikirim', 'bi-truck'],
                    'sampai'              => ['Sampai Tujuan', 'bi-house-check'],
                ];
                $currentIndex = array_search($status, $urutan);
                if ($currentIndex === false) $currentIndex = 0;
            @endphp

            <div class="mb-4">
                @foreach($urutan as $i => $key)
                @php $aktif = $i <= $currentIndex; @endphp
                <div class="d-flex align-items-start gap-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center {{ $aktif ? 'text-white' : 'bg-light text-muted border' }}"
                             style="width:38px;height:38px;font-size:.9rem;{{ $aktif ? 'background:'.$hero['color'] : '' }}">
                            <i class="bi {{ $labels[$key][1] }}"></i>
                        </div>
                        @if($i < count($urutan) - 1)
                        <div class="{{ $i < $currentIndex ? '' : 'bg-light' }}" style="width:3px;height:28px;{{ $i < $currentIndex && $hero ? 'background:'.$hero['color'] : '' }}"></div>
                        @endif
                    </div>
                    <div class="pb-2">
                        <p class="fw-semibold mb-0 small {{ $aktif ? '' : 'text-muted' }}">{{ $labels[$key][0] }}</p>
                        @if($aktif && $i == $currentIndex)
                        <p class="small text-muted mb-0">Posisi saat ini</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Kendala --}}
            @if($pesanan->kendala_pengiriman)
            <div class="alert alert-warning small">
                <i class="bi bi-exclamation-triangle me-1"></i><strong>Kendala:</strong> {{ $pesanan->kendala_pengiriman }}
            </div>
            @endif

            {{-- Alamat --}}
            @if($pesanan->alamat_pengiriman || $pesanan->alamat)
            <div class="border-top pt-3">
                <h6 class="fw-bold mb-2"><i class="bi bi-geo-alt me-1"></i>Alamat Tujuan</h6>
                @if($pesanan->alamat_pengiriman)
                    <p class="small mb-1"><strong>{{ $pesanan->user->nama ?? '' }}</strong></p>
                    <p class="small text-muted mb-0">{{ $pesanan->alamat_pengiriman }}</p>
                @else
                    <p class="small mb-1"><strong>{{ $pesanan->alamat->penerima }}</strong> · {{ $pesanan->alamat->no_hp }}</p>
                    <p class="small text-muted mb-0">{{ $pesanan->alamat->alamat_lengkap }}, {{ $pesanan->alamat->kota }} {{ $pesanan->alamat->kode_pos }}</p>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection