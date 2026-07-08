<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $fillable = ['nama_kategori', 'kode'];

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'kategori_id');
    }

    public static function booted()
    {
        static::creating(function ($kategori) {
            if (empty($kategori->kode)) {
                $kategori->kode = strtoupper(substr($kategori->nama_kategori, 0, 3));
            }
        });
    }
}
