@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('barang.index') }}" class="btn-secondary text-sm px-4 py-2">
            <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Barang</h2>
            <p class="text-sm text-gray-500 mt-1">Informasi lengkap barang inventaris</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $barang->nama_barang }}</h3>
                    <span class="text-xs font-mono bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ $barang->kode_barang }}</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 text-xs block">Kategori</span>
                        <p class="font-medium text-gray-800">{{ $barang->kategori->nama_kategori ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-xs block">Sumber</span>
                        <p class="font-medium text-gray-800">{{ $barang->sumber ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-xs block">Jumlah Total</span>
                        <p class="font-semibold text-gray-800 text-lg">{{ $barang->jumlah }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-xs block">Tanggal Masuk</span>
                        <p class="font-medium text-gray-800">{{ $barang->tanggal_masuk ? $barang->tanggal_masuk->format('d/m/Y') : '-' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-gray-500 text-xs block">Keterangan</span>
                        <p class="font-medium text-gray-800">{{ $barang->keterangan ?? '-' }}</p>
                    </div>
                </div>
                <div class="border-t border-gray-100 mt-4 pt-4">
                    <span class="text-sm font-medium text-gray-700 block mb-2">Rincian Kondisi</span>
                    <div class="flex flex-wrap gap-2">
                        <span class="badge-baik text-sm px-3 py-1">Baik: {{ $barang->baik }}</span>
                        <span class="badge-rusak text-sm px-3 py-1">Rusak: {{ $barang->rusak }}</span>
                        <span class="badge-rusakberat text-sm px-3 py-1">Rusak Berat: {{ $barang->rusak_berat }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Distribusi Lokasi</h3>
                @if($barang->barangLokasis->count() > 0)
                <div class="space-y-2">
                    @foreach($barang->barangLokasis as $bl)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="font-medium text-gray-700">{{ $bl->lokasi->nama_lokasi ?? '-' }}</span>
                        <span class="text-sm text-gray-600">{{ $bl->jumlah }} unit (B:{{ $bl->baik }}, R:{{ $bl->rusak }}, RB:{{ $bl->rusak_berat }})</span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-400">Belum ada data lokasi</p>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Riwayat Scan</h3>
                @if($barang->scanLogs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-left">
                                <th class="px-3 py-2 font-medium">Tanggal</th>
                                <th class="px-3 py-2 font-medium">Device</th>
                                <th class="px-3 py-2 font-medium">User</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($barang->scanLogs as $s)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 text-xs">{{ $s->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-3 py-2 text-xs">{{ Str::limit($s->device ?? '-', 30) }}</td>
                                <td class="px-3 py-2 text-xs">{{ $s->user->name ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-sm text-gray-400">Belum ada scan</p>
                @endif
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center sticky top-24">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">QR Code</h3>
                {!! $barang->generateQrSvg(200, true) !!}
                <p class="text-xs text-gray-400 mt-2 font-mono">{{ $barang->kode_barang }}</p>
                <div class="mt-4 space-y-2">
                    <a href="{{ route('barang.qr', $barang->kode_barang) }}" class="btn-outline text-sm px-4 py-2 w-full block text-center">Download QR</a>
                    <a href="{{ route('barang.edit', $barang->id) }}" class="btn-primary text-sm px-4 py-2 w-full block text-center">Edit Barang</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
