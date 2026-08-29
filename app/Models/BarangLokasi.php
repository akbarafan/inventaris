<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangLokasi extends Model
{
    protected $table = 'barang_lokasis';

    protected $fillable = [
        'barang_id', 'lokasi_id', 'jumlah', 'baik', 'rusak', 'rusak_berat',
    ];

    protected $casts = [
        'barang_id' => 'integer',
        'lokasi_id' => 'integer',
        'jumlah' => 'integer',
        'baik' => 'integer',
        'rusak' => 'integer',
        'rusak_berat' => 'integer',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }
}
