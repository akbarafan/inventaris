<?php

use App\Models\Satuan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const MERGE = [
        'pack' => ['bendel', 'bendle', 'bundle', 'pak'],
        'pcs' => ['buah', 'biji', 'bh', 'unit'],
        'dus' => ['box', 'kotak', 'kardus', 'karton'],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('satuans')) {
            return;
        }

        foreach (self::MERGE as $canonical => $synonyms) {
            $target = Satuan::firstOrCreate(['nama_satuan' => $canonical]);

            foreach ($synonyms as $synonym) {
                $old = Satuan::where('nama_satuan', $synonym)->first();
                if (! $old) {
                    continue;
                }

                DB::table('barangs')->where('satuan_id', $old->id)->update(['satuan_id' => $target->id]);
                DB::table('barang_lokasis')->where('satuan_id', $old->id)->update(['satuan_id' => $target->id]);
                $old->delete();
            }
        }
    }

    public function down(): void
    {
        // Non-destructive: konsolidasi tidak dibalik otomatis.
    }
};