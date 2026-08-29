<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sumber extends Model
{
    protected $fillable = ['nama_sumber', 'kode'];

    protected $casts = [
        'id' => 'integer',
    ];

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'sumber_id');
    }

    public static function booted()
    {
        static::creating(function ($sumber) {
            if (empty($sumber->kode)) {
                $sumber->kode = strtoupper(substr($sumber->nama_sumber, 0, 3));
            }
        });
    }
}