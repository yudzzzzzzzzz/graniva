<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ongkir extends Model
{
    protected $table = 'ongkir';
    protected $primaryKey = 'id_ongkir';

    protected $fillable = ['kota', 'biaya'];

    protected $casts = ['biaya' => 'decimal:2'];
}