<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_lokasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->foreignId('lokasi_id')->constrained('lokasis')->cascadeOnDelete();
            $table->integer('jumlah')->default(0);
            $table->integer('baik')->default(0);
            $table->integer('rusak')->default(0);
            $table->integer('rusak_berat')->default(0);
            $table->unique(['barang_id', 'lokasi_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_lokasis');
    }
};
