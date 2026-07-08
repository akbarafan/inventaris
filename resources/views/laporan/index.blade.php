@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Laporan</h2>
        <p class="text-sm text-gray-500 mt-1">Export data inventaris dalam format Excel</p>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Filter Laporan</h3>
        <form id="filterForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" id="startDate" value="{{ request('start_date') }}"
                    class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" id="endDate" value="{{ request('end_date') }}"
                    class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Sumber</label>
                <select name="sumber" id="filterSumber" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Sumber</option>
                    <option value="BOS" {{ request('sumber') == 'BOS' ? 'selected' : '' }}>BOS</option>
                    <option value="APBD" {{ request('sumber') == 'APBD' ? 'selected' : '' }}>APBD</option>
                    <option value="Swadaya" {{ request('sumber') == 'Swadaya' ? 'selected' : '' }}>Swadaya</option>
                    <option value="Hibah" {{ request('sumber') == 'Hibah' ? 'selected' : '' }}>Hibah</option>
                    <option value="Lainnya" {{ request('sumber') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
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
                    <p class="text-sm text-gray-500 mt-1">Excel - Seluruh data barang inventaris</p>
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
    </div>
</div>
@endsection
