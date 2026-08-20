<?php

namespace App\Http\Controllers;

use App\Models\Sumber;
use Illuminate\Http\Request;

class SumberController extends Controller
{
    public function index()
    {
        $sumbers = Sumber::withCount('barangs')->latest()->get();
        return view('sumber.index', compact('sumbers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_sumber' => 'required|string|max:255|unique:sumbers,nama_sumber',
            'kode' => 'nullable|string|max:10|unique:sumbers,kode',
        ]);

        $sumber = Sumber::create($validated);

        return response()->json(['success' => true, 'data' => $sumber, 'message' => 'Sumber berhasil ditambahkan!']);
    }

    public function edit($id)
    {
        $sumber = Sumber::findOrFail($id);
        return response()->json($sumber);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_sumber' => 'required|string|max:255|unique:sumbers,nama_sumber,' . $id,
            'kode' => 'nullable|string|max:10|unique:sumbers,kode,' . $id,
        ]);

        $sumber = Sumber::findOrFail($id);
        $sumber->update($validated);

        return response()->json(['success' => true, 'data' => $sumber, 'message' => 'Sumber berhasil diperbarui!']);
    }

    public function destroy($id)
    {
        $sumber = Sumber::findOrFail($id);

        if ($sumber->barangs()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Sumber tidak bisa dihapus karena masih dipakai ' . $sumber->barangs()->count() . ' barang.',
            ], 422);
        }

        $sumber->delete();

        return response()->json(['success' => true, 'message' => 'Sumber berhasil dihapus!']);
    }

}