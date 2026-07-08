<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::withCount('barangs')->latest()->get();
        return view('kategori.index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategoris,nama_kategori',
        ]);

        $kategori = Kategori::create($validated);

        return response()->json(['success' => true, 'data' => $kategori, 'message' => 'Kategori berhasil ditambahkan!']);
    }

    public function edit($id)
    {
        $kategori = Kategori::findOrFail($id);
        return response()->json($kategori);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategoris,nama_kategori,' . $id,
        ]);

        $kategori = Kategori::findOrFail($id);
        $kategori->update($validated);

        return response()->json(['success' => true, 'data' => $kategori, 'message' => 'Kategori berhasil diperbarui!']);
    }

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);

        if ($kategori->barangs()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Kategori tidak bisa dihapus karena masih memiliki barang.'], 422);
        }

        $kategori->delete();

        return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus!']);
    }

}
