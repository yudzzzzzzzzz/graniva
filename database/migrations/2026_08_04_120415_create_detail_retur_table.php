<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_retur', function (Blueprint $table) {
            $table->id('id_detail_retur');
            $table->foreignId('id_retur')->constrained('retur', 'id_retur')->cascadeOnDelete();
            $table->foreignId('id_produk')->constrained('produk', 'id_produk')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->string('kondisi_produk')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_retur');
    }
};