@extends('layouts.app')

@section('title', 'Hasil Scan')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ url('/scan') }}" class="btn-secondary text-sm px-4 py-2">
            <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Hasil Scan</h2>
            <p class="text-sm text-gray-500 mt-1">Detail barang dari hasil scan QR</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Barang Detail --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Informasi Barang</h3>
                    <span class="text-xs font-mono bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ $barang->kode_barang }}</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 text-xs block">Nama Barang</span>
                        <p class="font-medium text-gray-800">{{ $barang->nama_barang }}</p>
                    </div>
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
                        <span class="text-gray-500 text-xs block">Lokasi</span>
                        <p class="font-medium text-gray-800">
                            @foreach($barang->barangLokasis as $bl)
                                <span class="inline-block bg-gray-100 rounded px-2 py-0.5 mr-1 text-xs">{{ $bl->lokasi->nama_lokasi ?? '-' }} ({{ $bl->jumlah }})</span>
                            @endforeach
                        </p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-xs block">Tanggal Masuk</span>
                        <p class="font-medium text-gray-800">{{ $barang->tanggal_masuk ?? '-' }}</p>
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

            {{-- Scan Log Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Informasi Scan</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 text-xs block">Waktu Scan</span>
                        <p class="font-medium text-gray-800">{{ $scanLog->created_at ?? now()->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-xs block">Device</span>
                        <p class="font-medium text-gray-800">{{ $scanLog->device ?? request()->userAgent() }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-xs block">IP Address</span>
                        <p class="font-medium text-gray-800">{{ $scanLog->ip_address ?? request()->ip() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Foto & QR Code --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">Foto Barang</h3>
                <img src="{{ $barang->foto ? asset('storage/' . $barang->foto) : 'https://placehold.co/200x200/e2e8f0/64748b?text=No+Image' }}"
                     alt="Foto {{ $barang->nama_barang }}"
                     class="w-32 h-32 object-cover rounded-lg border border-gray-200 bg-gray-50 mx-auto"
                     onerror="this.src='https://placehold.co/200x200/e2e8f0/64748b?text=No+Image'">
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center sticky top-24">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">QR Code</h3>
                {!! $barang->generateQrSvg(200, true) !!}
                <p class="text-xs text-gray-400 mt-2 font-mono">{{ $barang->kode_barang }}</p>
                <div class="mt-4">
                    <a href="{{ url('/scan') }}" class="btn-secondary text-sm px-4 py-2 w-full block text-center">Scan Lagi</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
