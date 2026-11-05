<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $barangs = DB::table('barangs')->select('id', 'kode_barang')->get();

        foreach ($barangs as $barang) {
            $clean = trim(preg_replace('/[^A-Za-z0-9\-]/', '', $barang->kode_barang), '-');

            if ($clean === '') {
                $clean = 'X';
            }

            if ($clean === $barang->kode_barang) {
                continue;
            }

            $candidate = $clean;
            $counter = 2;

            while (DB::table('barangs')
                ->where('kode_barang', $candidate)
                ->where('id', '!=', $barang->id)
                ->exists()) {
                $candidate = $clean . '-' . $counter;
                $counter++;
            }

            DB::transaction(function () use ($barang, $candidate) {
                DB::table('barangs')
                    ->where('id', $barang->id)
                    ->update(['kode_barang' => $candidate]);

                $units = DB::table('barang_units')
                    ->where('barang_id', $barang->id)
                    ->get(['id', 'kode_unit']);

                foreach ($units as $unit) {
                    $newKode = str_replace($barang->kode_barang, $candidate, $unit->kode_unit);

                    if (DB::table('barang_units')->where('kode_unit', $newKode)->where('id', '!=', $unit->id)->exists()) {
                        continue;
                    }

                    DB::table('barang_units')
                        ->where('id', $unit->id)
                        ->update(['kode_unit' => $newKode]);
                }
            });
        }
    }

    public function down(): void
    {
        //
    }
};
