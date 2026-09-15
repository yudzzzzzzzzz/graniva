<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->foreignId('id_produk')->constrained('produk', 'id_produk')->cascadeOnDelete();
            $table->integer('jumlah')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('cart_items');
    }
};