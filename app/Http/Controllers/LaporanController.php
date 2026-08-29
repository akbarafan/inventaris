<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Models\Sumber;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::all();
        $sumbers = Sumber::all();
        $satuans = Satuan::all();
        $totalBarang = Barang::sum('jumlah');
        $totalKategori = Kategori::count();
        return view('laporan.index', compact('kategoris', 'sumbers', 'satuans', 'totalBarang', 'totalKategori'));
    }

    public function exportBarang(Request $request)
    {
        $request->validate([
            'kondisi' => 'nullable|in:baik,rusak,rusak_berat',
            'kategori_id' => 'nullable|integer|exists:kategoris,id',
            'sumber_id' => 'nullable|integer|exists:sumbers,id',
            'satuan_id' => 'nullable|integer|exists:satuans,id',
            'start_date' => 'nullable|date|before_or_equal:today',
            'end_date' => 'nullable|date|before_or_equal:today|after_or_equal:start_date',
        ]);

        $kondisi = $request->input('kondisi');
        $kategoriId = $request->input('kategori_id');
        $sumberId = $request->input('sumber_id');
        $satuanId = $request->input('satuan_id');
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        $query = Barang::with('kategori', 'sumber', 'lokasi', 'satuan');

        if ($kondisi && in_array($kondisi, ['baik', 'rusak', 'rusak_berat'])) {
            $query->where($kondisi, '>', 0);
        }

        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }

        if ($sumberId) {
            $query->where('sumber_id', $sumberId);
        }

        if ($satuanId) {
            $query->where('satuan_id', $satuanId);
        }

        if ($start) {
            $query->whereDate('tanggal_masuk', '>=', $start);
        }

        if ($end) {
            $query->whereDate('tanggal_masuk', '<=', $end);
        }

        $barangs = $query->cursor();

        $headings = [
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Lokasi',
            'Sumber',
            'Satuan',
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
                    $barang->sumber?->nama_sumber ?? '-',
                    $barang->satuan?->nama_satuan ?? '-',
                    $barang->tanggal_masuk ?? '-',
                    $barang->jumlah . ' ' . ($barang->satuan?->nama_satuan ?? ''),
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
