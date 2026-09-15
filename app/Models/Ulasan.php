<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ulasan extends Model
{
    protected $table = 'ulasan';
    protected $primaryKey = 'id_ulasan';

    protected $fillable = ['id_user', 'id_produk', 'id_pesanan', 'rating', 'komentar', 'gambar', 'balasan_admin'];

    public function user() { return $this->belongsTo(User::class, 'id_user', 'id_user'); }
    public function produk() { return $this->belongsTo(Produk::class, 'id_produk', 'id_produk'); }
    public function pesanan() { return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan'); }
}