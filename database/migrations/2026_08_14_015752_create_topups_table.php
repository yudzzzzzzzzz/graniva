<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('topups', function (Blueprint $table) {
            $table->id('id_topup');
            $table->unsignedBigInteger('id_user');
            $table->decimal('nominal', 15, 2);
            $table->string('bukti_bayar')->nullable();
            $table->enum('status', ['pending', 'sukses', 'gagal'])->default('pending');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('topups'); }
};