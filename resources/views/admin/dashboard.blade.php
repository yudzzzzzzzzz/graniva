@extends('layouts.admin')
@section('title', 'Dashboard - Admin Graniva')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    :root{--gold:#c9a227;--gold-soft:#e6c968;--ink:#23272c;--muted:#8a8f96;--cream:#faf7f2;}

    /* ===== HEADER DASHBOARD ===== */
    .dash-head{display:flex;justify-content:space-between;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:26px}
    .dash-head h2{font-family:'Playfair Display',serif;font-weight:800;font-size:1.7rem;margin:0;color:var(--ink)}
    .dash-head .sub{font-size:.82rem;color:var(--muted);margin-top:2px}
    .btn-gold{
        border:none;border-radius:12px;padding:10px 20px;font-weight:700;font-size:.84rem;
        background:linear-gradient(135deg,var(--gold),#a8861c);color:#fff;
        box-shadow:0 8px 20px rgba(201,162,39,.35);display:inline-flex;align-items:center;gap:8px;
        text-decoration:none;transition:.2s;cursor:pointer;
    }
    .btn-gold:hover{transform:translateY(-2px);box-shadow:0 12px 26px rgba(201,162,39,.45);color:#fff}

    /* ===== KARTU STATISTIK ===== */
    .stat-lux{
        background:#fff;border:none;border-radius:18px;padding:22px;position:relative;overflow:hidden;
        box-shadow:0 6px 22px rgba(35,39,44,.07);transition:.25s;border-top:4px solid transparent;
    }
    .stat-lux:hover{transform:translateY(-4px);box-shadow:0 16px 34px rgba(35,39,44,.12)}
    .stat-lux::after{
        content:"";position:absolute;right:-30px;top:-30px;width:110px;height:110px;border-radius:50%;
        background:radial-gradient(circle, rgba(201,162,39,.14), transparent 70%);
    }
    .stat-lux .icon-wrap{
        width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;
        font-size:1.35rem;color:#fff;margin-bottom:14px;box-shadow:0 8px 18px rgba(0,0,0,.18);
    }
    .stat-lux .num{font-family:'Playfair Display',serif;font-weight:800;font-size:1.65rem;color:var(--ink);line-height:1.1}
    .stat-lux .lbl{font-size:.72rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-top:4px}
    .grad-copper{background:linear-gradient(135deg,#b45a3c,#96482e)}
    .grad-gold{background:linear-gradient(135deg,#c9a227,#a8861c)}
    .grad-dark{background:linear-gradient(135deg,#3a4048,#23272c)}
    .grad-green{background:linear-gradient(135deg,#2e9e5b,#20784a)}
    .grad-blue{background:linear-gradient(135deg,#3d7bd9,#28599c)}
    .grad-red{background:linear-gradient(135deg,#d64545,#a92e2e)}

    /* ===== KARTU UMUM ===== */
    .card-lux{background:#fff;border:none;border-radius:18px;box-shadow:0 6px 22px rgba(35,39,44,.07);overflow:hidden}
    .card-lux .card-head{
        padding:18px 22px;border-bottom:1px solid #f0ebe3;display:flex;justify-content:space-between;align-items:center;
    }
    .card-lux .card-head .t{font-weight:800;font-size:.95rem;color:var(--ink);display:flex;align-items:center;gap:9px}
    .card-lux .card-head .t i{color:var(--gold);font-size:1.05rem}
    .card-lux .card-body-lux{padding:22px}

    /* ===== TABEL ===== */
    .table-lux{width:100%;border-collapse:collapse}
    .table-lux th{font-size:.68rem;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);padding:10px 12px;text-align:left;border-bottom:2px solid #f0ebe3}
    .table-lux td{padding:13px 12px;font-size:.86rem;border-bottom:1px solid #f6f2ea;vertical-align:middle}
    .table-lux tr:last-child td{border-bottom:none}
    .table-lux tr:hover td{background:var(--cream)}
    .pill{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:4px 12px;font-size:.7rem;font-weight:800;letter-spacing:.4px;text-transform:uppercase}

    /* ===== STOK MENIPIS ===== */
    .stock-item{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:11px 0;border-bottom:1px dashed #eee7dc}
    .stock-item:last-child{border-bottom:none}
    .stock-bar{height:6px;border-radius:6px;background:#f0ebe3;overflow:hidden;margin-top:6px}
    .stock-bar > div{height:100%;border-radius:6px;background:linear-gradient(90deg,#d64545,#e67e22)}

    /* ===== PRINT ===== */
    @@media print{
        .sidebar, .topbar, .btn-gold, .no-print{display:none !important}
        .main-content{margin-left:0;padding:0}
        .stat-lux, .card-lux{box-shadow:none;border:1px solid #e5ded5}
        body{background:#fff}
    }
</style>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
@endpush

@section('content')
@php
    // ===== AMBIL DARI CONTROLLER, KALAU KOSONG HITUNG SENDIRI (AMAN) =====
    try { $totalPendapatan = $totalPendapatan ?? \App\Models\Pesanan::whereNotIn('status_pesanan', ['dibatalkan', 'diretur'])->get()->sum(fn($p) => ($p->harga * $p->jumlah) + ($p->ongkir ?? 0)); } catch (\Throwable $e) { $totalPendapatan = 0; }
    try { $totalPesanan  = $totalPesanan  ?? \App\Models\Pesanan::count(); }   catch (\Throwable $e) { $totalPesanan = 0; }
    try { $totalUser     = $totalUser     ?? \App\Models\User::count(); }      catch (\Throwable $e) { $totalUser = 0; }
    try { $totalProduk   = $totalProduk   ?? \App\Models\Produk::count(); }    catch (\Throwable $e) { $totalProduk = 0; }
    try { $returPending  = $returPending  ?? \App\Models\Retur::whereIn('status', ['pending', 'menunggu'])->count(); } catch (\Throwable $e) { $returPending = 0; }
    try { $topupPending  = $topupPending  ?? \App\Models\Topup::whereIn('status', ['pending', 'menunggu'])->count(); } catch (\Throwable $e) { $topupPending = 0; }

    // Pesanan terbaru
    try { $pesananTerbaru = $pesananTerbaru ?? \App\Models\Pesanan::with(['user', 'produk'])->latest()->limit(8)->get(); } catch (\Throwable $e) { $pesananTerbaru = collect(); }

    // Stok menipis
    try { $stokMenipis = $stokMenipis ?? \App\Models\Produk::where('stok', '<=', 10)->orderBy('stok')->limit(6)->get(); } catch (\Throwable $e) { $stokMenipis = collect(); }

    // Grafik 6 bulan terakhir
    try {
        if (!isset($chartLabels) || !isset($chartData)) {
            $rows = \App\Models\Pesanan::whereNotIn('status_pesanan', ['dibatalkan'])
                ->get(['created_at', 'harga', 'jumlah', 'ongkir'])
                ->groupBy(fn($p) => $p->created_at->format('Y-m'))
                ->map(fn($g) => $g->sum(fn($p) => ($p->harga * $p->jumlah) + ($p->ongkir ?? 0)));
            $months = collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->format('Y-m'));
            $chartLabels = $months->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->isoFormat('MMM YYYY'));
            $chartData   = $months->map(fn($m) => $rows[$m] ?? 0);
        }
    } catch (\Throwable $e) { $chartLabels = collect(); $chartData = collect(); }

    $pill = function ($status) {
        return match (true) {
            in_array($status, ['sampai', 'selesai'])      => 'background:#e8f5e9;color:#1b5e20',
            in_array($status, ['dikirim', 'diproses'])    => 'background:#e3f2fd;color:#0d47a1',
            in_array($status, ['diretur'])                => 'background:#fff3e0;color:#e65100',
            in_array($status, ['dibatalkan', 'kendala'])  => 'background:#ffebee;color:#b71c1c',
            default                                       => 'background:#fff8e1;color:#8d6e00',
        };
    };
@endphp

{{-- ===== HEADER ===== --}}
<div class="dash-head">
    <div>
        <h2>Ringkasan Bisnis</h2>
        <div class="sub"><i class="bi bi-calendar3 me-1"></i>{{ now()->isoFormat('dddd, D MMMM Y') }} · Pantau performa Graniva secara real-time</div>
    </div>
    <div class="d-flex gap-2 no-print">
        <button onclick="window.print()" class="btn-gold"><i class="bi bi-printer"></i>Cetak Laporan</button>
        <a href="{{ route('admin.laporan.export') }}" class="btn-gold" style="background:linear-gradient(135deg,#b45a3c,#96482e);box-shadow:0 8px 20px rgba(180,90,60,.35)"><i class="bi bi-file-earmark-excel"></i>Export</a>
    </div>
</div>

{{-- ===== KARTU STATISTIK ===== --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-2">
        <div class="stat-lux" style="border-top-color:var(--gold)">
            <div class="icon-wrap grad-gold"><i class="bi bi-cash-coin"></i></div>
            <div class="num">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            <div class="lbl">Pendapatan</div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-lux" style="border-top-color:#b45a3c">
            <div class="icon-wrap grad-copper"><i class="bi bi-bag-check"></i></div>
            <div class="num">{{ $totalPesanan }}</div>
            <div class="lbl">Total Pesanan</div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-lux" style="border-top-color:#3d7bd9">
            <div class="icon-wrap grad-blue"><i class="bi bi-people"></i></div>
            <div class="num">{{ $totalUser }}</div>
            <div class="lbl">Pengguna</div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-lux" style="border-top-color:#3a4048">
            <div class="icon-wrap grad-dark"><i class="bi bi-box-seam"></i></div>
            <div class="num">{{ $totalProduk }}</div>
            <div class="lbl">Produk</div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-lux" style="border-top-color:#d64545">
            <div class="icon-wrap grad-red"><i class="bi bi-arrow-return-left"></i></div>
            <div class="num">{{ $returPending }}</div>
            <div class="lbl">Retur Pending</div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-lux" style="border-top-color:#2e9e5b">
            <div class="icon-wrap grad-green"><i class="bi bi-plus-circle"></i></div>
            <div class="num">{{ $topupPending }}</div>
            <div class="lbl">Top Up Pending</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- ===== GRAFIK PENJUALAN ===== --}}
    <div class="col-lg-8">
        <div class="card-lux h-100">
            <div class="card-head">
                <div class="t"><i class="bi bi-graph-up-arrow"></i>Tren Pendapatan (6 Bulan)</div>
                <span class="pill" style="background:#faf3dc;color:#8d6e00"><i class="bi bi-stars"></i>Live</span>
            </div>
            <div class="card-body-lux">
                <canvas id="chartPendapatan" height="110"></canvas>
            </div>
        </div>
    </div>

    {{-- ===== STOK MENIPIS ===== --}}
    <div class="col-lg-4">
        <div class="card-lux h-100">
            <div class="card-head">
                <div class="t"><i class="bi bi-exclamation-triangle"></i>Stok Menipis</div>
                <a href="{{ route('admin.produk.index') }}" class="small fw-bold text-decoration-none" style="color:var(--gold)">Lihat semua</a>
            </div>
            <div class="card-body-lux">
                @forelse($stokMenipis as $p)
                    <div class="stock-item">
                        <div style="flex:1">
                            <div class="fw-bold" style="font-size:.86rem">{{ $p->nama_produk }}</div>
                            <div class="stock-bar"><div style="width:{{ min(100, $p->stok * 10) }}%"></div></div>
                        </div>
                        <span class="pill" style="background:#ffebee;color:#b71c1c">{{ $p->stok }} pcs</span>
                    </div>
                @empty
                    <div class="text-center text-muted small py-4"><i class="bi bi-check-circle d-block mb-2" style="font-size:1.4rem;color:#2e9e5b"></i>Semua stok aman.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ===== PESANAN TERBARU ===== --}}
<div class="card-lux">
    <div class="card-head">
        <div class="t"><i class="bi bi-clock-history"></i>Pesanan Terbaru</div>
        <a href="{{ route('admin.pesanan.index') }}" class="btn-gold no-print" style="padding:7px 14px;font-size:.76rem">Kelola Pesanan</a>
    </div>
    <div class="table-responsive">
        <table class="table-lux">
            <thead>
                <tr>
                    <th style="padding-left:22px">Invoice</th>
                    <th>Pelanggan</th>
                    <th>Produk</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th style="padding-right:22px">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesananTerbaru as $ps)
                <tr>
                    <td style="padding-left:22px"><strong class="text-decoration-none" style="color:#b45a3c">GRV-{{ $ps->created_at?->format('Y') }}-{{ str_pad($ps->id_pesanan, 5, '0', STR_PAD_LEFT) }}</strong></td>
                    <td>{{ $ps->user->nama ?? '-' }}</td>
                    <td>{{ $ps->produk->nama_produk ?? '-' }} <span class="text-muted">×{{ $ps->jumlah }}</span></td>
                    <td class="fw-bold">Rp {{ number_format(($ps->harga * $ps->jumlah) + ($ps->ongkir ?? 0), 0, ',', '.') }}</td>
                    <td><span class="pill" style="{{ $pill($ps->status_pesanan) }}">{{ ucfirst(str_replace('_', ' ', $ps->status_pesanan ?? '-')) }}</span></td>
                    <td style="padding-right:22px" class="text-muted small">{{ $ps->created_at?->format('d M Y, H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pesanan masuk.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('chartPendapatan');
    if (!ctx) return;

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(201, 162, 39, .35)');
    gradient.addColorStop(1, 'rgba(201, 162, 39, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels->values()),
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: @json($chartData->values()),
                borderColor: '#c9a227',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: .4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#c9a227',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#23272c',
                    titleFont: { family: 'Inter', weight: '700' },
                    bodyFont: { family: 'Inter' },
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: (c) => ' Rp ' + Number(c.parsed.y).toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f0ebe3' },
                    ticks: {
                        font: { family: 'Inter', size: 11 },
                        callback: (v) => 'Rp ' + (v >= 1000000 ? (v / 1000000) + 'jt' : (v >= 1000 ? (v / 1000) + 'rb' : v))
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Inter', size: 11, weight: '600' } }
                }
            }
        }
    });
});
</script>
@endpush