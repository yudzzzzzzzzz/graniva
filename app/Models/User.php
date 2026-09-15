<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama', 'email', 'password', 'no_hp', 'alamat', 'is_blocked', 'blocked_at',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'password' => 'hashed',
        'is_blocked' => 'boolean',
        'blocked_at' => 'datetime',
    ];

    public function pesanan() { return $this->hasMany(Pesanan::class, 'id_user', 'id_user'); }
    public function alamat() { return $this->hasMany(Alamat::class, 'id_user', 'id_user'); }
    public function cartItems() { return $this->hasMany(CartItem::class, 'id_user', 'id_user'); }
    public function notifikasi() { return $this->hasMany(Notifikasi::class, 'id_user', 'id_user'); }
    public function ulasan() { return $this->hasMany(Ulasan::class, 'id_user', 'id_user'); }
    public function retur() { return $this->hasMany(Retur::class, 'id_user', 'id_user'); }
}