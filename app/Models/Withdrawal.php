<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $table = 'withdrawals';
    protected $primaryKey = 'id_withdrawal';

    protected $fillable = ['id_user', 'nominal', 'biaya_admin', 'metode', 'no_rekening', 'nama_bank', 'status'];

    public function user() { return $this->belongsTo(User::class, 'id_user', 'id_user'); }
}