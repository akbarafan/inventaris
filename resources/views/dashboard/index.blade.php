@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
            <p class="text-sm text-gray-500 mt-1">Ringkasan data inventaris sekolah</p>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Barang</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalBarang ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Baik</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalBaik ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Rusak</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalRusak ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Rusak Berat</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalRusakBerat ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Scan Hari Ini</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $scanHariIni ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Two Columns --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Barang Terbaru --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Barang Terbaru</h3>
                    <a href="{{ url('/barang') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table id="barangTerbaruTable" class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-left">
                                <th class="px-4 py-3 font-medium">Kode</th>
                                <th class="px-4 py-3 font-medium">Nama</th>
                                <th class="px-4 py-3 font-medium">Jumlah</th>
                                <th class="px-4 py-3 font-medium">Kondisi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($barangTerbaru ?? [] as $barang)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $barang->kode_barang ?? '-' }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $barang->nama_barang ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $barang->jumlah ?? 0 }}</td>
                                <td class="px-4 py-3">
                                        <span class="badge-baik">Baik: {{ $barang->baik }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Scan Terbaru --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Scan Terbaru</h3>
                    <a href="{{ url('/scan') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Semua</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($scanTerbaru ?? [] as $scan)
                    <div class="px-5 py-3 flex items-center gap-3 hover:bg-gray-50 transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $scan->barang->nama_barang ?? 'Barang' }}</p>
                            <p class="text-xs text-gray-500">{{ $scan->created_at ?? '' }}</p>
                        </div>
                        <span class="text-xs font-mono text-gray-500">{{ $scan->kode_barang }}</span>
                    </div>
                    @empty
                    <div class="px-5 py-8 text-center text-gray-400">Belum ada scan</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Chart Area --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Aktivitas Scan 7 Hari Terakhir</h3>
            <div class="flex items-end gap-2 h-40">
                @php
                    $days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
                    $maxVal = max($chartData) ?: 1;
                @endphp
                @foreach($days as $i => $day)
                    @php
                        $val = $chartData[$i] ?? 0;
                        $height = ($val / $maxVal) * 100;
                        $h = max($height, 4);
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <span class="text-xs text-gray-500 font-medium">{{ $val }}</span>
                        <div class="w-full bg-blue-100 rounded-t-lg relative" style="height: 160px;">
                            <div class="absolute bottom-0 w-full bg-blue-500 rounded-t-lg transition-all duration-500 hover:bg-blue-600" style="height: {{ $h }}%;"></div>
                        </div>
                        <span class="text-xs text-gray-500">{{ $day }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    new DataTable('#barangTerbaruTable', {
        paging: false,
        info: false,
        searching: false,
        order: [[1, 'asc']],
        language: {
            processing: "Memproses...",
            lengthMenu: "Tampilkan _MENU_ data",
            zeroRecords: "Tidak ditemukan data yang sesuai",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ data keseluruhan)",
            search: "Cari:",
            paginate: { first: "Awal", previous: "Sebelumnya", next: "Selanjutnya", last: "Akhir" }
        },
    });
</script>
@endpush
