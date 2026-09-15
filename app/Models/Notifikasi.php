<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';

    protected $fillable = ['id_user', 'judul', 'pesan', 'tipe', 'is_read'];

    protected $casts = ['is_read' => 'boolean'];

    // Relasi ke User (notifikasi dimiliki satu user)
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}