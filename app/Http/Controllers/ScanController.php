<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\ActivityLog;
use App\Models\ScanLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{
    public function index()
    {
        $logs = ScanLog::with('barang', 'user')->latest()->limit(100)->get();
        return view('scan.index', compact('logs'));
    }

    public function scan($kode)
    {
        $barang = Barang::with('kategori', 'sumber', 'barangLokasis.lokasi')
            ->where('kode_barang', $kode)
            ->firstOrFail();

        if (Auth::check()) {
            $scanLog = ScanLog::create([
                'barang_id' => $barang->id,
                'kode_barang' => $barang->kode_barang,
                'user_id' => Auth::id(),
                'device' => request()->userAgent(),
                'ip_address' => request()->ip(),
            ]);
            ActivityLog::log('scan', 'Scan QR: ' . $barang->nama_barang, $barang, [
                'kode' => $barang->kode_barang,
            ]);
            return view('scan.result', compact('barang', 'scanLog'));
        }

        return redirect(url('/b/' . $barang->kode_barang));
    }

}
