@extends('layouts.app')

@section('title', 'Lokasi')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Lokasi</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola lokasi penyimpanan barang</p>
        </div>
        <button onclick="openModal('lokasiModal')" class="btn-primary text-sm px-4 py-2">
            <svg class="w-4 h-4 mr-1.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Lokasi
        </button>
    </div>


<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="lokasiTable" class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-left">
                        <th class="px-4 py-3 font-medium w-12">No</th>
                        <th class="px-4 py-3 font-medium">Nama Lokasi</th>
                        <th class="px-4 py-3 font-medium">Kode Unik</th>
                        <th class="px-4 py-3 font-medium">Jumlah Barang</th>
                        <th class="px-4 py-3 font-medium">Total Unit</th>
                        <th class="px-4 py-3 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($lokasis as $l)
                    <tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="detailLokasi({{ $l->id }})" data-id="{{ $l->id }}">
                        <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $l->nama_lokasi }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-blue-600 font-semibold">{{ $l->kode }}</td>
                        <td class="px-4 py-3">{{ $l->barang_lokasis_count ?? 0 }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $l->barangLokasis->sum('jumlah') ?? 0 }}</td>
                        <td class="px-4 py-3" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="editLokasi({{ $l->id }})" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                @if(Auth::user()->isAdmin())
                                <button onclick="hapusLokasi({{ $l->id }})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah/Edit Lokasi --}}
<div id="lokasiModal" class="modal-overlay hidden">
    <div class="modal-content max-w-md">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800" id="lokasiModalTitle">Tambah Lokasi</h3>
            <button onclick="closeModal('lokasiModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="lokasiForm" class="p-5 space-y-4">
            <input type="hidden" id="lokasiId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lokasi</label>
                <input type="text" id="namaLokasi" required class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan nama lokasi">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Unik</label>
                <input type="text" id="kodeLokasi" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Otomatis dari nama lokasi" maxlength="10">
                <p class="text-xs text-gray-400 mt-1">Kosongkan untuk otomatis. Jika diubah, semua kode barang di lokasi ini akan menyesuaikan.</p>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('lokasiModal')" class="btn-secondary text-sm px-4 py-2">Batal</button>
                <button type="submit" class="btn-primary text-sm px-4 py-2">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Detail Lokasi --}}
<div id="detailLokasiModal" class="modal-overlay hidden">
    <div class="modal-content max-w-2xl">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800" id="detailLokasiTitle">Detail Lokasi</h3>
            <button onclick="closeModal('detailLokasiModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-5" id="detailLokasiContent"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let editingLokasiId = null;

    function generateKodePreview(nama) {
        var words = nama.trim().split(/[\s\-]+/);
        return words.length > 1
            ? words.map(function(w) { return w.charAt(0).toUpperCase(); }).join('')
            : nama.substring(0, 3).toUpperCase();
    }

    document.getElementById('namaLokasi').addEventListener('input', function() {
        var kodeEl = document.getElementById('kodeLokasi');
        if (!kodeEl.dataset.userEdited) kodeEl.value = generateKodePreview(this.value);
    });

    document.getElementById('kodeLokasi').addEventListener('input', function() {
        this.dataset.userEdited = this.value !== '';
    });

    document.getElementById('lokasiForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('lokasiId').value;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `/lokasi/${id}` : '/lokasi';
        const data = { nama_lokasi: document.getElementById('namaLokasi').value, kode: document.getElementById('kodeLokasi').value };

        fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(res => { if (res.success) location.reload(); else alert(res.message || 'Gagal menyimpan'); });
    });

    function editLokasi(id) {
        editingLokasiId = id;
        document.getElementById('lokasiModalTitle').textContent = 'Edit Lokasi';
        document.getElementById('lokasiId').value = id;

        fetch(`/lokasi/${id}/edit`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('namaLokasi').value = data.nama_lokasi;
            document.getElementById('kodeLokasi').value = data.kode || '';
            document.getElementById('kodeLokasi').dataset.userEdited = '1';
            openModal('lokasiModal');
        });
    }

    function resetLokasiForm() {
        editingLokasiId = null;
        document.getElementById('lokasiModalTitle').textContent = 'Tambah Lokasi';
        document.getElementById('lokasiId').value = '';
        document.getElementById('namaLokasi').value = '';
        document.getElementById('kodeLokasi').value = '';
        delete document.getElementById('kodeLokasi').dataset.userEdited;
    }

    function detailLokasi(id) {
        fetch(`/lokasi/${id}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('detailLokasiTitle').textContent = data.nama_lokasi;
            const barang = data.barang_lokasis || [];
            let html = `<p class="text-sm text-gray-500 mb-3">${barang.length} barang ditemukan</p>`;
            if (barang.length) {
                html += `<table class="w-full text-sm"><thead><tr class="bg-gray-50 text-gray-600 text-left"><th class="px-3 py-2 font-medium">Kode</th><th class="px-3 py-2 font-medium">Nama</th><th class="px-3 py-2 font-medium">Jumlah</th></tr></thead><tbody class="divide-y divide-gray-100">`;
                barang.forEach(b => {
                    const brg = b.barang || {};
                    html += `<tr class="hover:bg-gray-50"><td class="px-3 py-2 font-mono text-xs">${brg.kode_barang || '-'}</td><td class="px-3 py-2 font-medium">${brg.nama_barang || '-'}</td><td class="px-3 py-2">${b.jumlah}</td></tr>`;
                });
                html += `</tbody></table>`;
            } else {
                html += `<p class="text-center text-gray-400 py-8">Tidak ada barang di lokasi ini</p>`;
            }
            document.getElementById('detailLokasiContent').innerHTML = html;
            openModal('detailLokasiModal');
        });
    }

    function hapusLokasi(id) {
        if (!confirm('Yakin ingin menghapus lokasi ini?')) return;
        fetch(`/lokasi/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => { if (res.success) location.reload(); });
    }

    function openModal(id) {
        if (id === 'lokasiModal' && !editingLokasiId) resetLokasiForm();
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    new DataTable('#lokasiTable', {
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
        columnDefs: [{ orderable: false, targets: [0, 5] }],
    });
</script>
@endpush
