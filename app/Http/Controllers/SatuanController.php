<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    public function index()
    {
        $satuans = Satuan::withCount('barangs')->latest()->get();
        return view('satuan.index', compact('satuans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:255|unique:satuans,nama_satuan',
            'kode' => 'nullable|string|max:10|unique:satuans,kode',
        ]);

        $satuan = Satuan::create($validated);

        return response()->json(['success' => true, 'data' => $satuan, 'message' => 'Satuan berhasil ditambahkan!']);
    }

    public function edit($id)
    {
        $satuan = Satuan::findOrFail($id);
        return response()->json($satuan);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:255|unique:satuans,nama_satuan,' . $id,
            'kode' => 'nullable|string|max:10|unique:satuans,kode,' . $id,
        ]);

        $satuan = Satuan::findOrFail($id);
        $satuan->update($validated);

        return response()->json(['success' => true, 'data' => $satuan, 'message' => 'Satuan berhasil diperbarui!']);
    }

    public function destroy($id)
    {
        $satuan = Satuan::findOrFail($id);

        if ($satuan->barangs()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Satuan tidak bisa dihapus karena masih dipakai ' . $satuan->barangs()->count() . ' barang.',
            ], 422);
        }

        $satuan->delete();

        return response()->json(['success' => true, 'message' => 'Satuan berhasil dihapus!']);
    }
}
