<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Tabel Ulasan
        if (!Schema::hasTable('ulasan')) {
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

        // Tabel Retur
        if (!Schema::hasTable('retur')) {
            Schema::create('retur', function (Blueprint $table) {
                $table->id('id_retur');
                $table->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnDelete();
                $table->foreignId('id_pesanan')->constrained('pesanan', 'id_pesanan')->cascadeOnDelete();
                $table->text('alasan');
                $table->string('foto')->nullable();
                $table->string('status')->default('pending'); // pending, disetujui, ditolak
                $table->timestamps();
            });
        }

        // Tabel Ongkir
        if (!Schema::hasTable('ongkir')) {
            Schema::create('ongkir', function (Blueprint $table) {
                $table->id('id_ongkir');
                $table->string('kota')->unique();
                $table->decimal('biaya', 15, 2);
                $table->timestamps();
            });
        }

        // Tambah kolom ongkir & total di pesanan
        if (!Schema::hasColumn('pesanan', 'ongkir')) {
            Schema::table('pesanan', function (Blueprint $table) {
                $table->decimal('ongkir', 15, 2)->default(0)->after('harga');
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('ulasan');
        Schema::dropIfExists('retur');
        Schema::dropIfExists('ongkir');
    }
};