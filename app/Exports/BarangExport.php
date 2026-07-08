<?php

namespace App\Exports;

use App\Models\Barang;
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
}
