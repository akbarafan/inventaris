<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use Illuminate\Database\Seeder;

class LokasiSeeder extends Seeder
{
    public function run(): void
    {
        $lokasis = [
            'Ruang Kelas',
            'Lab Komputer',
            'Perpustakaan',
            'Ruang Guru',
            'Gudang',
            'Lab IPA',
        ];

        foreach ($lokasis as $nama) {
            Lokasi::create(['nama_lokasi' => $nama]);
        }
    }
}
