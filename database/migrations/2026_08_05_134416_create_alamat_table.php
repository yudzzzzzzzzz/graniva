<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('alamat', function (Blueprint $table) {
            $table->id('id_alamat');
            $table->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->string('label')->default('Rumah');
            $table->string('penerima');
            $table->string('no_hp');
            $table->text('alamat_lengkap');
            $table->string('kota');
            $table->string('kode_pos', 10);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('alamat');
    }
};