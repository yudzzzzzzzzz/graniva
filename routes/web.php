<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminKategoriController;
use App\Http\Controllers\AdminLaporanController;
use App\Http\Controllers\AdminNotifikasiController;
use App\Http\Controllers\AdminPesananController;
use App\Http\Controllers\AdminReturController;
use App\Http\Controllers\AdminTopupController;
use App\Http\Controllers\AdminUlasanController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminWithdrawController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\StatistikController;
use App\Http\Controllers\TopupController;
use App\Http\Controllers\UlasanController;
use App\Http\Controllers\WithdrawController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

// ===== HOMEPAGE =====
Route::get('/', fn () => redirect()->route('produk.index'));

// ===== HALAMAN AKUN DIBLOKIR (tanpa login) =====
Route::get('/akun-diblokir', function () {
    $userId = session('blocked_user_id');
    $user = $userId ? \App\Models\User::find($userId) : null;
    return view('auth.blocked', compact('user'));
})->name('blocked');

// ✅ BARU: tombol keluar dari halaman akun diblokir (notif + berhasil.mp3)
Route::any('/akun-diblokir/keluar', [AuthController::class, 'leaveBlocked'])->name('blocked.leave');

// ===== AUTH USER (guest) =====
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Routes verifikasi email (setelah register)
    Route::get('/verify', [AuthController::class, 'showVerify'])->name('verify.show');
    Route::post('/verify', [AuthController::class, 'verify'])->name('verify');
    Route::post('/resend', [AuthController::class, 'resend'])->name('resend');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ===== SEMUA FITUR USER (WAJIB LOGIN) =====
Route::middleware('auth')->group(function () {

    // Katalog Produk
    Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
    Route::get('/produk/{id}', [ProdukController::class, 'show'])->name('produk.show');

    // Wishlist / Favorit
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    // Pesanan User
    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/create', [PesananController::class, 'create'])->name('pesanan.create');
    Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.store');

    // Draft pembayaran (pesanan BELUM dibuat) — HARUS di atas route {id}
    Route::get('/pesanan/draft/bayar', [PesananController::class, 'showPaymentDraft'])->name('pesanan.bayarDraft');
    Route::post('/pesanan/draft/bayar', [PesananController::class, 'payDraft'])->name('pesanan.payDraft');

    Route::get('/pesanan/{id}/bayar', [PesananController::class, 'showPayment'])->name('pesanan.bayar');
    Route::post('/pesanan/{id}/bayar', [PesananController::class, 'pay'])->name('pesanan.pay');
    Route::get('/pesanan/{id}/track', [PesananController::class, 'track'])->name('pesanan.track');
    Route::get('/pesanan/{id}/invoice', [PesananController::class, 'invoice'])->name('pesanan.invoice');
    Route::post('/pesanan/{id}/cancel', [PesananController::class, 'cancel'])->name('pesanan.cancel');
    Route::delete('/pesanan/{id}', [PesananController::class, 'destroy'])->name('pesanan.destroy');

    // Keranjang Belanja
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/checkout', [CartController::class, 'toCheckout'])->name('cart.toCheckout');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

    // Top Up GraPay
    Route::get('/topup', [TopupController::class, 'index'])->name('topup.index');
    Route::post('/topup', [TopupController::class, 'store'])->name('topup.store');

    // Tarik Saldo GraPay
    Route::get('/withdraw', [WithdrawController::class, 'index'])->name('withdraw.index');
    Route::post('/withdraw', [WithdrawController::class, 'store'])->name('withdraw.store');

    // Notifikasi User
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'markRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotifikasiController::class, 'markAllRead'])->name('notifikasi.markAll');

    // Profil
    Route::get('/profil', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [ProfilController::class, 'changePassword'])->name('profil.password');

    // Statistik Belanja
    Route::get('/statistik', [StatistikController::class, 'index'])->name('statistik.index');

    // Ulasan User
    Route::get('/ulasan', fn () => redirect()->route('produk.index'));
    Route::post('/ulasan', [UlasanController::class, 'store'])->name('ulasan.store');
    Route::delete('/ulasan/{id}', [UlasanController::class, 'destroy'])->name('ulasan.destroy');

    // Retur
    Route::get('/retur', [ReturController::class, 'index'])->name('retur.index');
    Route::get('/retur/create', [ReturController::class, 'create'])->name('retur.create');
    Route::post('/retur', [ReturController::class, 'store'])->name('retur.store');
});

