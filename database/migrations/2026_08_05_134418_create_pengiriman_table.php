<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->id('id_pengiriman');
            $table->foreignId('id_pesanan')->constrained('pesanan', 'id_pesanan')->cascadeOnDelete();
            $table->string('status')->default('diproses');
            $table->text('catatan')->nullable();
            $table->timestamp('waktu')->useCurrent();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pengiriman');
    }
};