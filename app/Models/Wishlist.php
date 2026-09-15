<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $table = 'wishlists';
    protected $primaryKey = 'id_wishlist';

    protected $fillable = ['id_user', 'id_produk'];

    public function user() { return $this->belongsTo(User::class, 'id_user', 'id_user'); }
    public function produk() { return $this->belongsTo(Produk::class, 'id_produk', 'id_produk'); }
}