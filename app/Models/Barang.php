<?php

namespace App\Models;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

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

    public static function kodeBarangBase($lokasi, $kategori, $namaBarang = '')
    {
        $lokasiKode = $lokasi instanceof Lokasi ? $lokasi->kode : Lokasi::find($lokasi)?->kode ?? 'XX';
        $kategoriKode = $kategori instanceof Kategori ? $kategori->kode : Kategori::find($kategori)?->kode ?? 'XXX';

        return "{$lokasiKode}-{$kategoriKode}-" . static::initials($namaBarang);
    }

    public static function generateKodeBarang($lokasi, $kategori, $namaBarang = '')
    {
        $baseKode = static::kodeBarangBase($lokasi, $kategori, $namaBarang);
        $kode = $baseKode;
        $counter = 2;
        while (static::where('kode_barang', $kode)->exists()) {
            $kode = "{$baseKode}-{$counter}";
            $counter++;
        }

        return $kode;
    }

    public static function createBarangUnique(array $data, ?string $customKode = null): Barang
    {
        $attempt = 0;

        while (true) {
            $attempt++;

            if ($customKode && $attempt === 1) {
                $kode = $customKode;
            } else {
                $baseKode = static::kodeBarangBase(
                    $data['lokasi_id'] ?? null,
                    $data['kategori_id'] ?? null,
                    $data['nama_barang'] ?? ''
                );
                $kode = $attempt === 1 ? $baseKode : "{$baseKode}-{$attempt}";
            }

            $data['kode_barang'] = $kode;

            try {
                return DB::transaction(fn () => static::query()->create($data));
            } catch (QueryException $e) {
                if (! static::isDuplicateEntry($e) || $attempt >= 50) {
                    throw $e;
                }
            }
        }
    }

    private static function isDuplicateEntry(QueryException $e): bool
    {
        $message = $e->getMessage();

        if (str_contains($message, 'Duplicate entry')) {
            return true;
        }

        if (str_contains($message, 'SQLSTATE[23505]') || str_contains($message, 'duplicate key value violates unique constraint')) {
            return true;
        }

        if (str_contains($message, 'UNIQUE constraint failed')) {
            return true;
        }

        return false;
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
