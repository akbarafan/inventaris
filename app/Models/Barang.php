<?php

namespace App\Models;

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

    public function generateQrSvg($size = 200)
    {
        $data = $this->kode_barang;
        $qr = $this->buildQrMatrix($data);
        $dim = count($qr);
        $cell = $size / ($dim + 4);
        $offset = $cell * 2;
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $size . ' ' . $size . '" width="' . $size . '" height="' . $size . '"><rect width="' . $size . '" height="' . $size . '" fill="white"/>';
        for ($y = 0; $y < $dim; $y++) {
            for ($x = 0; $x < $dim; $x++) {
                if ($qr[$y][$x]) {
                    $svg .= '<rect x="' . ($offset + $x * $cell) . '" y="' . ($offset + $y * $cell) . '" width="' . ceil($cell) . '" height="' . ceil($cell) . '" fill="black"/>';
                }
            }
        }
        $svg .= '</svg>';
        return $svg;
    }

    private function generateQrMatrix($data)
    {
        $len = strlen($data);
        $bits = [];
        for ($i = 0; $i < $len; $i++) {
            $char = ord($data[$i]);
            for ($j = 0; $j < 8; $j++) {
                $bits[] = ($char >> (7 - $j)) & 1;
            }
        }
        $dim = (int)ceil(sqrt(count($bits)));
        $dim = max($dim, 11);
        $matrix = array_fill(0, $dim, array_fill(0, $dim, 0));
        for ($y = 0; $y < $dim; $y++) {
            for ($x = 0; $x < $dim; $x++) {
                $idx = $y * $dim + $x;
                if ($idx < count($bits)) {
                    $matrix[$y][$x] = $bits[$idx];
                }
            }
        }
        for ($i = 0; $i < 7 && $i < $dim; $i++) {
            for ($j = 0; $j < 7 && $j < $dim; $j++) {
                $matrix[$i][$j] = ($i == 0 || $i == 6 || $j == 0 || $j == 6 || $i == 4 || $j == 4) ? 1 : ($i > 0 && $i < 6 && $j > 0 && $j < 6 ? 0 : $matrix[$i][$j]);
                $matrix[$i][$dim - 1 - $j] = ($i == 0 || $i == 6 || $j == 0 || $j == 6 || $i == 4 || $j == 4) ? 1 : ($i > 0 && $i < 6 && $j > 0 && $j < 6 ? 0 : $matrix[$i][$dim - 1 - $j]);
                $matrix[$dim - 1 - $i][$j] = ($i == 0 || $i == 6 || $j == 0 || $j == 6 || $i == 4 || $j == 4) ? 1 : ($i > 0 && $i < 6 && $j > 0 && $j < 6 ? 0 : $matrix[$dim - 1 - $i][$j]);
            }
        }
        return $matrix;
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
