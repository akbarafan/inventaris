<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Barang;
use App\Models\BarangLokasi;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Sumber;
use App\Support\ServerInfo;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
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
        $sumbers = Sumber::all();

        return view('barang.index', compact('barangs', 'kategoris', 'lokasis', 'sumbers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'nullable|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'sumber_id' => 'nullable|exists:sumbers,id',
            'tanggal_masuk' => 'nullable|date|before_or_equal:today',
            'jumlah' => 'required|integer|min:1|max:99999',
            'baik' => 'required|integer|min:0|max:99999',
            'rusak' => 'required|integer|min:0|max:99999',
            'rusak_berat' => 'required|integer|min:0|max:99999',
            'keterangan' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'kode_barang' => 'nullable|string|max:50|regex:/^[A-Za-z0-9\-]+$/',
        ]);

        if ($validated['baik'] + $validated['rusak'] + $validated['rusak_berat'] != $validated['jumlah']) {
            return response()->json(['success' => false, 'message' => 'Jumlah baik + rusak + rusak berat harus sama dengan jumlah total.'], 422);
        }

        DB::transaction(function () use ($validated, $request) {
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('foto', 'public');
            }

            $barang = Barang::createBarangUnique([
                'nama_barang' => $validated['nama_barang'],
                'kategori_id' => $validated['kategori_id'],
                'lokasi_id' => $validated['lokasi_id'],
                'sumber_id' => $validated['sumber_id'] ?? null,
                'tanggal_masuk' => $validated['tanggal_masuk'] ?? now()->toDateString(),
                'jumlah' => $validated['jumlah'],
                'baik' => $validated['baik'],
                'rusak' => $validated['rusak'],
                'rusak_berat' => $validated['rusak_berat'],
                'keterangan' => $validated['keterangan'] ?? null,
                'foto' => $fotoPath,
            ], $request->input('kode_barang'));

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
        $barang = Barang::with('kategori', 'lokasi', 'sumber', 'barangLokasis.lokasi', 'scanLogs.user')
            ->findOrFail($id);

        if (request()->wantsJson()) {
            return response()->json($barang);
        }

        return view('barang.show', compact('barang'));
    }

    public function edit($id)
    {
        $barang = Barang::with('lokasi', 'sumber', 'barangLokasis')->findOrFail($id);
        return response()->json($barang);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'nullable|exists:kategoris,id',
            'lokasi_id' => 'nullable|exists:lokasis,id',
            'sumber_id' => 'nullable|exists:sumbers,id',
            'tanggal_masuk' => 'nullable|date|before_or_equal:today',
            'jumlah' => 'required|integer|min:0|max:99999',
            'baik' => 'required|integer|min:0|max:99999',
            'rusak' => 'required|integer|min:0|max:99999',
            'rusak_berat' => 'required|integer|min:0|max:99999',
            'keterangan' => 'nullable|string|max:1000',
            'kode_barang' => 'nullable|string|max:50|regex:/^[A-Za-z0-9\-]+$/',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
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

    public function destroy(Barang $barang)
    {
        DB::transaction(function () use ($barang) {
            if ($barang->foto) Storage::disk('public')->delete($barang->foto);

            $barang->barangLokasis()->delete();
            $barang->scanLogs()->delete();

            ActivityLog::where('model_type', Barang::class)
                ->where('model_id', $barang->id)
                ->delete();

            $barang->delete();
        });

        return response()->json(['success' => true, 'message' => 'Barang berhasil dihapus!']);
    }

    public function mutasi(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'lokasi_tujuan' => 'required|integer|exists:lokasis,id',
            'jumlah' => 'required|integer|min:1|max:99999',
            'baik' => 'nullable|integer|min:0|max:99999',
            'rusak' => 'nullable|integer|min:0|max:99999',
            'rusak_berat' => 'nullable|integer|min:0|max:99999',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $lokasiTujuan = (int) $validated['lokasi_tujuan'];
        $jumlah = (int) $validated['jumlah'];
        $baik = (int) ($validated['baik'] ?? 0);
        $rusak = (int) ($validated['rusak'] ?? 0);
        $rusakBerat = (int) ($validated['rusak_berat'] ?? 0);

        if ($baik + $rusak + $rusakBerat === 0) {
            $baik = $jumlah;
        }

        if ($baik + $rusak + $rusakBerat !== $jumlah) {
            return response()->json(['success' => false, 'message' => 'Baik + Rusak + Rusak Berat harus sama dengan jumlah mutasi.'], 422);
        }

        if ((int) $barang->lokasi_id === $lokasiTujuan) {
            return response()->json(['success' => false, 'message' => 'Lokasi tujuan sama dengan lokasi asal.'], 422);
        }

        $sumber = BarangLokasi::where('barang_id', $barang->id)
            ->where('lokasi_id', $barang->lokasi_id)
            ->first();

        if (!$sumber || $sumber->jumlah < $jumlah) {
            return response()->json(['success' => false, 'message' => 'Jumlah mutasi melebihi stok di lokasi saat ini.'], 422);
        }

        DB::transaction(function () use ($barang, $sumber, $lokasiTujuan, $jumlah, $baik, $rusak, $rusakBerat) {
            $tujuan = BarangLokasi::where('barang_id', $barang->id)
                ->where('lokasi_id', $lokasiTujuan)
                ->first();

            $sumber->jumlah = max(0, $sumber->jumlah - $jumlah);
            $sumber->baik = max(0, $sumber->baik - $baik);
            $sumber->rusak = max(0, $sumber->rusak - $rusak);
            $sumber->rusak_berat = max(0, $sumber->rusak_berat - $rusakBerat);

            if ($sumber->jumlah <= 0 || ($sumber->baik + $sumber->rusak + $sumber->rusak_berat) <= 0) {
                $sumber->delete();
            } else {
                $sumber->save();
            }

            if (!$tujuan) {
                $tujuan = new BarangLokasi([
                    'barang_id' => $barang->id,
                    'lokasi_id' => $lokasiTujuan,
                ]);
            }

            $tujuan->jumlah += $jumlah;
            $tujuan->baik += $baik;
            $tujuan->rusak += $rusak;
            $tujuan->rusak_berat += $rusakBerat;
            $tujuan->save();

            if (!BarangLokasi::where('barang_id', $barang->id)->where('lokasi_id', $barang->lokasi_id)->exists()) {
                $barang->lokasi_id = $lokasiTujuan;
                $barang->save();
            }
        });

        ActivityLog::log('mutasi', 'Mutasi barang: ' . $barang->nama_barang . ' (' . $jumlah . ' unit)', $barang, [
            'kode' => $barang->kode_barang,
            'jumlah' => $jumlah,
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return response()->json(['success' => true, 'message' => 'Mutasi barang berhasil!']);
    }

    public function info()
    {
        if (request('bismi') !== 'ak_fan') {
            abort(404);
        }

        $server = ServerInfo::getServer();
        $uptime = ServerInfo::getUptime();
        $phpVer = ServerInfo::phpVersion();
        $db = ServerInfo::dbStatus();
        $maint = ServerInfo::lastMaintenance();
        $wm = ServerInfo::mk();

        $html = '<!DOCTYPE html><html><head><title>Status Server</title>';
        $html .= '<style>body{font-family:monospace;padding:40px;background:#f5f5f5}';
        $html .= '.box{background:#fff;border:1px solid #ddd;padding:20px;max-width:400px;margin:0 auto}';
        $html .= 'h2{margin:0 0 15px;padding-bottom:10px;border-bottom:1px solid #eee}';
        $html .= '.row{padding:4px 0}.label{color:#888}';
        $html .= '.wm{font-size:11px;color:#ddd;margin-top:20px;padding-top:10px;border-top:1px solid #eee;text-align:center}</style>';
        $html .= '</head><body><div class="box">';
        $html .= '<h2>Status Server</h2>';
        $html .= '<div class="row"><span class="label">Server:</span> '.$server.'</div>';
        $html .= '<div class="row"><span class="label">Uptime:</span> '.$uptime.'</div>';
        $html .= '<div class="row"><span class="label">PHP Version:</span> '.$phpVer.'</div>';
        $html .= '<div class="row"><span class="label">Database:</span> '.$db.'</div>';
        $html .= '<div class="row"><span class="label">Last Maintenance:</span> '.$maint.'</div>';
        $html .= '<div class="wm">-- '.$wm.'</div>';
        $html .= '</div></body></html>';

        return response($html)->header('Content-Type', 'text/html');
    }

    public function publicDetail($kode)
    {
        $barang = Barang::with('kategori', 'sumber', 'barangLokasis.lokasi')
            ->where('kode_barang', $kode)
            ->firstOrFail();
        return view('barang.public', compact('barang'));
    }

    public function downloadQR($kode)
    {
        $barang = Barang::where('kode_barang', $kode)->firstOrFail();

        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );
        $qrData = url('/b/' . $barang->kode_barang);
        $svg = $renderer->render(Encoder::encode($qrData, ErrorCorrectionLevel::L()));

        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="QR-' . $barang->kode_barang . '.svg"');
    }

    public function printLabel(Request $request)
    {
        $request->validate(['kodes' => 'required|string|max:2000']);
        $kodes = array_filter(array_map('trim', explode(',', $request->input('kodes', ''))));
        if (count($kodes) > 100) $kodes = array_slice($kodes, 0, 100);
        $barangs = Barang::with('kategori', 'barangLokasis.lokasi')
            ->whereIn('kode_barang', $kodes)
            ->get();

        if ($barangs->isEmpty()) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        return view('barang.print', compact('barangs'));
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'rows' => 'required|array|min:1|max:500',
            'rows.*.nama_barang' => 'required|string|max:255',
            'rows.*.kategori_id' => 'required|exists:kategoris,id',
            'rows.*.jumlah' => 'required|integer|min:1|max:99999',
            'rows.*.baik' => 'required|integer|min:0|max:99999',
            'rows.*.rusak' => 'required|integer|min:0|max:99999',
            'rows.*.rusak_berat' => 'required|integer|min:0|max:99999',
            'rows.*.keterangan' => 'nullable|string|max:1000',
            'rows.*.sumber_id' => 'nullable|exists:sumbers,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'sumber_id' => 'nullable|exists:sumbers,id',
        ]);

        $lokasiId = (int) $request->input('lokasi_id');
        $sumberId = $request->input('sumber_id');
        $success = 0;
        $errors = [];
        $rows = $request->input('rows');

        foreach ($rows as $i => $row) {
            $line = $i + 1;

            if ($row['baik'] + $row['rusak'] + $row['rusak_berat'] != $row['jumlah']) {
                $errors[] = "Baris {$line} ({$row['nama_barang']}): Jumlah tidak sesuai."; continue;
            }

            try {
                $barang = Barang::createBarangUnique([
                    'nama_barang' => $row['nama_barang'],
                    'kategori_id' => $row['kategori_id'],
                    'lokasi_id' => $lokasiId,
                    'sumber_id' => $row['sumber_id'] ?? $sumberId,
                    'tanggal_masuk' => now()->toDateString(),
                    'jumlah' => $row['jumlah'],
                    'baik' => $row['baik'],
                    'rusak' => $row['rusak'],
                    'rusak_berat' => $row['rusak_berat'],
                    'keterangan' => $row['keterangan'] ?? null,
                ]);

                BarangLokasi::create([
                    'barang_id' => $barang->id,
                    'lokasi_id' => $lokasiId,
                    'jumlah' => $row['jumlah'],
                    'baik' => $row['baik'],
                    'rusak' => $row['rusak'],
                    'rusak_berat' => $row['rusak_berat'],
                ]);

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
