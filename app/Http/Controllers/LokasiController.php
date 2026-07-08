<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangLokasi;
use App\Models\Lokasi;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    public function index()
    {
        $lokasis = Lokasi::withCount('barangLokasis')->latest()->get();
        return view('lokasi.index', compact('lokasis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:255|unique:lokasis,nama_lokasi',
            'kode' => 'nullable|string|max:10|unique:lokasis,kode',
        ]);

        $lokasi = Lokasi::create($validated);

        return response()->json(['success' => true, 'data' => $lokasi, 'message' => 'Lokasi berhasil ditambahkan!']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_lokasi' => 'required|string|max:255|unique:lokasis,nama_lokasi,' . $id,
            'kode' => 'nullable|string|max:10|unique:lokasis,kode,' . $id,
        ]);

        $lokasi = Lokasi::findOrFail($id);
        $oldKode = $lokasi->kode;
        $lokasi->update($validated);

        if ($lokasi->kode !== $oldKode) {
            $barangs = Barang::with('kategori')->where('lokasi_id', $lokasi->id)->get();
            foreach ($barangs as $barang) {
                $katKode = $barang->kategori?->kode ?? 'XXX';
                $initials = Barang::initials($barang->nama_barang);
                $baseKode = $lokasi->kode . '-' . $katKode . '-' . $initials;
                $kode = $baseKode;
                $counter = 2;
                while (Barang::where('kode_barang', $kode)->where('id', '!=', $barang->id)->exists()) {
                    $kode = $baseKode . '-' . $counter++;
                }
                $barang->kode_barang = $kode;
                $barang->save();
            }
        }

        return response()->json(['success' => true, 'data' => $lokasi, 'message' => 'Lokasi berhasil diperbarui!']);
    }

    public function show($id)
    {
        $lokasi = Lokasi::with('barangLokasis.barang')->findOrFail($id);
        return response()->json($lokasi);
    }

    public function edit($id)
    {
        $lokasi = Lokasi::findOrFail($id);
        return response()->json($lokasi);
    }

    public function destroy($id)
    {
        $lokasi = Lokasi::findOrFail($id);

        if ($lokasi->barangLokasis()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Lokasi tidak bisa dihapus karena masih memiliki barang.'], 422);
        }

        $lokasi->delete();

        return response()->json(['success' => true, 'message' => 'Lokasi berhasil dihapus!']);
    }

}
