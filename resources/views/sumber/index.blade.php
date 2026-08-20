@extends('layouts.app')

@section('title', 'Sumber')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Sumber</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola sumber dana pengadaan barang</p>
        </div>
        <button onclick="openModal('sumberModal')" class="btn-primary text-sm px-4 py-2">
            <svg class="w-4 h-4 mr-1.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Sumber
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="sumberTable" class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-left">
                        <th class="px-4 py-3 font-medium w-12">No</th>
                        <th class="px-4 py-3 font-medium">Nama Sumber</th>
                        <th class="px-4 py-3 font-medium">Kode Unik</th>
                        <th class="px-4 py-3 font-medium">Jumlah Barang</th>
                        <th class="px-4 py-3 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($sumbers as $s)
                    <tr class="hover:bg-gray-50 transition-colors" data-id="{{ $s->id }}">
                        <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $s->nama_sumber }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-blue-600 font-semibold">{{ $s->kode }}</td>
                        <td class="px-4 py-3">{{ $s->barangs_count ?? 0 }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="editSumber({{ $s->id }})" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                @if(Auth::user()->isAdmin())
                                <button onclick="hapusSumber({{ $s->id }})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
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

{{-- Modal Tambah/Edit Sumber --}}
<div id="sumberModal" class="modal-overlay hidden">
    <div class="modal-content max-w-md">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800" id="sumberModalTitle">Tambah Sumber</h3>
            <button onclick="closeModal('sumberModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="sumberForm" class="p-5 space-y-4">
            <input type="hidden" id="sumberId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Sumber</label>
                <input type="text" id="namaSumber" required class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan nama sumber">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Unik</label>
                <input type="text" id="kodeSumber" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Otomatis dari nama sumber" maxlength="10">
                <p class="text-xs text-gray-400 mt-1">Kosongkan untuk otomatis.</p>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('sumberModal')" class="btn-secondary text-sm px-4 py-2">Batal</button>
                <button type="submit" class="btn-primary text-sm px-4 py-2">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let editingSumberId = null;

    function generateKodePreview(nama) {
        var words = nama.trim().split(/[\s\-]+/);
        return words.length > 1
            ? words.map(function(w) { return w.charAt(0).toUpperCase(); }).join('')
            : nama.substring(0, 3).toUpperCase();
    }

    document.getElementById('namaSumber').addEventListener('input', function() {
        var kodeEl = document.getElementById('kodeSumber');
        if (!kodeEl.dataset.userEdited) kodeEl.value = generateKodePreview(this.value);
    });

    document.getElementById('kodeSumber').addEventListener('input', function() {
        this.dataset.userEdited = this.value !== '';
    });

    document.getElementById('sumberForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('sumberId').value;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `/sumber/${id}` : '/sumber';
        const data = { nama_sumber: document.getElementById('namaSumber').value, kode: document.getElementById('kodeSumber').value };

        fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(r => r.json())
        .then(res => { if (res.success) location.reload(); else alert(res.message || 'Gagal menyimpan'); })
        .catch(() => alert('Terjadi kesalahan, coba lagi.'));
    });

    function editSumber(id) {
        editingSumberId = id;
        document.getElementById('sumberModalTitle').textContent = 'Edit Sumber';
        document.getElementById('sumberId').value = id;

        fetch(`/sumber/${id}/edit`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('namaSumber').value = data.nama_sumber;
            document.getElementById('kodeSumber').value = data.kode || '';
            document.getElementById('kodeSumber').dataset.userEdited = '1';
            openModal('sumberModal');
        });
    }

    function resetSumberForm() {
        editingSumberId = null;
        document.getElementById('sumberModalTitle').textContent = 'Tambah Sumber';
        document.getElementById('sumberId').value = '';
        document.getElementById('namaSumber').value = '';
        document.getElementById('kodeSumber').value = '';
        delete document.getElementById('kodeSumber').dataset.userEdited;
    }

    function hapusSumber(id) {
        if (!confirm('Yakin ingin menghapus sumber ini?')) return;
        fetch(`/sumber/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) location.reload();
            else alert(res.message || 'Gagal menghapus');
        })
        .catch(() => alert('Terjadi kesalahan, coba lagi.'));
    }

    function openModal(id) {
        if (id === 'sumberModal' && !editingSumberId) resetSumberForm();
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    new DataTable('#sumberTable', {
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
        columnDefs: [{ orderable: false, targets: [0, 4] }],
    });
</script>
@endpush