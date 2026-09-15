<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasColumn('ulasan', 'gambar')) {
            Schema::table('ulasan', function (Blueprint $table) {
                $table->string('gambar')->nullable()->after('komentar');
            });
        }
    }

    public function down(): void {
        Schema::table('ulasan', function (Blueprint $table) {
            $table->dropColumn('gambar');
        });
    }
};