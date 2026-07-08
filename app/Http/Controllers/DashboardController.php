<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\ScanLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBarang = Barang::sum('jumlah');
        $totalBaik = Barang::sum('baik');
        $totalRusak = Barang::sum('rusak');
        $totalRusakBerat = Barang::sum('rusak_berat');
        $scanHariIni = ScanLog::whereDate('created_at', today())->count();
        $barangHampirHabis = Barang::where('jumlah', '<', 5)->get();
        $barangTerbaru = Barang::latest()->take(5)->get();
        $scanTerbaru = ScanLog::with('barang', 'user')->latest()->take(5)->get();
        $chartRaw = ScanLog::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartData[] = $chartRaw[$date] ?? 0;
        }

        return view('dashboard.index', compact(
            'totalBarang', 'totalBaik', 'totalRusak', 'totalRusakBerat',
            'scanHariIni', 'barangHampirHabis',
            'barangTerbaru', 'scanTerbaru', 'chartData'
        ));
    }
}