// ===== ADMIN PANEL =====
Route::prefix('admin')->name('admin.')->group(function () {

    // Auth Admin
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::any('/logout', [AdminAuthController::class, 'logout'])->name('logout')->middleware('admin');

    // Fitur Admin (wajib login admin)
    Route::middleware('admin')->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Produk
        Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
        Route::get('/produk/create', [ProdukController::class, 'create'])->name('produk.create');
        Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
        Route::get('/produk/{id}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
        Route::put('/produk/{id}', [ProdukController::class, 'update'])->name('produk.update');
        Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');

        // Kategori
        Route::get('/kategori', [AdminKategoriController::class, 'index'])->name('kategori.index');
        Route::get('/kategori/create', [AdminKategoriController::class, 'create'])->name('kategori.create');
        Route::post('/kategori', [AdminKategoriController::class, 'store'])->name('kategori.store');
        Route::get('/kategori/{id}/edit', [AdminKategoriController::class, 'edit'])->name('kategori.edit');
        Route::put('/kategori/{id}', [AdminKategoriController::class, 'update'])->name('kategori.update');
        Route::delete('/kategori/{id}', [AdminKategoriController::class, 'destroy'])->name('kategori.destroy');

        // Pesanan
        Route::get('/pesanan', [AdminPesananController::class, 'index'])->name('pesanan.index');
        Route::get('/pesanan/{id}', [AdminPesananController::class, 'show'])->name('pesanan.show');
        Route::post('/pesanan/{id}/status', [AdminPesananController::class, 'updateStatus'])->name('pesanan.status');
        Route::post('/pesanan/{id}/verify', [AdminPesananController::class, 'verifyPayment'])->name('pesanan.verify');
        Route::post('/pesanan/{id}/shipping', [AdminPesananController::class, 'updateShipping'])->name('pesanan.shipping');
        Route::post('/pesanan/{id}/arrive', [AdminPesananController::class, 'markArrived'])->name('pesanan.arrive');
        Route::post('/pesanan/{id}/kendala', [AdminPesananController::class, 'setKendala'])->name('pesanan.kendala');
        Route::post('/pesanan/{id}/cancel', [AdminPesananController::class, 'cancel'])->name('pesanan.cancel');

        // Top Up GraPay
        Route::get('/topup', [AdminTopupController::class, 'index'])->name('topup.index');
        Route::post('/topup/{id}/process', [AdminTopupController::class, 'process'])->name('topup.process');

        // Penarikan Saldo
        Route::get('/withdraw', [AdminWithdrawController::class, 'index'])->name('withdraw.index');
        Route::post('/withdraw/{id}/process', [AdminWithdrawController::class, 'process'])->name('withdraw.process');

        // Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('users.show');
        Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('users.update');
        Route::post('/users/{id}/reset', [AdminUserController::class, 'resetPassword'])->name('users.reset');
        Route::post('/users/{id}/block', [AdminUserController::class, 'toggleBlock'])->name('users.block');
        Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // Notifikasi
        Route::get('/notifikasi', [AdminNotifikasiController::class, 'index'])->name('notifikasi.index');
        Route::post('/notifikasi/send', [AdminNotifikasiController::class, 'send'])->name('notifikasi.send');
        Route::delete('/notifikasi/delete-all', [AdminNotifikasiController::class, 'deleteAll'])->name('notifikasi.deleteAll');
        Route::delete('/notifikasi/delete-user/{id}', [AdminNotifikasiController::class, 'deleteUser'])->name('notifikasi.deleteUser');
        Route::delete('/notifikasi/{id}', [AdminNotifikasiController::class, 'deleteOne'])->name('notifikasi.deleteOne');

        // Retur Admin
        Route::get('/retur', [AdminReturController::class, 'index'])->name('retur.index');
        Route::post('/retur/{id}/process', [AdminReturController::class, 'process'])->name('retur.process');

        // Kelola Rating & Ulasan
        Route::get('/ulasan', [AdminUlasanController::class, 'index'])->name('ulasan.index');
        Route::post('/ulasan/{id}/reply', [AdminUlasanController::class, 'reply'])->name('ulasan.reply');
        Route::delete('/ulasan/{id}', [AdminUlasanController::class, 'destroy'])->name('ulasan.destroy');

        // Laporan Penjualan
        Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export', [AdminLaporanController::class, 'export'])->name('laporan.export');
    });
});