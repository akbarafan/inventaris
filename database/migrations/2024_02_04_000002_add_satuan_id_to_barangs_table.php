<?php

use App\Models\Satuan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->foreignId('satuan_id')->nullable()->after('sumber_id')->constrained('satuans')->nullOnDelete();
        });

        Schema::table('barang_lokasis', function (Blueprint $table) {
            $table->foreignId('satuan_id')->nullable()->after('lokasi_id')->constrained('satuans')->nullOnDelete();
        });

        $pcs = Satuan::firstOrCreate(['nama_satuan' => 'pcs']);
        DB::table('barangs')->whereNull('satuan_id')->update(['satuan_id' => $pcs->id]);
        DB::table('barang_lokasis')->whereNull('satuan_id')->update(['satuan_id' => $pcs->id]);
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropForeign(['satuan_id']);
            $table->dropColumn('satuan_id');
        });

        Schema::table('barang_lokasis', function (Blueprint $table) {
            $table->dropForeign(['satuan_id']);
            $table->dropColumn('satuan_id');
        });
    }
};
