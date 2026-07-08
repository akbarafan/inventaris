<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangLokasi;
use App\Models\Kategori;
use App\Models\Lokasi;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class BarangController extends Controller
{
    public function index(Request $request)
    {
        $barangs = Barang::with('kategori', 'lokasi')
            ->orderBy('lokasis.nama_lokasi')
            ->orderBy('kategoris.nama_kategori')
            ->orderBy('barangs.nama_barang')
            ->leftJoin('lokasis', 'barangs.lokasi_id', '=', 'lokasis.id')
            ->leftJoin('kategoris', 'barangs.kategori_id', '=', 'kategoris.id')
            ->select('barangs.*')
            ->get();

        $kategoris = Kategori::all();
        $lokasis = Lokasi::all();

        return view('barang.index', compact('barangs', 'kategoris', 'lokasis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'nullable|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'sumber' => 'nullable|string|max:255',
            'tanggal_masuk' => 'nullable|date',
            'jumlah' => 'required|integer|min:1',
            'baik' => 'required|integer|min:0',
            'rusak' => 'required|integer|min:0',
            'rusak_berat' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        if ($validated['baik'] + $validated['rusak'] + $validated['rusak_berat'] != $validated['jumlah']) {
            return response()->json(['success' => false, 'message' => 'Jumlah baik + rusak + rusak berat harus sama dengan jumlah total.'], 422);
        }

        DB::transaction(function () use ($validated, $request) {
            $kodeBarang = $request->input('kode_barang');
            if (!$kodeBarang || Barang::where('kode_barang', $kodeBarang)->exists()) {
                $kodeBarang = Barang::generateKodeBarang($validated['lokasi_id'], $validated['kategori_id'], $validated['nama_barang']);
            }

            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('foto', 'public');
            }

            $barang = Barang::create([
                'kode_barang' => $kodeBarang,
                'nama_barang' => $validated['nama_barang'],
                'kategori_id' => $validated['kategori_id'],
                'lokasi_id' => $validated['lokasi_id'],
                'sumber' => $validated['sumber'],
                'tanggal_masuk' => $validated['tanggal_masuk'] ?? now()->toDateString(),
                'jumlah' => $validated['jumlah'],
                'baik' => $validated['baik'],
                'rusak' => $validated['rusak'],
                'rusak_berat' => $validated['rusak_berat'],
                'keterangan' => $validated['keterangan'],
                'foto' => $fotoPath,
            ]);

            BarangLokasi::create([
                'barang_id' => $barang->id,
                'lokasi_id' => $validated['lokasi_id'],
                'jumlah' => $validated['jumlah'],
                'baik' => $validated['baik'],
                'rusak' => $validated['rusak'],
                'rusak_berat' => $validated['rusak_berat'],
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Barang berhasil ditambahkan!']);
    }

    public function show($id)
    {
        $barang = Barang::with('kategori', 'lokasi', 'barangLokasis.lokasi', 'scanLogs.user')
            ->findOrFail($id);

        if (request()->wantsJson()) {
            return response()->json($barang);
        }

        return view('barang.show', compact('barang'));
    }

    public function edit($id)
    {
        $barang = Barang::with('lokasi', 'barangLokasis')->findOrFail($id);
        return response()->json($barang);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'nullable|exists:kategoris,id',
            'lokasi_id' => 'nullable|exists:lokasis,id',
            'sumber' => 'nullable|string|max:255',
            'tanggal_masuk' => 'nullable|date',
            'jumlah' => 'required|integer|min:0',
            'baik' => 'required|integer|min:0',
            'rusak' => 'required|integer|min:0',
            'rusak_berat' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
            'kode_barang' => 'nullable|string|max:50',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
        ]);

        if ($validated['baik'] + $validated['rusak'] + $validated['rusak_berat'] != $validated['jumlah']) {
            return response()->json(['success' => false, 'message' => 'Jumlah baik + rusak + rusak berat harus sama dengan jumlah total.'], 422);
        }

        $barang = Barang::findOrFail($id);

        if ($request->filled('kode_barang') && $request->kode_barang !== $barang->kode_barang) {
            $existing = Barang::where('kode_barang', $request->kode_barang)
                ->where('id', '!=', $id)
                ->exists();
            if ($existing) {
                return response()->json(['success' => false, 'message' => 'Kode barang sudah digunakan.'], 422);
            }
            $barang->kode_barang = $request->kode_barang;
        }

        if ($request->hasFile('foto')) {
            if ($barang->foto) Storage::disk('public')->delete($barang->foto);
            $validated['foto'] = $request->file('foto')->store('foto', 'public');
        }

        $barang->fill($validated);
        $barang->save();

        return response()->json(['success' => true, 'message' => 'Barang berhasil diperbarui!']);
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        if ($barang->foto) Storage::disk('public')->delete($barang->foto);
        $barang->delete();
        return response()->json(['success' => true, 'message' => 'Barang berhasil dihapus!']);
    }

    public function downloadQR($kode)
    {
        $barang = Barang::where('kode_barang', $kode)->firstOrFail();

        ob_clean();
        $options = new QROptions(['outputType' => 'svg', 'scale' => 10]);
        $qrcode = new QRCode($options);
        $svg = $qrcode->render($barang->kode_barang);

        return response($svg)
            ->header('Content-Type', 'image/svg+xml; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="QR-' . $barang->kode_barang . '.svg"');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'rows' => 'required|array|min:1',
            'rows.*.nama_barang' => 'required|string|max:255',
            'rows.*.kategori_id' => 'required|exists:kategoris,id',
            'rows.*.jumlah' => 'required|integer|min:1',
            'rows.*.baik' => 'required|integer|min:0',
            'rows.*.rusak' => 'required|integer|min:0',
            'rows.*.rusak_berat' => 'required|integer|min:0',
            'rows.*.keterangan' => 'nullable|string',
            'ruang' => 'nullable|string|max:255',
            'sumber' => 'nullable|string|max:255',
        ]);

        $lokasi = null;
        if ($request->filled('ruang')) {
            $lokasi = Lokasi::firstOrCreate(['nama_lokasi' => $request->ruang]);
        }

        $sumber = $request->input('sumber');
        $success = 0;
        $errors = [];
        $rows = $request->input('rows');

        foreach ($rows as $i => $row) {
            $line = $i + 1;

            if ($row['baik'] + $row['rusak'] + $row['rusak_berat'] != $row['jumlah']) {
                $errors[] = "Baris {$line} ({$row['nama_barang']}): Jumlah tidak sesuai."; continue;
            }

            try {
                $lokasiId = $lokasi?->id;
                $kodeBarang = Barang::generateKodeBarang($lokasiId, $row['kategori_id'], $row['nama_barang']);

                $barang = Barang::create([
                    'kode_barang' => $kodeBarang,
                    'nama_barang' => $row['nama_barang'],
                    'kategori_id' => $row['kategori_id'],
                    'lokasi_id' => $lokasiId,
                    'sumber' => $sumber,
                    'tanggal_masuk' => now()->toDateString(),
                    'jumlah' => $row['jumlah'],
                    'baik' => $row['baik'],
                    'rusak' => $row['rusak'],
                    'rusak_berat' => $row['rusak_berat'],
                    'keterangan' => $row['keterangan'] ?? null,
                ]);

                if ($lokasiId) {
                    BarangLokasi::create([
                        'barang_id' => $barang->id,
                        'lokasi_id' => $lokasiId,
                        'jumlah' => $row['jumlah'],
                        'baik' => $row['baik'],
                        'rusak' => $row['rusak'],
                        'rusak_berat' => $row['rusak_berat'],
                    ]);
                }

                $success++;
            } catch (\Exception $e) {
                $errors[] = "Baris {$line} ({$row['nama_barang']}): " . $e->getMessage();
            }
        }

        $message = "Berhasil mengimport {$success} barang.";
        if (count($errors) > 0) {
            $message .= " Gagal: " . count($errors) . " barang.";
        }

        return response()->json(['success' => true, 'message' => $message, 'errors' => $errors]);
    }
}
