<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Barang;

class BarangObserver
{
    public function created(Barang $barang)
    {
        ActivityLog::log('created', 'Menambahkan barang: ' . $barang->nama_barang, $barang, [
            'kode' => $barang->kode_barang,
        ]);
    }

    public function updated(Barang $barang)
    {
        ActivityLog::log('updated', 'Mengubah barang: ' . $barang->nama_barang, $barang, [
            'kode' => $barang->kode_barang,
        ]);
    }

    public function deleted(Barang $barang)
    {
        ActivityLog::log('deleted', 'Menghapus barang: ' . $barang->nama_barang, $barang, [
            'kode' => $barang->kode_barang,
        ]);
    }

    public function restored(Barang $barang)
    {
        ActivityLog::log('restored', 'Memulihkan barang: ' . $barang->nama_barang, $barang, [
            'kode' => $barang->kode_barang,
        ]);
    }
}