<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topup extends Model
{
    protected $table = 'topups';
    protected $primaryKey = 'id_topup';

    protected $fillable = ['id_user', 'nominal', 'bukti_bayar', 'status'];

    public function user() { return $this->belongsTo(User::class, 'id_user', 'id_user'); }
}