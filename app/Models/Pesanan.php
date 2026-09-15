<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';

    protected $fillable = [
        'id_user', 'id_produk', 'alamat_id', 'jumlah', 'harga', 'ongkir', 'kategori',
        'alamat_pengiriman', 'status_pesanan', 'estimasi_datang', 'kurir', 'no_resi',
        'kendala_pengiriman', 'alasan_batal',
    ];

    public function user() { return $this->belongsTo(User::class, 'id_user', 'id_user'); }
    public function produk() { return $this->belongsTo(Produk::class, 'id_produk', 'id_produk'); }
    public function alamat() { return $this->belongsTo(Alamat::class, 'alamat_id', 'id_alamat'); }
    public function pembayaran() { return $this->hasOne(Pembayaran::class, 'id_pesanan', 'id_pesanan'); }
    public function retur() { return $this->hasOne(Retur::class, 'id_pesanan', 'id_pesanan'); }
    public function ulasan() { return $this->hasOne(Ulasan::class, 'id_pesanan', 'id_pesanan'); }

    // ===== BARU: total bayar (harga × jumlah + ongkir) =====
    public function getTotalBayarAttribute()
    {
        return ($this->harga * $this->jumlah) + ($this->ongkir ?? 0);
    }

    // ===== BARU: relasi kompatibilitas detailPesanan =====
    // Proyek ini 1 pesanan = 1 produk (tanpa tabel detail).
    // Relasi ini mengembalikan koleksi berisi pesanan itu sendiri,
    // supaya kode lama yang memanggil $pesanan->detailPesanan / with('detailPesanan')
    // tidak error dan tetap dapat ->produk, ->jumlah, dll.
    public function detailPesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}