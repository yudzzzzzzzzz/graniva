@extends('layouts.admin')
@section('title', 'Dashboard - Admin Graniva')
@section('page-title', 'Dashboard')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    .print-only{display:none;}
    @media print{
        @page{ size:A4; margin:10mm; }
        .sidebar, .topbar, .no-print, .page-title-section, .breadcrumb,
        .dash-head, .stat-card-row, .row.g-3.mb-4:not(.print-keep){display:none !important;}
        .main-content{margin-left:0 !important;padding:0 !important;}
        body{background:#fff !important;font-family:'Inter',sans-serif;}
        .card{box-shadow:none !important;border:none !important;}
        .print-only{display:block !important;}
        .print-keep{display:block !important;}

        /* Lembar laporan */
        .lap-sheet{padding:0;max-width:100%;}
        .lap-head{display:flex;justify-content:space-between;align-items:flex-start;gap:24px;flex-wrap:wrap;border-bottom:3px double #c9a227;padding-bottom:18px;margin-bottom:20px;}
        .lap-brand{display:flex;gap:14px;align-items:center;}
        .lap-brand .logo{
            width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#b45a3c,#96482e);
            display:flex;align-items:center;justify-content:center;color:#fff;
            font-family:'Playfair Display',serif;font-weight:800;font-size:1.5rem;
        }
        .lap-brand .name{font-family:'Playfair Display',serif;font-weight:800;font-size:1.5rem;color:#23272c;}
        .lap-brand .name span{color:#b45a3c;}
        .lap-brand .tag{font-size:.68rem;letter-spacing:2.5px;text-transform:uppercase;color:#8a8f96;font-weight:700;}
        .lap-brand .addr{font-size:.75rem;color:#8a8f96;margin-top:3px;line-height:1.5;}
        .lap-title-box{text-align:right;}
        .lap-title{font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:700;letter-spacing:3px;color:#23272c;margin-bottom:4px;}
        .lap-title small{display:block;font-size:.68rem;letter-spacing:2px;color:#c9a227;font-weight:800;text-transform:uppercase;margin-top:4px;}
        .lap-period{background:#faf7f2;border:1px solid #eee7dc;border-radius:10px;padding:7px 14px;display:inline-block;font-size:.75rem;font-weight:700;color:#8a4530;margin-top:8px;}
        .lap-no{font-size:.7rem;color:#8a8f96;margin-top:6px;}

        /* Tabel print */
        .lap-table{width:100%;border-collapse:collapse;font-size:.78rem;margin-top:18px;}
        .lap-table thead th{
            background:#23272c;color:#fff;font-size:.62rem;font-weight:800;
            letter-spacing:1.5px;text-transform:uppercase;padding:10px 10px;text-align:left;
        }
        .lap-table thead th:first-child{border-radius:8px 0 0 0;}
        .lap-table thead th:last-child{border-radius:0 8px 0 0;text-align:right;}
        .lap-table tbody td{padding:9px 10px;border-bottom:1px solid #f0ebe3;vertical-align:middle;color:#495057;}
        .lap-table tbody td:last-child{text-align:right;font-weight:700;color:#23272c;}
        .lap-table tbody tr:nth-child(even){background:#faf7f2;}
        .lap-table tfoot td{padding:12px 10px;border-top:3px double #c9a227;font-weight:800;font-size:.85rem;background:#faf3ec;}
        .lap-table tfoot td:last-child{text-align:right;color:#b45a3c;font-family:'Playfair Display',serif;font-size:1.05rem;}
        .lap-pill{display:inline-flex;border-radius:999px;padding:.2em .7em;font-size:.62rem;font-weight:800;letter-spacing:.4px;text-transform:uppercase;}

        /* Summary print */
        .lap-sum{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:16px;}
        .lap-sum-card{background:#faf7f2;border:1px solid #eee7dc;border-radius:10px;padding:12px 14px;position:relative;}
        .lap-sum-card::before{content:"";position:absolute;left:0;top:0;bottom:0;width:3px;background:#c9a227;border-radius:3px 0 0 3px;}
        .lap-sum-card .lbl{font-size:.62rem;font-weight:800;letter-spacing:1.2px;text-transform:uppercase;color:#8a8f96;margin-bottom:4px;}
        .lap-sum-card .num{font-family:'Playfair Display',serif;font-size:1.15rem;font-weight:800;color:#23272c;line-height:1.15;}
        .lap-sum-card .sub{font-size:.65rem;color:#8a8f96;margin-top:2px;}

        /* Tanda tangan */
        .lap-sign{display:grid;grid-template-columns:1fr 1fr;gap:50px;margin-top:40px;}
        .lap-sign-box{text-align:center;}
        .lap-sign-box .role{font-size:.68rem;font-weight:800;letter-spacing:1.8px;text-transform:uppercase;color:#8a8f96;margin-bottom:6px;}
        .lap-sign-space{height:70px;border-bottom:1.5px solid #23272c;margin:0 18px;position:relative;}
        .lap-sign-space .stamp{
            position:absolute;right:-8px;top:-16px;width:68px;height:68px;border:2.5px double #c9a227;
            border-radius:50%;display:flex;align-items:center;justify-content:center;transform:rotate(-14deg);
            font-family:'Playfair Display',serif;font-size:.5rem;font-weight:800;color:#c9a227;
            text-align:center;line-height:1.1;padding:4px;letter-spacing:1px;
        }
        .lap-sign-name{font-weight:800;color:#23272c;font-size:.88rem;margin-top:6px;}
        .lap-sign-role{font-size:.68rem;color:#8a8f96;}

        /* Catatan & footer */
        .lap-note{
            margin-top:24px;padding:12px 16px;background:#faf7f2;border-left:3px solid #c9a227;
            border-radius:0 10px 10px 0;font-size:.72rem;color:#6c5a3a;line-height:1.55;
        }
        .lap-foot{margin-top:20px;padding-top:14px;border-top:1px solid #eee7dc;text-align:center;font-size:.68rem;color:#8a8f96;}
        .lap-foot .brand{font-family:'Playfair Display',serif;color:#23272c;font-weight:700;}
        .lap-foot .brand span{color:#b45a3c;}

        .report-head{display:none !important;}
        .table-responsive{overflow:visible !important;}
    }

    .report-head{background:linear-gradient(135deg,#b45a3c,#7a3c26);border-radius:16px 16px 0 0;color:#fff;padding:1.2rem 1.5rem;}

    /* Summary preview (muncul pas print aja, hidden di layar) */
    .lap-sheet{display:none;}
</style>
@endpush

@section('content')
{{-- ===== HEADER PRINT (muncul cuma pas print) - UPGRADED ===== --}}
<div class="print-only lap-sheet">
    {{-- HEADER --}}
    <div class="lap-head">
        <div class="lap-brand">
            <div class="logo">G</div>
            <div>
                <div class="name">Grani<span>va</span></div>
                <div class="tag">Ceramic & Granite Premium</div>
                <div class="addr">
                    Jl. Raya Keramik No. 123, Indonesia<br>
                    cs@graniva.com · 0812-3456-7890
                </div>
            </div>
        </div>
        <div class="lap-title-box">
            <div class="lap-title">LAPORAN PENJUALAN</div>
            <small>Monthly Sales Report</small>
            <div class="lap-period">
                <i class="bi bi-calendar3 me-1"></i>Periode: {{ now()->format('F Y') }}
            </div>
            <div class="lap-no">Dicetak: {{ now()->format('d M Y, H:i') }} WIB</div>
        </div>
    </div>

    {{-- RINGKASAN --}}
    <div class="lap-sum">
        <div class="lap-sum-card">
            <div class="lbl">Total Pendapatan</div>
            <div class="num">Rp {{ number_format($laporan->sum(fn($p) => $p->harga + ($p->ongkir ?? 0)), 0, ',', '.') }}</div>
            <div class="sub">Bulan {{ now()->format('F Y') }}</div>
        </div>
        <div class="lap-sum-card">
            <div class="lbl">Jumlah Transaksi</div>
            <div class="num">{{ $laporan->count() }}</div>
            <div class="sub">Transaksi sukses</div>
        </div>
        <div class="lap-sum-card">
            <div class="lbl">Total Pengguna</div>
            <div class="num">{{ $jumlahUser }}</div>
            <div class="sub">Pengguna terdaftar</div>
        </div>
        <div class="lap-sum-card">
            <div class="lbl">Total Produk</div>
            <div class="num">{{ $jumlahProduk }}</div>
            <div class="sub">Produk tersedia</div>
        </div>
    </div>

    {{-- TABEL --}}
    <table class="lap-table">
        <thead>
            <tr>
                <th style="width:40px">No</th>
                <th>Tanggal</th>
                <th>Invoice</th>
                <th>Pelanggan</th>
                <th>Produk</th>
                <th>Qty</th>
                <th>Status</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporan as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->created_at->format('d M Y') }}</td>
                <td><strong style="color:#b45a3c">GRV-{{ $p->created_at->format('Y') }}-{{ str_pad($p->id_pesanan, 5, '0', STR_PAD_LEFT) }}</strong></td>
                <td>{{ $p->user->nama ?? '-' }}</td>
                <td>{{ $p->produk->nama_produk ?? '-' }}</td>
                <td>{{ $p->jumlah }}</td>
                <td><span class="lap-pill" style="background:#e8f5e9;color:#1b5e20">{{ ucfirst(str_replace('_', ' ', $p->status_pesanan)) }}</span></td>
                <td>Rp {{ number_format($p->harga + ($p->ongkir ?? 0), 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;padding:24px;color:#8a8f96;font-style:italic">Tidak ada transaksi pada periode ini.</td></tr>
            @endforelse
        </tbody>
        @if($laporan->isNotEmpty())
        <tfoot>
            <tr>
                <td colspan="7">TOTAL PENDAPATAN PERIODE INI</td>
                <td>Rp {{ number_format($laporan->sum(fn($p) => $p->harga + ($p->ongkir ?? 0)), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- CATATAN --}}
    <div class="lap-note">
        <strong style="color:#8a4530"><i class="bi bi-info-circle me-1"></i>Catatan:</strong>
        Laporan ini dihasilkan otomatis oleh sistem <strong>Graniva</strong> pada {{ now()->format('d F Y, H:i') }} WIB.
        Data mencakup seluruh transaksi dengan status selain <em>dibatalkan</em> pada bulan {{ now()->format('F Y') }}.
    </div>

    {{-- TANDA TANGAN --}}
    <div class="lap-sign">
        <div class="lap-sign-box">
            <div class="role">Dibuat Oleh,</div>
            <div class="lap-sign-space"></div>
            <div class="lap-sign-name">{{ auth()->guard('admin')->user()->nama_admin ?? auth()->user()->nama ?? 'Admin Graniva' }}</div>
            <div class="lap-sign-role">Admin Graniva</div>
        </div>
        <div class="lap-sign-box">
            <div class="role">Disetujui Oleh,</div>
            <div class="lap-sign-space">
                {{-- Stempel TERVERIFIKASI --}}
                <div class="stamp">TERVERIFIKASI<br>★<br>GRANIVA</div>
            </div>
            <div class="lap-sign-name">Pimpinan</div>
            <div class="lap-sign-role">Manager Graniva</div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="lap-foot">
        <span class="brand">Grani<span>va</span></span> · Hadirkan keindahan di setiap sudut rumah.<br>
        Dokumen ini sah dan dihasilkan secara elektronik oleh sistem Graniva.
    </div>
</div>

{{-- ===== STATISTIK (tetap di layar) ===== --}}
<div class="row g-3 mb-4 stat-card-row">
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#0ea5e9,#0284c7)">
            <div class="small opacity-75">Total Pengguna</div><h3 class="fw-bold mb-0">{{ $jumlahUser }}</h3>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed)">
            <div class="small opacity-75">Total Produk</div><h3 class="fw-bold mb-0">{{ $jumlahProduk }}</h3>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
            <div class="small opacity-75">Total Pesanan</div><h3 class="fw-bold mb-0">{{ $jumlahPesanan }}</h3>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#28a745,#1e7e34)">
            <div class="small opacity-75">Total Penjualan</div><h5 class="fw-bold mb-0">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h5>
        </div>
    </div>
</div>

{{-- ===== GRAFIK + STATUS (nggak ikut print) ===== --}}
<div class="row g-3 mb-4 no-print">
    <div class="col-lg-8">
        <div class="card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-graph-up me-1"></i>Penjualan 7 Hari Terakhir</h6>
            <canvas id="chartPenjualan" height="110"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart me-1"></i>Status Pesanan</h6>
            <div class="d-flex justify-content-between border-bottom py-2 small"><span class="text-muted">Menunggu Pembayaran</span><span class="badge bg-warning text-dark">{{ $statusPesanan['menunggu_pembayaran'] }}</span></div>
            <div class="d-flex justify-content-between border-bottom py-2 small"><span class="text-muted">Diproses</span><span class="badge bg-info">{{ $statusPesanan['diproses'] }}</span></div>
            <div class="d-flex justify-content-between border-bottom py-2 small"><span class="text-muted">Dikirim</span><span class="badge bg-primary">{{ $statusPesanan['dikirim'] }}</span></div>
            <div class="d-flex justify-content-between py-2 small"><span class="text-muted">Sampai Tujuan</span><span class="badge bg-success">{{ $statusPesanan['sampai'] }}</span></div>
        </div>
    </div>
</div>

{{-- ===== LAPORAN PENJUALAN (bisa print) ===== --}}
<div class="card mb-4 print-keep">
    <div class="report-head d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h6 class="fw-bold mb-1"><i class="bi bi-file-earmark-text me-1"></i>Laporan Penjualan Bulan Ini</h6>
            <p class="small mb-0 opacity-75">{{ $laporan->count() }} transaksi · {{ now()->format('F Y') }}</p>
        </div>
        <button onclick="window.print()" class="btn btn-light btn-sm no-print fw-bold" style="color:var(--primary-dark)">
            <i class="bi bi-printer me-1"></i>Print Laporan
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr><th class="ps-4">ID</th><th>Tanggal</th><th>User</th><th>Produk</th><th>Jumlah</th><th>Total</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($laporan as $p)
                <tr>
                    <td class="ps-4 small">#{{ $p->id_pesanan }}</td>
                    <td class="small">{{ $p->created_at->format('d M Y') }}</td>
                    <td class="small">{{ $p->user->nama ?? '-' }}</td>
                    <td class="small">{{ $p->produk->nama_produk ?? '-' }}</td>
                    <td class="small">{{ $p->jumlah }} pcs</td>
                    <td class="harga small">Rp {{ number_format($p->harga + ($p->ongkir ?? 0), 0, ',', '.') }}</td>
                    <td><span class="badge badge-soft">{{ ucfirst(str_replace('_', ' ', $p->status_pesanan)) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada transaksi bulan ini.</td></tr>
                @endforelse
            </tbody>
            @if($laporan->isNotEmpty())
            <tfoot>
                <tr class="fw-bold">
                    <td class="ps-4" colspan="5">TOTAL PENDAPATAN</td>
                    <td class="harga" colspan="2">Rp {{ number_format($laporan->sum(fn($p) => $p->harga + ($p->ongkir ?? 0)), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

{{-- ===== PESANAN TERBARU (nggak ikut print) ===== --}}
<div class="card no-print">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-1"></i>Pesanan Terbaru</h6>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>ID</th><th>User</th><th>Produk</th><th>Total</th><th>Pembayaran</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($pesananTerbaru as $p)
                    <tr>
                        <td class="small">#{{ $p->id_pesanan }}</td>
                        <td class="small">{{ $p->user->nama ?? '-' }}</td>
                        <td class="small">{{ $p->produk->nama_produk ?? '-' }}</td>
                        <td class="harga small">Rp {{ number_format($p->harga + ($p->ongkir ?? 0), 0, ',', '.') }}</td>
                        <td>
                            @php $sb = $p->pembayaran->status_bayar ?? 'pending'; @endphp
                            @if($sb=='sukses') <span class="badge bg-success">Lunas</span>
                            @elseif($sb=='gagal') <span class="badge bg-danger">Gagal</span>
                            @elseif($sb=='refund') <span class="badge bg-info">Refund</span>
                            @else <span class="badge bg-warning text-dark">Pending</span> @endif
                        </td>
                        <td><span class="badge badge-soft">{{ ucfirst(str_replace('_', ' ', $p->status_pesanan)) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('chartPenjualan'), {
    type: 'line',
    data: {
        labels: @json($labels),
        datasets: [{
            label: 'Penjualan (Rp)',
            data: @json($dataPenjualan),
            borderColor: '#b45a3c',
            backgroundColor: 'rgba(180,90,60,.15)',
            borderWidth: 2, fill: true, tension: .4, pointBackgroundColor: '#b45a3c',
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
});
</script>
@endpush