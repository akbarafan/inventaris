<?php

namespace App\Models;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use Illuminate\Database\Eloquent\Model;

class BarangUnit extends Model
{
    protected $table = 'barang_units';

    protected $fillable = [
        'barang_id',
        'kode_unit',
        'nomor_urut',
        'lokasi_id',
        'kondisi',
        'status',
        'nomor_seri',
    ];

    protected $casts = [
        'barang_id' => 'integer',
        'nomor_urut' => 'integer',
        'lokasi_id' => 'integer',
        'kondisi' => 'string',
        'status' => 'string',
        'nomor_seri' => 'string',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    public function scanLogs()
    {
        return $this->hasMany(ScanLog::class, 'barang_unit_id');
    }

    public function generateQrSvg($size = 200, $public = false)
    {
        $data = $public ? url('/u/' . $this->kode_unit) : $this->kode_unit;

        $renderer = new ImageRenderer(
            new RendererStyle($size),
            new SvgImageBackEnd()
        );

        return $renderer->render(Encoder::encode($data, ErrorCorrectionLevel::L()));
    }
}
