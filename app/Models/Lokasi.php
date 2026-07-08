<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $fillable = ['nama_lokasi', 'kode'];

    public function barangLokasis()
    {
        return $this->hasMany(BarangLokasi::class, 'lokasi_id');
    }

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'lokasi_id');
    }

    protected static function booted()
    {
        static::saving(function ($lokasi) {
            if (empty($lokasi->kode)) {
                $words = preg_split('/[\s\-]+/', $lokasi->nama_lokasi);
                $lokasi->kode = count($words) > 1
                    ? strtoupper(implode('', array_map(fn($w) => $w[0], $words)))
                    : strtoupper(substr($lokasi->nama_lokasi, 0, 3));
            }
        });
    }
}
