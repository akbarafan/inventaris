<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Barang;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBarang = Barang::sum('jumlah');
        $totalBaik = Barang::sum('baik');
        $totalRusak = Barang::sum('rusak');
        $totalRusakBerat = Barang::sum('rusak_berat');
        $totalJenis = Barang::count();

        $kondisiData = [
            'baik' => (int) $totalBaik,
            'rusak' => (int) $totalRusak,
            'rusak_berat' => (int) $totalRusakBerat,
        ];
        $pctBaik = $totalBarang > 0 ? round($totalBaik / $totalBarang * 100) : 0;

        $perLokasi = Barang::selectRaw('lokasis.nama_lokasi as nama, lokasis.kode as kode, COALESCE(SUM(barangs.jumlah),0) as total')
            ->leftJoin('lokasis', 'barangs.lokasi_id', '=', 'lokasis.id')
            ->groupBy('lokasis.id', 'lokasis.nama_lokasi', 'lokasis.kode')
            ->orderByDesc('total')
            ->get()
            ->filter(fn ($r) => $r->nama)
            ->values();

        $perluPerhatian = Barang::with('lokasi', 'kategori')
            ->where(function ($q) { $q->where('rusak', '>', 0)->orWhere('rusak_berat', '>', 0); })
            ->orderByDesc('rusak_berat')
            ->orderByDesc('rusak')
            ->take(6)
            ->get();

        $barangTerbaru = Barang::with('kategori', 'lokasi')->latest()->take(5)->get();
        $aktivitasTerbaru = ActivityLog::with('user')->latest()->take(8)->get();

        return view('dashboard.index', compact(
            'totalBarang', 'totalBaik', 'totalRusak', 'totalRusakBerat',
            'totalJenis', 'kondisiData', 'pctBaik',
            'perLokasi', 'perluPerhatian',
            'barangTerbaru', 'aktivitasTerbaru'
        ));
    }
}
