<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scan_logs', function (Blueprint $table) {
            $table->foreignId('barang_unit_id')->nullable()->constrained('barang_units')->nullOnDelete();
            $table->string('kode_unit', 64)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('scan_logs', function (Blueprint $table) {
            $table->dropForeign(['barang_unit_id']);
            $table->dropColumn(['barang_unit_id', 'kode_unit']);
        });
    }
};
