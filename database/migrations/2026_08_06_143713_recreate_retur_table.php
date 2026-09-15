<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Hapus tabel retur lama
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('retur');
        Schema::enableForeignKeyConstraints();

        // Bikin ulang dengan struktur lengkap
        Schema::create('retur', function (Blueprint $table) {
            $table->id('id_retur');
            $table->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->foreignId('id_pesanan')->constrained('pesanan', 'id_pesanan')->cascadeOnDelete();
            $table->text('alasan');
            $table->string('foto')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('retur');
    }
};