<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailRetur extends Model
{
    use HasFactory;

    protected $table = 'detail_retur';
    protected $primaryKey = 'id_detail_retur';

    protected $fillable = [
        'id_retur',
        'id_produk',
        'jumlah',
        'kondisi_produk',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    public function retur()
    {
        return $this->belongsTo(Retur::class, 'id_retur', 'id_retur');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}