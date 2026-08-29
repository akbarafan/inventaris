<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    protected $fillable = ['nama_satuan', 'kode'];

    protected $casts = [
        'id' => 'integer',
    ];

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'satuan_id');
    }

    public static function booted()
    {
        static::creating(function ($satuan) {
            if (empty($satuan->kode)) {
                $satuan->kode = strtoupper(substr($satuan->nama_satuan, 0, 3));
            }
        });
    }
}
