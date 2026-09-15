<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('id_notifikasi');
            $table->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->string('judul');
            $table->text('pesan');
            $table->string('tipe')->default('info');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('notifikasi');
    }
};