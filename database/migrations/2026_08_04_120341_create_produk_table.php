<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id('id_produk');
            $table->foreignId('id_admin')->constrained('admins', 'id_admin')->cascadeOnDelete();
            $table->string('nama_produk');
            $table->string('ukuran')->nullable();
            $table->string('warna')->nullable();
            $table->string('jenis')->nullable();
            $table->string('tekstur')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->integer('stok')->default(0);
            $table->decimal('harga', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};