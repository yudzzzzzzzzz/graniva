<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id('id_withdrawal');
            $table->unsignedBigInteger('id_user');
            $table->decimal('nominal', 15, 2);
            $table->decimal('biaya_admin', 15, 2)->default(500);
            $table->string('metode');
            $table->string('no_rekening');
            $table->string('nama_bank')->nullable();
            $table->enum('status', ['pending', 'sukses', 'gagal'])->default('pending');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('withdrawals'); }
};