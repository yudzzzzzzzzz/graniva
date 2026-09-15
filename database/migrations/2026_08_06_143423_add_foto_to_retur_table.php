<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasColumn('retur', 'foto')) {
            Schema::table('retur', function (Blueprint $table) {
                $table->string('foto')->nullable()->after('alasan');
            });
        }
    }

    public function down(): void {
        Schema::table('retur', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
};