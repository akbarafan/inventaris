<?php

use App\Models\Sumber;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->foreignId('sumber_id')->nullable()->constrained('sumbers')->nullOnDelete();
        });

        $oldValues = DB::table('barangs')
            ->whereNotNull('sumber')
            ->where('sumber', '!=', '')
            ->distinct()
            ->pluck('sumber');

        foreach ($oldValues as $nama) {
            $sumber = Sumber::firstOrCreate(['nama_sumber' => $nama]);
            DB::table('barangs')
                ->where('sumber', $nama)
                ->update(['sumber_id' => $sumber->id]);
        }

        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn('sumber');
        });
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->string('sumber')->nullable();
        });

        $rows = DB::table('barangs')
            ->whereNotNull('sumber_id')
            ->join('sumbers', 'sumbers.id', '=', 'barangs.sumber_id')
            ->select('barangs.id', 'sumbers.nama_sumber')
            ->get();

        foreach ($rows as $row) {
            DB::table('barangs')->where('id', $row->id)->update(['sumber' => $row->nama_sumber]);
        }

        Schema::table('barangs', function (Blueprint $table) {
            $table->dropForeign(['sumber_id']);
            $table->dropColumn('sumber_id');
        });
    }
};