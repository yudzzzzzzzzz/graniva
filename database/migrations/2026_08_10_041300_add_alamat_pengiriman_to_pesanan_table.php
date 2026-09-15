<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasColumn('pesanan', 'alamat_pengiriman')) {
            Schema::table('pesanan', function (Blueprint $table) {
                $table->text('alamat_pengiriman')->nullable()->after('kategori');
            });
        }
    }

    public function down(): void {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('alamat_pengiriman');
        });
    }
};