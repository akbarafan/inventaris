<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\BarangUnit;
use App\Models\BarangLokasi;

class BarangUnitService
{
    public static function generateKodeUnit(Barang $barang, int $nomorUrut): string
    {
        return strtoupper($barang->kode_barang . '-' . str_pad((string) $nomorUrut, 4, '0', STR_PAD_LEFT));
    }

    public static function createUnits(Barang $barang, int $jumlah, ?int $lokasiId = null, array $kondisi = []): array
    {
        $lokasiId ??= $barang->lokasi_id;
        if (!$lokasiId) {
            return [];
        }

        $units = [];
        $nomorUrut = 1;
        $conditions = [
            'baik' => (int) ($kondisi['baik'] ?? 0),
            'rusak' => (int) ($kondisi['rusak'] ?? 0),
            'rusak_berat' => (int) ($kondisi['rusak_berat'] ?? 0),
        ];

        foreach ($conditions as $condition => $count) {
            for ($i = 0; $i < max(0, $count); $i++) {
                $unit = new BarangUnit();
                $unit->barang_id = $barang->id;
                $unit->kode_unit = self::generateKodeUnit($barang, $nomorUrut);
                $unit->nomor_urut = $nomorUrut++;
                $unit->lokasi_id = $lokasiId;
                $unit->kondisi = $condition;
                $unit->status = 'aktif';
                $unit->nomor_seri = null;
                $unit->save();

                $units[] = $unit;
            }
        }

        return $units;
    }

    public static function convertToUnit(Barang $barang): array
    {
        $barangsLokasis = BarangLokasi::where('barang_id', $barang->id)->get();
        $units = [];
        $nomorUrut = 1;

        if ($barangsLokasis->isNotEmpty()) {
            foreach ($barangsLokasis as $barangLokasi) {
                $conditions = [
                    'baik' => (int) $barangLokasi->baik,
                    'rusak' => (int) $barangLokasi->rusak,
                    'rusak_berat' => (int) $barangLokasi->rusak_berat,
                ];

                foreach ($conditions as $condition => $count) {
                    for ($i = 0; $i < max(0, $count); $i++) {
                        $unit = new BarangUnit();
                        $unit->barang_id = $barang->id;
                        $unit->kode_unit = self::generateKodeUnit($barang, $nomorUrut);
                        $unit->nomor_urut = $nomorUrut++;
                        $unit->lokasi_id = $barangLokasi->lokasi_id;
                        $unit->kondisi = $condition;
                        $unit->status = 'aktif';
                        $unit->nomor_seri = null;
                        $unit->save();

                        $units[] = $unit;
                    }
                }
            }
        } else {
            $units = self::createUnits($barang, (int) $barang->jumlah, null, [
                'baik' => (int) $barang->baik,
                'rusak' => (int) $barang->rusak,
                'rusak_berat' => (int) $barang->rusak_berat,
            ]);
        }

        $barang->tracking_mode = 'unit';
        $barang->save();

        return $units;
    }

    public static function syncAggregatesFromUnits(Barang $barang): void
    {
        $units = BarangUnit::where('barang_id', $barang->id)
            ->where('status', 'aktif')
            ->get();

        $total = 0;
        $baik = 0;
        $rusak = 0;
        $rusakBerat = 0;
        $byLokasi = [];

        foreach ($units as $unit) {
            $total++;
            if ($unit->kondisi === 'baik') {
                $baik++;
            } elseif ($unit->kondisi === 'rusak') {
                $rusak++;
            } elseif ($unit->kondisi === 'rusak_berat') {
                $rusakBerat++;
            }

            if ($unit->lokasi_id) {
                $lokasiId = (int) $unit->lokasi_id;
                $byLokasi[$lokasiId] ??= [
                    'jumlah' => 0,
                    'baik' => 0,
                    'rusak' => 0,
                    'rusak_berat' => 0,
                ];

                $byLokasi[$lokasiId]['jumlah']++;
                if ($unit->kondisi === 'baik') {
                    $byLokasi[$lokasiId]['baik']++;
                } elseif ($unit->kondisi === 'rusak') {
                    $byLokasi[$lokasiId]['rusak']++;
                } elseif ($unit->kondisi === 'rusak_berat') {
                    $byLokasi[$lokasiId]['rusak_berat']++;
                }
            }
        }

        $barang->jumlah = $total;
        $barang->baik = $baik;
        $barang->rusak = $rusak;
        $barang->rusak_berat = $rusakBerat;
        $barang->save();

        $existing = BarangLokasi::where('barang_id', $barang->id)
            ->get()
            ->keyBy('lokasi_id');

        foreach ($byLokasi as $lokasiId => $data) {
            $barangLokasi = $existing->get($lokasiId);
            if (!$barangLokasi) {
                $barangLokasi = new BarangLokasi();
            }

            $barangLokasi->barang_id = $barang->id;
            $barangLokasi->lokasi_id = (int) $lokasiId;
            $barangLokasi->jumlah = $data['jumlah'];
            $barangLokasi->baik = $data['baik'];
            $barangLokasi->rusak = $data['rusak'];
            $barangLokasi->rusak_berat = $data['rusak_berat'];
            $barangLokasi->save();
        }

        foreach ($existing as $barangLokasi) {
            if (!isset($byLokasi[$barangLokasi->lokasi_id])) {
                $barangLokasi->delete();
            }
        }
    }
}
