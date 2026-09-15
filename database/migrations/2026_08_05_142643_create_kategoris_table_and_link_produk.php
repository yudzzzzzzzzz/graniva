<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('kategoris', function (Blueprint $table) {
            $table->id('id_kategori');
            $table->string('nama_kategori')->unique();
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->timestamps();
        });

        Schema::table('produk', function (Blueprint $table) {
            $table->foreignId('kategori_id')->nullable()->after('id_admin')
                  ->constrained('kategoris', 'id_kategori')->nullOnDelete();
        });

        // Auto-import kategori dari kolom `jenis` yang udah ada (data real, bukan dummy)
        $jenisList = DB::table('produk')->whereNotNull('jenis')->distinct()->pluck('jenis');
        foreach ($jenisList as $jenis) {
            DB::table('kategoris')->insert([
                'nama_kategori' => $jenis,
                'slug' => \Illuminate\Support\Str::slug($jenis).'-'.uniqid(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Link produk ke kategori berdasarkan jenis
        foreach (DB::table('kategoris')->get() as $kat) {
            DB::table('produk')->where('jenis', $kat->nama_kategori)->update(['kategori_id' => $kat->id_kategori]);
        }
    }

    public function down(): void {
        Schema::table('produk', fn(Blueprint $t) => $t->dropConstrainedForeignId('kategori_id'));
        Schema::dropIfExists('kategoris');
    }
};