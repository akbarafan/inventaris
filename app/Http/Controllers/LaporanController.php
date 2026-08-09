<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::all();
        $totalBarang = Barang::sum('jumlah');
        $totalKategori = Kategori::count();
        return view('laporan.index', compact('kategoris', 'totalBarang', 'totalKategori'));
    }

    public function exportBarang(Request $request)
    {
        $kondisi = $request->input('kondisi');
        $kategoriId = $request->input('kategori_id');
        $sumber = $request->input('sumber');
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        $query = Barang::with('kategori');

        if ($kondisi && in_array($kondisi, ['baik', 'rusak', 'rusak_berat'])) {
            $query->where($kondisi, '>', 0);
        }

        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }

        if ($sumber) {
            $query->where('sumber', $sumber);
        }

        if ($start) {
            $query->whereDate('tanggal_masuk', '>=', $start);
        }

        if ($end) {
            $query->whereDate('tanggal_masuk', '<=', $end);
        }

        $barangs = $query->get();

        $headings = [
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

        $fileName = 'laporan-barang-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($barangs, $headings) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, $headings, ';');

            foreach ($barangs as $barang) {
                fputcsv($out, [
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
                ], ';');
            }

            fclose($out);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
