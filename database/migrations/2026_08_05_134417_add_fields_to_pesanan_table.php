<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->unsignedBigInteger('alamat_id')->nullable()->after('id_produk');
            $table->string('status_pesanan')->default('diproses')->after('harga');
            $table->string('kurir')->nullable()->after('status_pesanan');
            $table->string('no_resi')->nullable()->after('kurir');
            $table->date('estimasi_datang')->nullable()->after('no_resi');
            $table->text('kendala_pengiriman')->nullable()->after('estimasi_datang');
        });
    }

    public function down(): void {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['alamat_id', 'status_pesanan', 'kurir', 'no_resi', 'estimasi_datang', 'kendala_pengiriman']);
        });
    }
};