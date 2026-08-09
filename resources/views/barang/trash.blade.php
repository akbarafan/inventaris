@extends('layouts.app')

@section('title', 'Sampah Barang')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Sampah</h2>
            <p class="text-sm text-gray-500 mt-1">Barang yang dihapus sementara, bisa dipulihkan atau dihapus permanen</p>
        </div>
        <a href="{{ url('/barang') }}" class="btn-secondary text-sm px-4 py-2 inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Barang
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="trashTable" class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-left">
                        <th class="px-4 py-3 font-medium w-12">No</th>
                        <th class="px-4 py-3 font-medium">Kode</th>
                        <th class="px-4 py-3 font-medium">Nama Barang</th>
                        <th class="px-4 py-3 font-medium">Kategori</th>
                        <th class="px-4 py-3 font-medium">Jumlah</th>
                        <th class="px-4 py-3 font-medium">Dihapus</th>
                        <th class="px-4 py-3 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($barangs as $b)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $b->kode_barang }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $b->nama_barang }}</td>
                        <td class="px-4 py-3">{{ $b->kategori->nama_kategori ?? '-' }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $b->jumlah }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $b->deleted_at ? \Carbon\Carbon::parse($b->deleted_at)->format('d M Y H:i') : '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="restoreBarang({{ $b->id }})" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Pulihkan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </button>
                                <button onclick="hapusPermanen({{ $b->id }})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Permanen">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400">Tidak ada barang di sampah.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    new DataTable('#trashTable', {
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
        order: [[1, 'asc']],
        columnDefs: [{ orderable: false, targets: [6] }],
    });

    function restoreBarang(id) {
        if (!confirm('Pulihkan barang ini?')) return;
        fetch('/barang/' + id + '/restore', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
            .then(r => r.json())
            .then(res => { if (res.success) location.reload(); else alert(res.message || 'Gagal'); });
    }

    function hapusPermanen(id) {
        if (!confirm('Hapus permanen? Data tidak bisa dikembalikan.')) return;
        fetch('/barang/' + id + '/force-delete', { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
            .then(r => r.json())
            .then(res => { if (res.success) location.reload(); else alert(res.message || 'Gagal'); });
    }
</script>
@endpush