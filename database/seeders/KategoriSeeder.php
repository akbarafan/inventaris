<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategoris = [
            'Elektronik',
            'Furniture',
            'Alat Peraga',
            'Buku',
            'Olahraga',
            'Lab Komputer',
            'Kesenian',
            'Perlengkapan Kantor',
        ];

        foreach ($kategoris as $nama) {
            Kategori::create(['nama_kategori' => $nama]);
        }
    }
}
