<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id('id_wishlist');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_produk');
            $table->timestamps();
            $table->unique(['id_user', 'id_produk']);
        });
    }
    public function down(): void { Schema::dropIfExists('wishlists'); }
};