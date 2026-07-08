<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->foreignId('kategori_id')->nullable()->constrained('kategoris')->nullOnDelete();
            $table->foreignId('lokasi_id')->nullable()->after('kategori_id')->constrained()->nullOnDelete();
            $table->string('sumber')->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->string('foto')->nullable();
            $table->integer('jumlah')->default(0);
            $table->integer('baik')->default(0);
            $table->integer('rusak')->default(0);
            $table->integer('rusak_berat')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
