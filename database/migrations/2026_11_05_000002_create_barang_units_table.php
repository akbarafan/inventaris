<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->string('kode_unit', 64)->unique();
            $table->unsignedInteger('nomor_urut');
            $table->foreignId('lokasi_id')->nullable()->constrained()->nullOnDelete();
            $table->string('kondisi', 20)->default('baik');
            $table->string('status')->default('aktif');
            $table->string('nomor_seri', 64)->nullable();
            $table->timestamps();

            $table->unique(['barang_id', 'nomor_urut']);
            $table->index(['lokasi_id']);
            $table->index(['kondisi']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_units');
    }
};
