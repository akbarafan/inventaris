@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
                <p class="text-sm text-gray-500 mt-1">Ringkasan kesehatan inventaris — {{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-full text-gray-600">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span> {{ $totalJenis ?? 0 }} jenis barang
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-full text-gray-600">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> {{ ($perLokasi ?? collect())->count() }} lokasi aktif
                </span>
            </div>
        </div>

        {{-- KPI Cards with % --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $kpi = [
                    ['label' => 'Total Unit', 'value' => $totalBarang ?? 0, 'pct' => null, 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'bg' => 'bg-blue-600', 'light' => 'bg-blue-50', 'href' => url('/barang')],
                    ['label' => 'Kondisi Baik', 'value' => $totalBaik ?? 0, 'pct' => $totalBarang ? round($totalBaik / $totalBarang * 100) : 0, 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'bg' => 'bg-emerald-600', 'light' => 'bg-emerald-50', 'href' => url('/barang')],
                    ['label' => 'Rusak', 'value' => $totalRusak ?? 0, 'pct' => $totalBarang ? round($totalRusak / $totalBarang * 100) : 0, 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z', 'bg' => 'bg-amber-500', 'light' => 'bg-amber-50', 'href' => url('/barang')],
                    ['label' => 'Rusak Berat', 'value' => $totalRusakBerat ?? 0, 'pct' => $totalBarang ? round($totalRusakBerat / $totalBarang * 100) : 0, 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z', 'bg' => 'bg-red-600', 'light' => 'bg-red-50', 'href' => url('/barang')],
                ];
            @endphp
            @foreach($kpi as $c)
            <a href="{{ $c['href'] }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-shadow block group">
                <div class="flex items-start justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ $c['label'] }}</p>
                        <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ number_format($c['value']) }}</p>
                        @if($c['pct'] !== null)
                            <p class="text-xs mt-1 {{ $c['label'] === 'Kondisi Baik' ? 'text-emerald-600' : ($c['label'] === 'Rusak Berat' ? 'text-red-600' : 'text-amber-600') }}">{{ $c['pct'] }}%</p>
                        @else
                            <p class="text-xs text-gray-400 mt-1">{{ $totalJenis ?? 0 }} jenis</p>
                        @endif
                    </div>
                    <div class="w-10 h-10 {{ $c['light'] }} rounded-xl flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5 {{ str_replace('bg-', 'text-', $c['bg']) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $c['icon'] }}"/></svg>
                    </div>
                </div>
                @if($c['pct'] !== null)
                <div class="mt-3 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full {{ $c['bg'] }} rounded-full" style="width: {{ $c['pct'] }}%"></div>
                </div>
                @endif
            </a>
            @endforeach
        </div>

        {{-- Charts row: Donut kondisi + Compact bar per lokasi --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800">Kesehatan Inventaris</h3>
                    <span class="text-xs px-2 py-1 rounded-full {{ ($pctBaik ?? 0) >= 80 ? 'bg-emerald-50 text-emerald-700' : (($pctBaik ?? 0) >= 60 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">{{ $pctBaik ?? 0 }}% baik</span>
                </div>
                <div class="relative h-56 flex items-center justify-center">
                    <canvas id="kondisiChart"></canvas>
                    @if(($totalBarang ?? 0) == 0)
                        <p class="absolute text-sm text-gray-400">Belum ada data</p>
                    @endif
                </div>
                <div class="flex items-center justify-center gap-4 mt-3 text-xs">
                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Baik ({{ number_format($totalBaik ?? 0) }})</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> Rusak ({{ number_format($totalRusak ?? 0) }})</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Rusak Berat ({{ number_format($totalRusakBerat ?? 0) }})</span>
                </div>
            </div>

            <div class="lg:col-span-3 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800">Sebaran per Lokasi</h3>
                    <a href="{{ url('/lokasi') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Lihat lokasi →</a>
                </div>
                @if(($perLokasi ?? collect())->isEmpty())
                    <div class="py-16 text-center">
                        <p class="text-sm text-gray-400">Belum ada data lokasi</p>
                    </div>
                @else
                    <div class="relative" style="height: {{ min(240, max(120, ($perLokasi->count() * 36))) }}px">
                        <canvas id="lokasiChart"></canvas>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-2 text-center">{{ $perLokasi->count() }} lokasi · total {{ number_format($totalBarang ?? 0) }} unit</p>
                @endif
            </div>
        </div>

        {{-- Perlu Perhatian: rusak & rusak berat --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </span>
                    <h3 class="font-semibold text-gray-800">Perlu Perhatian</h3>
                    @if(($perluPerhatian ?? collect())->isNotEmpty())
                        <span class="text-xs px-2 py-0.5 rounded-full bg-red-50 text-red-600 font-medium">{{ $perluPerhatian->count() }} item</span>
                    @endif
                </div>
                <a href="{{ url('/barang') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat semua →</a>
            </div>
            @if(($perluPerhatian ?? collect())->isEmpty())
                <div class="px-5 py-10 text-center">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center mx-auto">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-700 mt-2">Semua barang dalam kondisi baik</p>
                    <p class="text-xs text-gray-400 mt-1">Tidak ada barang rusak atau rusak berat saat ini</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($perluPerhatian as $b)
                    <a href="{{ url('/barang') }}" class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 transition-colors group">
                        <div class="w-9 h-9 rounded-lg {{ $b->rusak_berat > 0 ? 'bg-red-50' : 'bg-amber-50' }} flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 {{ $b->rusak_berat > 0 ? 'text-red-600' : 'text-amber-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate group-hover:text-blue-700">{{ $b->nama_barang }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $b->kategori->nama_kategori ?? '-' }} · {{ $b->lokasi->nama_lokasi ?? '-' }} · <span class="font-mono">{{ $b->kode_barang }}</span></p>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            @if($b->rusak > 0)<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700">Rusak {{ $b->rusak }}</span>@endif
                            @if($b->rusak_berat > 0)<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700">RB {{ $b->rusak_berat }}</span>@endif
                        </div>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l5 7-5 7"/></svg>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Barang Terbaru</h3>
                    <a href="{{ url('/barang') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat semua</a>
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
                            @forelse($barangTerbaru ?? [] as $barang)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $barang->kode_barang ?? '-' }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $barang->nama_barang ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $barang->jumlah ?? 0 }}</td>
                                <td class="px-4 py-3">
                                    @if(($barang->baik ?? 0) > 0)<span class="badge-baik mr-1">Baik: {{ $barang->baik }}</span>@endif
                                    @if(($barang->rusak ?? 0) > 0)<span class="badge-rusak mr-1">Rusak: {{ $barang->rusak }}</span>@endif
                                    @if(($barang->rusak_berat ?? 0) > 0)<span class="badge-rusakberat">RB: {{ $barang->rusak_berat }}</span>@endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="py-12 text-center">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <p class="text-gray-400 text-sm">Belum ada barang</p>
                                <a href="{{ url('/barang') }}" class="mt-2 inline-block text-blue-600 text-sm">+ Tambah barang pertama</a>
                            </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Aktivitas Terbaru</h3>
                    <a href="{{ url('/audit') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat semua</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($aktivitasTerbaru ?? [] as $log)
                    @php
                        $badge = match($log->action) {
                            'created' => 'bg-emerald-50 text-emerald-700',
                            'updated' => 'bg-blue-50 text-blue-700',
                            'deleted' => 'bg-red-50 text-red-700',
                            'mutasi' => 'bg-purple-50 text-purple-700',
                            'scan' => 'bg-amber-50 text-amber-700',
                            default => 'bg-gray-50 text-gray-600',
                        };
                    @endphp
                    <div class="px-5 py-3 flex items-center gap-3 hover:bg-gray-50 transition-colors">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $badge }}">{{ $log->action }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-700 truncate">{{ $log->description }}</p>
                            <p class="text-xs text-gray-400">{{ $log->user->name ?? 'Sistem' }} · {{ $log->created_at?->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="px-5 py-10 text-center">
                        <p class="text-sm text-gray-400">Belum ada aktivitas</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-white">
            <div>
                <p class="font-semibold">Aksi Cepat</p>
                <p class="text-sm text-blue-100 mt-0.5">Kelola inventaris lebih cepat</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ url('/barang') }}" class="inline-flex items-center gap-1.5 bg-white text-blue-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-blue-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Barang
                </a>
                <a href="{{ url('/scan') }}" class="inline-flex items-center gap-1.5 bg-white/15 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-white/25 transition-colors border border-white/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01"/></svg>
                    Scan QR
                </a>
                <a href="{{ url('/laporan') }}" class="inline-flex items-center gap-1.5 bg-white/15 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-white/25 transition-colors border border-white/20">
                    Laporan
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    (function() {
        const kondisiEl = document.getElementById('kondisiChart');
        if (kondisiEl) {
            const d = @json($kondisiData ?? ['baik'=>0,'rusak'=>0,'rusak_berat'=>0]);
            const vals = [d.baik || 0, d.rusak || 0, d.rusak_berat || 0];
            const total = vals.reduce((a,b)=>a+b,0);
            if (total === 0) vals[0] = 1;
            new Chart(kondisiEl, {
                type: 'doughnut',
                data: {
                    labels: ['Baik','Rusak','Rusak Berat'],
                    datasets: [{ data: vals, backgroundColor: ['#10b981','#f59e0b','#ef4444'], borderWidth: 0, hoverOffset: 4 }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '68%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: '#0f172a', padding: 10, cornerRadius: 8, callbacks: { label: c => ' ' + c.label + ': ' + c.parsed + (total? ' ('+Math.round(c.parsed/total*100)+'%)' : '') } }
                    }
                },
                plugins: total ? [{ id: 'centerText', beforeDraw(c) { const {ctx, chartArea:{width,height,top,left}} = c; ctx.save(); ctx.font='700 22px Plus Jakarta Sans'; ctx.fillStyle='#1f2937'; ctx.textAlign='center'; ctx.textBaseline='middle'; ctx.fillText(@json($pctBaik ?? 0) + '%', left+width/2, top+height/2 - 4); ctx.font='500 11px Plus Jakarta Sans'; ctx.fillStyle='#6b7280'; ctx.fillText('Baik', left+width/2, top+height/2 + 14); ctx.restore(); }}] : []
            });
        }
        const lokasiEl = document.getElementById('lokasiChart');
        if (lokasiEl) {
            const raw = @json(($perLokasi ?? collect())->map(fn($r)=>['nama'=>$r->nama,'total'=>$r->total])->values());
            if (raw.length) {
                const max = Math.max(...raw.map(r=>r.total), 1);
                new Chart(lokasiEl, {
                    type: 'bar',
                    data: {
                        labels: raw.map(r=>r.nama.length>18 ? r.nama.slice(0,18)+'…' : r.nama),
                        datasets: [{ data: raw.map(r=>r.total), backgroundColor: '#3b82f6', hoverBackgroundColor: '#1d4ed8', borderRadius: 6, barThickness: raw.length > 8 ? 18 : 26, maxBarThickness: 28 }]
                    },
                    options: {
                        indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                        plugins: { legend: {display:false}, tooltip: { backgroundColor:'#0f172a', padding:10, cornerRadius:8, callbacks:{ title: items => raw[items[0].dataIndex].nama, label: c => ' ' + c.parsed.x + ' unit' } } },
                        scales: {
                            x: { beginAtZero:true, max: Math.ceil(max*1.15), ticks:{ precision:0, color:'#6b7280', font:{size:11} }, grid:{ color:'#f1f5f9' }, border:{display:false} },
                            y: { ticks:{ color:'#374151', font:{size:11, weight:500} }, grid:{display:false}, border:{display:false} }
                        }
                    }
                });
            }
        }
    })();
</script>
<script>
    (function() {
        const tbl = document.getElementById('barangTerbaruTable');
        if (tbl && !tbl.querySelector('td[colspan]') && tbl.querySelectorAll('tbody tr').length) {
            new DataTable('#barangTerbaruTable', {
                paging: false, info: false, searching: false, order: [[1, 'asc']],
                language: { zeroRecords: "Tidak ditemukan data yang sesuai" },
            });
        }
    })();
</script>
@endpush
