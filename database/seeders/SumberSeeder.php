<?php

namespace Database\Seeders;

use App\Models\Sumber;
use Illuminate\Database\Seeder;

class SumberSeeder extends Seeder
{
    public function run(): void
    {
        $sumbers = [
            'BOS',
            'APBD',
            'Swadaya',
            'Hibah',
            'Lainnya',
        ];

        foreach ($sumbers as $nama) {
            Sumber::firstOrCreate(['nama_sumber' => $nama]);
        }
    }
}