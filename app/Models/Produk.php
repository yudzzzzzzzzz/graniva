<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';
    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'id_admin', 'kategori_id', 'nama_produk', 'ukuran', 'warna', 'jenis',
        'tekstur', 'stok', 'harga', 'ongkir', 'deskripsi', 'gambar',
    ];

    public function admin() { return $this->belongsTo(Admin::class, 'id_admin', 'id_admin'); }
    public function kategori() { return $this->belongsTo(Kategori::class, 'kategori_id', 'id_kategori'); }
    public function ulasan() { return $this->hasMany(Ulasan::class, 'id_produk', 'id_produk'); }
    public function cartItems() { return $this->hasMany(CartItem::class, 'id_produk', 'id_produk'); }
    public function pesanans() { return $this->hasMany(Pesanan::class, 'id_produk', 'id_produk'); }
}