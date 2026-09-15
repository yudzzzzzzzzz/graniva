<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ulasan', function (Blueprint $table) {
            $table->id('id_ulasan');
            $table->foreignId('id_pesanan')->constrained('pesanan', 'id_pesanan')->cascadeOnDelete();
            $table->foreignId('id_produk')->constrained('produk', 'id_produk')->cascadeOnDelete();
            $table->date('tanggal_ulasan')->nullable();
            $table->text('komentar')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ulasan');
    }
};