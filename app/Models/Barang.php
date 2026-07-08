<?php

namespace App\Models;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable = [
        'kode_barang', 'nama_barang', 'kategori_id', 'lokasi_id', 'sumber',
        'foto', 'jumlah', 'baik', 'rusak', 'rusak_berat',
        'keterangan', 'tanggal_masuk',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    public function barangLokasis()
    {
        return $this->hasMany(BarangLokasi::class, 'barang_id');
    }

    public function scanLogs()
    {
        return $this->hasMany(ScanLog::class, 'barang_id');
    }

    public static function generateKodeBarang($lokasi, $kategori, $namaBarang = '')
    {
        $lokasiKode = $lokasi instanceof Lokasi ? $lokasi->kode : Lokasi::find($lokasi)?->kode ?? 'XX';
        $kategoriKode = $kategori instanceof Kategori ? $kategori->kode : Kategori::find($kategori)?->kode ?? 'XXX';
        $initials = static::initials($namaBarang);

        $baseKode = "{$lokasiKode}-{$kategoriKode}-{$initials}";
        $kode = $baseKode;
        $counter = 2;
        while (static::where('kode_barang', $kode)->exists()) {
            $kode = "{$baseKode}-{$counter}";
            $counter++;
        }

        return $kode;
    }

    public function generateQrSvg($size = 200, $public = false)
    {
        $data = $public ? url('/b/' . $this->kode_barang) : $this->kode_barang;
        $renderer = new ImageRenderer(
            new RendererStyle($size),
            new SvgImageBackEnd()
        );
        return $renderer->render(Encoder::encode($data, ErrorCorrectionLevel::L()));
    }

    public static function initials($nama)
    {
        $words = preg_split('/[\s\-]+/', trim($nama));
        $result = '';
        foreach ($words as $w) {
            $first = mb_substr(trim($w), 0, 1);
            if (!empty($first)) {
                $result .= mb_strtoupper($first);
                if (mb_strlen($result) >= 5) break;
            }
        }
        return $result ?: 'X';
    }
}
