<?php

namespace App\Console\Commands;

use App\Models\Barang;
use App\Models\BarangLokasi;
use App\Models\Kategori;
use App\Models\Lokasi;
use Illuminate\Console\Command;

class MigrateBarangKode extends Command
{
    protected $signature = 'barang:migrate-kode';
    protected $description = 'Migrasi semua kode barang BRG-XXXX ke format {LOKASI}-{KATEGORI}-{RANDOM}';

    public function handle()
    {
        $this->info('Mengisi kode kategori...');
        foreach (Kategori::whereNull('kode')->get() as $k) {
            $k->kode = strtoupper(substr($k->nama_kategori, 0, 3));
            $k->save();
            $this->line("  {$k->nama_kategori} -> {$k->kode}");
        }

        $this->info('Migrasi kode barang...');
        $total = Barang::count();
        $bar = $this->output->createProgressBar($total);
        $updated = 0;

        foreach (Barang::cursor() as $barang) {
            $lokasiId = $barang->lokasi_id ?? BarangLokasi::where('barang_id', $barang->id)->value('lokasi_id');
            $kategoriId = $barang->kategori_id;

            $lokasiKode = $lokasiId ? Lokasi::find($lokasiId)?->kode : 'XX';
            $kategoriKode = $kategoriId ? Kategori::find($kategoriId)?->kode ?? 'XXX' : 'XXX';

            do {
                $random = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
                $kodeBaru = "{$lokasiKode}-{$kategoriKode}-{$random}";
            } while (Barang::where('kode_barang', $kodeBaru)->exists());

            if (empty($barang->lokasi_id) && $lokasiId) {
                $barang->lokasi_id = $lokasiId;
            }
            $barang->kode_barang = $kodeBaru;
            $barang->save();
            $updated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Selesai! {$updated} barang berhasil dimigrasi.");
    }
}
