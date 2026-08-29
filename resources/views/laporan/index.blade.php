@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Laporan</h2>
        <p class="text-sm text-gray-500 mt-1">Export data inventaris dalam format CSV (kompatibel Excel)</p>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Filter Laporan</h3>
        <form id="filterForm" method="GET" action="{{ url('/laporan') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="startDate" value="{{ request('start_date') }}" max="{{ now()->format('Y-m-d') }}"
                        class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date" id="endDate" value="{{ request('end_date') }}" max="{{ now()->format('Y-m-d') }}"
                        class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p id="dateError" class="text-xs text-red-500 mt-1 hidden"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kondisi</label>
                    <select name="kondisi" id="filterKondisi" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Kondisi</option>
                        <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak" {{ request('kondisi') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                        <option value="rusak_berat" {{ request('kondisi') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="kategori_id" id="filterKategori" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $k)
                        <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sumber</label>
                    <select name="sumber_id" id="filterSumber" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Sumber</option>
                        @foreach($sumbers ?? [] as $s)
                        <option value="{{ $s->id }}" {{ request('sumber_id') == $s->id ? 'selected' : '' }}>{{ $s->nama_sumber }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button type="submit" class="btn-primary text-sm px-4 py-2">Terapkan</button>
                <a href="{{ url('/laporan') }}" class="btn-secondary text-sm px-4 py-2">Reset</a>
            </div>
        </form>
    </div>

    @if(Auth::user()->isAdmin())
    {{-- Export Buttons --}}
    <div class="grid grid-cols-1 md:grid-cols-1 gap-6 max-w-md">
        <a href="{{ url('/laporan/export-barang?' . http_build_query(request()->query())) }}"
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all hover:border-green-200 group">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition-colors">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-800 group-hover:text-green-600 transition-colors">Export Data Barang</h3>
                    <p class="text-sm text-gray-500 mt-1">CSV - Seluruh data barang inventaris</p>
                </div>
                <svg class="w-5 h-5 text-gray-300 group-hover:text-green-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </div>
        </a>
    </div>
    @endif

    {{-- Quick Summary --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Ringkasan Data</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $totalBarang ?? 0 }}</p>
                <p class="text-blue-800 text-xs mt-1">Total Barang</p>
            </div>
            <div class="bg-amber-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-amber-600">{{ $totalKategori ?? 0 }}</p>
                <p class="text-amber-800 text-xs mt-1">Total Kategori</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-green-600">{{ \App\Models\Lokasi::count() }}</p>
                <p class="text-green-800 text-xs mt-1">Total Lokasi</p>
            </div>
        </div>
        @if(($totalBarang ?? 0) == 0)
        <div class="mt-6 py-8 text-center border-t border-gray-100">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            <p class="text-gray-400 text-sm">Belum ada data barang untuk filter ini</p>
            <a href="{{ url('/laporan') }}" class="mt-2 inline-block text-blue-600 text-sm">Reset filter</a>
        </div>
        @endif
        @if(isset($barangs) && $barangs->isEmpty())
        <div class="mt-6 py-8 text-center border-t border-gray-100">
            <p class="text-gray-400 text-sm">Tidak ada barang ditemukan</p>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        const start = document.getElementById('startDate').value;
        const end = document.getElementById('endDate').value;
        const err = document.getElementById('dateError');
        err.classList.add('hidden'); err.textContent = '';
        if (start && end && end < start) {
            e.preventDefault();
            err.textContent = 'Tanggal akhir tidak boleh sebelum tanggal mulai';
            err.classList.remove('hidden');
            if (window.toast) window.toast('Tanggal akhir tidak boleh sebelum tanggal mulai', 'error');
        }
    });
</script>
@endpush
