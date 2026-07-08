<?php

namespace App\Http\Controllers;

use App\Exports\BarangExport;
use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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

        $query = Barang::with('kategori');

        if ($kondisi && in_array($kondisi, ['baik', 'rusak', 'rusak_berat'])) {
            $query->where($kondisi, '>', 0);
        }

        if ($kategoriId) {
            $query->where('kategori_id', $kategoriId);
        }

        $barangs = $query->get();

        $fileName = 'laporan-barang-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new BarangExport($barangs), $fileName);
    }
}
