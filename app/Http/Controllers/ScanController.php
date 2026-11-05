<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\ActivityLog;
use App\Models\BarangUnit;
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
        $unit = BarangUnit::where('kode_unit', $kode)->first();
        $barang = null;

        if ($unit) {
            $barang = Barang::with('kategori', 'sumber', 'satuan', 'barangLokasis.lokasi')
                ->where('id', $unit->barang_id)
                ->firstOrFail();
        } else {
            $barang = Barang::with('kategori', 'sumber', 'satuan', 'barangLokasis.lokasi')
                ->where('kode_barang', $kode)
                ->firstOrFail();
        }

        if (Auth::check()) {
            $scanLog = ScanLog::create([
                'barang_id' => $barang->id,
                'barang_unit_id' => $unit?->id,
                'kode_barang' => $unit?->kode_unit ?? $barang->kode_barang,
                'kode_unit' => $unit?->kode_unit ?? $barang->kode_barang,
                'user_id' => Auth::id(),
                'device' => request()->userAgent(),
                'ip_address' => request()->ip(),
            ]);
            ActivityLog::log('scan', 'Scan QR: ' . $barang->nama_barang, $barang, [
                'kode' => $unit?->kode_unit ?? $barang->kode_barang,
                'unit_id' => $unit?->id,
            ]);
            return view('scan.result', ['barang' => $barang, 'unit' => $unit, 'scanLog' => $scanLog]);
        }

        if ($unit) {
            return redirect(url('/u/' . $unit->kode_unit));
        }

        return redirect(url('/b/' . $barang->kode_barang));
    }

}
