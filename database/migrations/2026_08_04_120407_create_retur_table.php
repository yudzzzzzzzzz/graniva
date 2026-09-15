<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    // ✅ KUNCI FIX: nama tabel aslinya 'retur' (bukan 'returs')
    protected $table = 'retur';
    protected $primaryKey = 'id_retur';

    protected $fillable = [
        'id_user',
        'id_pesanan',
        'alasan',
        'foto',
        'status',
        'catatan_admin',
        'tanggal_proses',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
}