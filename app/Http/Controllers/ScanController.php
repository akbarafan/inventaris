<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\ScanLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{
    public function index()
    {
        $logs = ScanLog::with('barang', 'user')->latest()->get();
        return view('scan.index', compact('logs'));
    }

    public function scan($kode)
    {
        $barang = Barang::with('kategori', 'barangLokasis.lokasi')
            ->where('kode_barang', $kode)
            ->firstOrFail();

        $scanLog = ScanLog::create([
            'barang_id' => $barang->id,
            'kode_barang' => $barang->kode_barang,
            'user_id' => Auth::id(),
            'device' => request()->userAgent(),
            'ip_address' => request()->ip(),
        ]);

        return view('scan.result', compact('barang', 'scanLog'));
    }

}
