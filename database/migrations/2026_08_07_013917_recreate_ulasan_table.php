<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Hapus tabel ulasan lama
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('ulasan');
        Schema::enableForeignKeyConstraints();

        // Bikin ulang dengan struktur lengkap
        Schema::create('ulasan', function (Blueprint $table) {
            $table->id('id_ulasan');
            $table->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->foreignId('id_produk')->constrained('produk', 'id_produk')->cascadeOnDelete();
            $table->foreignId('id_pesanan')->nullable()->constrained('pesanan', 'id_pesanan')->nullOnDelete();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('komentar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('ulasan');
    }
};