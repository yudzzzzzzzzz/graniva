<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasColumn('users', 'saldo')) {
            Schema::table('users', function (Blueprint $table) {
                $table->decimal('saldo', 15, 2)->default(0);
            });
        }
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('saldo');
        });
    }
};