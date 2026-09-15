<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasColumn('pesanan', 'alasan_batal')) {
            Schema::table('pesanan', function (Blueprint $table) {
                $table->text('alasan_batal')->nullable()->after('kendala_pengiriman');
            });
        }
    }
    public function down(): void {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('alasan_batal');
        });
    }
};