<?php

namespace Database\Seeders;

use App\Models\Satuan;
use Illuminate\Database\Seeder;

class SatuanSeeder extends Seeder
{
    public function run(): void
    {
        $satuans = [
            'pcs',
            'pack',
            'dus',
            'lusin',
            'kodi',
            'rim',
            'gross',
            'set',
            'lembar',
            'roll',
            'meter',
            'karung',
            'kaleng',
            'botol',
            'batang',
        ];

        foreach ($satuans as $nama) {
            Satuan::firstOrCreate(['nama_satuan' => $nama]);
        }
    }
}
