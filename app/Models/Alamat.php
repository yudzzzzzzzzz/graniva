<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    protected $table = 'alamat';
    protected $primaryKey = 'id_alamat';

    protected $fillable = [
        'id_user', 'label', 'penerima', 'no_hp',
        'alamat_lengkap', 'kota', 'kode_pos', 'is_default',
    ];

    protected $casts = ['is_default' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}