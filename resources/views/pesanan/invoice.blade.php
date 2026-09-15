<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Struk Pesanan #{{ str_pad($pesanan->id_pesanan, 5, '0', STR_PAD_LEFT) }} - Graniva</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root{
        --primary:#b45a3c; --primary-dark:#96482e; --gold:#c9a227; --gold-soft:#e6c968;
        --ink:#23272c; --muted:#8a8f96; --cream:#f7f3ee; --line:#e8e0d6;
    }
    *{box-sizing:border-box}
    body{background:#efe9e1;font-family:'Inter',sans-serif;color:var(--ink);padding:40px 16px}

    /* ===== TOOLBAR (tidak ikut tercetak) ===== */
    .inv-toolbar{max-width:860px;margin:0 auto 18px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap}
    .inv-toolbar .btn-tool{border:none;border-radius:12px;padding:10px 18px;font-weight:700;font-size:.85rem;display:inline-flex;align-items:center;gap:8px;transition:.2s;cursor:pointer}
    .btn-back{background:#fff;color:var(--ink);box-shadow:0 4px 14px rgba(35,39,44,.08)}
    .btn-back:hover{transform:translateY(-1px)}
    .btn-print{background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:#fff;box-shadow:0 8px 20px rgba(180,90,60,.35)}
    .btn-print:hover{transform:translateY(-1px);box-shadow:0 12px 26px rgba(180,90,60,.45)}

    /* ===== LEMBAR STRUK ===== */
    .sheet{
        max-width:860px;margin:0 auto;background:#fff;position:relative;overflow:hidden;
        border-radius:6px;box-shadow:0 30px 80px rgba(35,39,44,.18);
        border-top:6px solid var(--gold);
    }
    .sheet::before{ /* watermark logo */
        content:"G";position:absolute;right:-40px;bottom:-90px;
        font-family:'Playfair Display',serif;font-size:340px;font-weight:800;
        color:rgba(180,90,60,.05);line-height:1;pointer-events:none;
    }
    .sheet-inner{padding:48px 52px}

    /* Header */
    .inv-head{display:flex;justify-content:space-between;gap:24px;flex-wrap:wrap}
    .brand-box{display:flex;gap:14px;align-items:center}
    .brand-box img{width:58px;height:58px;border-radius:14px;object-fit:cover;box-shadow:0 6px 18px rgba(180,90,60,.25)}
    .brand-name{font-family:'Playfair Display',serif;font-weight:800;font-size:1.7rem;letter-spacing:.5px}
    .brand-name span{color:var(--primary)}
    .brand-tag{font-size:.72rem;letter-spacing:2.5px;text-transform:uppercase;color:var(--muted);font-weight:600}
    .inv-title-box{text-align:right}
    .inv-title{font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;letter-spacing:4px;color:var(--ink)}
    .inv-no{font-size:.9rem;font-weight:700;color:var(--primary);letter-spacing:1px}
    .inv-date{font-size:.78rem;color:var(--muted)}
    .gold-rule{height:3px;background:linear-gradient(90deg,var(--gold),var(--gold-soft),transparent);margin:26px 0 30px;border-radius:3px}

    /* Info grid */
    .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:28px;margin-bottom:34px}
    .info-card{background:var(--cream);border:1px solid var(--line);border-radius:14px;padding:18px 20px}
    .info-label{font-size:.68rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:var(--primary);margin-bottom:10px;display:flex;align-items:center;gap:7px}
    .info-label i{font-size:.85rem}
    .info-row{font-size:.85rem;margin-bottom:4px;color:#495057}
    .info-row strong{color:var(--ink);font-weight:700}
    .status-pill{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:5px 14px;font-size:.72rem;font-weight:800;letter-spacing:.5px;text-transform:uppercase}

    /* Tabel item */
    .item-table{width:100%;border-collapse:collapse;margin-bottom:26px}
    .item-table thead th{
        background:var(--ink);color:#fff;font-size:.7rem;font-weight:700;
        letter-spacing:1.5px;text-transform:uppercase;padding:12px 14px;text-align:left;
    }
    .item-table thead th:first-child{border-radius:10px 0 0 10px}
    .item-table thead th:last-child{border-radius:0 10px 10px 0;text-align:right}
    .item-table tbody td{padding:16px 14px;border-bottom:1px solid var(--line);font-size:.88rem;vertical-align:middle}
    .item-table tbody td:last-child{text-align:right;font-weight:700}
    .prod-cell{display:flex;align-items:center;gap:12px}
    .prod-cell img{width:52px;height:52px;border-radius:10px;object-fit:cover;border:1px solid var(--line)}
    .prod-name{font-weight:700}
    .prod-cat{font-size:.72rem;color:var(--muted)}

    /* Total */
    .total-wrap{display:flex;justify-content:flex-end}
    .total-box{width:320px}
    .total-row{display:flex;justify-content:space-between;font-size:.86rem;padding:7px 0;color:#495057}
    .total-row.grand{
        border-top:2px dashed var(--line);margin-top:8px;padding-top:12px;
        font-size:1.05rem;font-weight:800;color:var(--ink);
    }
    .total-row.grand .amount{color:var(--primary);font-family:'Playfair Display',serif;font-size:1.3rem}

    /* Stempel */
    .stamp{
        position:absolute;right:60px;top:330px;transform:rotate(-14deg);
        border:3px double var(--gold);color:var(--gold);border-radius:10px;
        padding:10px 22px;font-family:'Playfair Display',serif;font-weight:800;
        font-size:1.15rem;letter-spacing:4px;text-transform:uppercase;opacity:.85;
        background:rgba(255,255,255,.6);
    }

    /* Footer */
    .inv-foot{margin-top:44px;display:grid;grid-template-columns:1fr 1fr;gap:28px}
    .sign-box{text-align:center;font-size:.78rem;color:var(--muted)}
    .sign-space{height:70px;border-bottom:1.5px solid var(--ink);margin:0 30px 8px}
    .sign-name{font-weight:700;color:var(--ink)}
    .thanks{
        margin-top:36px;text-align:center;font-size:.8rem;color:var(--muted);
        border-top:1px solid var(--line);padding-top:20px;
    }
    .thanks .brand-line{font-family:'Playfair Display',serif;font-size:1rem;color:var(--ink);font-weight:700;margin-bottom:4px}
    .thanks .brand-line span{color:var(--primary)}

    @media (max-width:768px){
        .sheet-inner{padding:30px 22px}
        .info-grid,.inv-foot{grid-template-columns:1fr}
        .inv-title-box{text-align:left}
        .stamp{right:20px;top:auto;bottom:220px}
    }

    /* ===== PRINT ===== */
    @@media print{
        body{background:#fff;padding:0}
        .inv-toolbar{display:none}
        .sheet{box-shadow:none;border-radius:0;max-width:100%}
        .sheet-inner{padding:24px 30px}
    }
</style>
</head>
<body>

@php
    $produk     = $pesanan->produk;
    $pembayaran = $pesanan->pembayaran;
    $user       = $pesanan->user;
    $subtotal   = ($pesanan->harga * $pesanan->jumlah);
    $total      = $subtotal + ($pesanan->ongkir ?? 0);
    $noInvoice  = 'GRV-' . date('Y') . '-' . str_pad($pesanan->id_pesanan, 5, '0', STR_PAD_LEFT);
    $metode     = $pembayaran->metode_pembayaran ?? $pembayaran->metode ?? 'GraPay';
    $status     = $pesanan->status_pesanan ?? '-';
    $statusNice = ucfirst(str_replace('_', ' ', $status));

    $pillClass = match(true) {
        in_array($status, ['sampai', 'selesai'])           => 'background:#e8f5e9;color:#1b5e20',
        in_array($status, ['dikirim', 'diproses'])         => 'background:#e3f2fd;color:#0d47a1',
        in_array($status, ['diretur'])                     => 'background:#fff3e0;color:#e65100',
        in_array($status, ['dibatalkan', 'kendala'])       => 'background:#ffebee;color:#b71c1c',
        default                                            => 'background:#fff8e1;color:#8d6e00',
    };
@endphp

{{-- ===== TOOLBAR ===== --}}
<div class="inv-toolbar">
    <a href="{{ route('pesanan.index') }}" class="btn-tool btn-back" style="text-decoration:none">
        <i class="bi bi-arrow-left"></i>Kembali ke Pesanan
    </a>
    <button onclick="window.print()" class="btn-tool btn-print">
        <i class="bi bi-printer"></i>Cetak / Simpan PDF
    </button>
</div>

{{-- ===== LEMBAR STRUK ===== --}}
<div class="sheet">
    <div class="sheet-inner">

        {{-- Header --}}
        <div class="inv-head">
            <div class="brand-box">
                <img src="{{ asset('images/logo.jpeg') }}" onerror="this.style.display='none'" alt="Graniva">
                <div>
                    <div class="brand-name">Grani<span>va</span></div>
                    <div class="brand-tag">Ceramic & Granite Premium</div>
                </div>
            </div>
            <div class="inv-title-box">
                <div class="inv-title">INVOICE</div>
                <div class="inv-no">{{ $noInvoice }}</div>
                <div class="inv-date">Tanggal: {{ $pesanan->created_at?->format('d F Y, H:i') }} WIB</div>
            </div>
        </div>

        <div class="gold-rule"></div>

        {{-- Info pelanggan & pengiriman --}}
        <div class="info-grid">
            <div class="info-card">
                <div class="info-label"><i class="bi bi-person-vcard"></i>Tagihan Kepada</div>
                <div class="info-row"><strong>{{ $user->nama ?? '-' }}</strong></div>
                <div class="info-row">{{ $user->email ?? '-' }}</div>
                <div class="info-row">{{ $user->no_hp ?? '-' }}</div>
                <div class="info-row mt-2" style="line-height:1.5">{{ $pesanan->alamat_pengiriman ?? '-' }}</div>
            </div>
            <div class="info-card">
                <div class="info-label"><i class="bi bi-truck"></i>Detail Pengiriman</div>
                <div class="info-row">Kurir: <strong>{{ $pesanan->kurir ?? 'Tim Graniva' }}</strong></div>
                <div class="info-row">No. Resi: <strong>{{ $pesanan->no_resi ?: 'Belum ada' }}</strong></div>
                <div class="info-row">Layanan: <strong>{{ $pesanan->kategori ?? 'Standar' }}</strong></div>
                <div class="info-row">Estimasi: <strong>{{ $pesanan->estimasi_datang ? \Carbon\Carbon::parse($pesanan->estimasi_datang)->format('d F Y') : 'Menunggu konfirmasi' }}</strong></div>
                <div class="info-row mt-2">Status: <span class="status-pill" style="{{ $pillClass }}">{{ $statusNice }}</span></div>
            </div>
        </div>

        {{-- Tabel item --}}
        <table class="item-table">
            <thead>
                <tr>
                    <th style="width:55%">Produk</th>
                    <th>Harga Satuan</th>
                    <th style="text-align:center">Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="prod-cell">
                            @if($produk && $produk->gambar)
                                <img src="{{ Storage::url($produk->gambar) }}" onerror="this.style.display='none'" alt="{{ $produk->nama_produk }}">
                            @endif
                            <div>
                                <div class="prod-name">{{ $produk->nama_produk ?? 'Produk' }}</div>
                                <div class="prod-cat">Keramik / Granit Premium · SKU-{{ str_pad($produk->id_produk ?? 0, 4, '0', STR_PAD_LEFT) }}</div>
                            </div>
                        </div>
                    </td>
                    <td>Rp {{ number_format($pesanan->harga, 0, ',', '.') }}</td>
                    <td style="text-align:center">{{ $pesanan->jumlah }} pcs</td>
                    <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Total --}}
        <div class="total-wrap">
            <div class="total-box">
                <div class="total-row"><span>Subtotal</span><span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div>
                <div class="total-row"><span>Ongkos Kirim</span><span>Rp {{ number_format($pesanan->ongkir ?? 0, 0, ',', '.') }}</span></div>
                <div class="total-row"><span>Metode Pembayaran</span><span style="font-weight:700">{{ ucfirst($metode) }}</span></div>
                <div class="total-row grand"><span>TOTAL BAYAR</span><span class="amount">Rp {{ number_format($total, 0, ',', '.') }}</span></div>
            </div>
        </div>

        {{-- Stempel --}}
        @if(in_array($status, ['diproses', 'dikirim', 'sampai', 'selesai']))
            <div class="stamp">Terverifikasi</div>
        @endif

        {{-- Tanda tangan --}}
        <div class="inv-foot">
            <div class="sign-box">
                <div class="sign-space"></div>
                <div class="sign-name">{{ $user->nama ?? 'Penerima' }}</div>
                <div>Pelanggan</div>
            </div>
            <div class="sign-box">
                <div class="sign-space"></div>
                <div class="sign-name">Admin Graniva</div>
                <div>Hormat kami,</div>
            </div>
        </div>

        {{-- Terima kasih --}}
        <div class="thanks">
            <div class="brand-line">Grani<span>va</span> — Hadirkan keindahan di setiap sudut rumah.</div>
            Terima kasih telah berbelanja di Graniva. Simpan struk ini sebagai bukti pembayaran yang sah.<br>
            Jl. Raya Keramik No. 123, Indonesia · cs@graniva.com · 0812-3456-7890
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>