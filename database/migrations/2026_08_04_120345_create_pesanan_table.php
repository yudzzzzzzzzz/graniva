<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('pesanan', function (Blueprint $table) {
    $table->id('id_pesanan');
    $table->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnDelete();
    $table->foreignId('id_produk')->constrained('produk', 'id_produk')->cascadeOnDelete();
    $table->integer('jumlah');
    $table->decimal('harga', 12, 2);
    $table->timestamps();
}); 
    }
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};