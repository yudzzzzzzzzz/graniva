<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    // ✅ TAMBAHKAN: nama tabel yang bener
    protected $table = 'retur';
    protected $primaryKey = 'id_retur';
    public $timestamps = true;

    protected $fillable = [
        'id_retur',
        'id_user',
        'id_pesanan',
        'alasan',
        'bukti_foto',
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