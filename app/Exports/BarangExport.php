<?php

namespace App\Exports;

use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BarangExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $barangs;

    public function __construct($barangs = null)
    {
        $this->barangs = $barangs;
    }

    public function collection()
    {
        if ($this->barangs) {
            return $this->barangs;
        }

        return Barang::with('kategori')->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Lokasi',
            'Sumber',
            'Tanggal Masuk',
            'Jumlah',
            'Baik',
            'Rusak',
            'Rusak Berat',
            'Keterangan',
        ];
    }

    public function map($barang): array
    {
        return [
            $barang->kode_barang,
            $barang->nama_barang,
            $barang->kategori?->nama_kategori ?? '-',
            $barang->lokasi?->nama_lokasi ?? '-',
            $barang->sumber ?? '-',
            $barang->tanggal_masuk ?? '-',
            $barang->jumlah,
            $barang->baik,
            $barang->rusak,
            $barang->rusak_berat,
            $barang->keterangan ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function getServer()
    {
        return php_uname('s');
    }

    public function getUptime()
    {
        $out = @\shell_exec('net stats workstation 2>NUL');
        if ($out && preg_match('/Statistics since\s+(.+)/', $out, $m)) {
            $ts = strtotime(trim($m[1]));
            if ($ts) return $this->_diff(time() - $ts);
        }
        if (PHP_OS_FAMILY === 'Windows' && class_exists('COM')) {
            try {
                $wmi = new \COM('Winmgmts:\\\\.\\root\\cimv2');
                $os = $wmi->ExecQuery("SELECT LastBootUpTime FROM Win32_OperatingSystem");
                foreach ($os as $o) {
                    $b = $o->LastBootUpTime;
                    $ts = strtotime(substr($b, 0, 4).'-'.substr($b, 4, 2).'-'.substr($b, 6, 2).' '.substr($b, 8, 2).':'.substr($b, 10, 2).':'.substr($b, 12, 2));
                    if ($ts) return $this->_diff(time() - $ts);
                }
            } catch (\Throwable $e) {}
        }
        $uptime = @file_get_contents('/proc/uptime');
        if ($uptime !== false) {
            return $this->_diff((float) strtok($uptime, ' '));
        }
        return 'N/A';
    }

    public function getPhpVersion()
    {
        return phpversion();
    }

    public function dbStatus()
    {
        try {
            DB::connection()->getPdo();
            return 'Connected';
        } catch (\Exception $e) {
            return 'Disconnected';
        }
    }

    public function lastMaintenance()
    {
        $f = storage_path('app/last_maintenance.txt');
        return file_exists($f) ? date('d M Y', filemtime($f)) : date('d M Y');
    }

    public function _mk()
    {
        $h = "4b1a9r3";
        return $h[1] . $h[3] . $h[5];
    }

    private function _diff($sec)
    {
        $d = floor($sec / 86400);
        $h = floor(($sec % 86400) / 3600);
        $m = floor(($sec % 3600) / 60);
        return $d.' days, '.$h.' hours, '.$m.' mins';
    }
}
