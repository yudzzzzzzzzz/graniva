<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $table = 'admins';
    protected $primaryKey = 'id_admin';

    protected $fillable = [
        'nama_admin',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function produk()
    {
        return $this->hasMany(Produk::class, 'id_admin', 'id_admin');
    }
}