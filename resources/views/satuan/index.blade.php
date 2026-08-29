@extends('layouts.app')

@section('title', 'Satuan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Satuan</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola satuan barang (pcs, dus, pack, lusin, dll.)</p>
        </div>
        <button onclick="openModal('satuanModal')" class="btn-primary text-sm px-4 py-2">
            <svg class="w-4 h-4 mr-1.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Satuan
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="satuanTable" class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-left">
                        <th class="px-4 py-3 font-medium w-12">No</th>
                        <th class="px-4 py-3 font-medium">Nama Satuan</th>
                        <th class="px-4 py-3 font-medium">Kode Unik</th>
                        <th class="px-4 py-3 font-medium">Jumlah Barang</th>
                        <th class="px-4 py-3 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($satuans as $s)
                    <tr class="hover:bg-gray-50 transition-colors" data-id="{{ $s->id }}">
                        <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $s->nama_satuan }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-blue-600 font-semibold">{{ $s->kode }}</td>
                        <td class="px-4 py-3">{{ $s->barangs_count ?? 0 }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="editSatuan({{ $s->id }})" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                @if(Auth::user()->isAdmin())
                                <button onclick="hapusSatuan({{ $s->id }}, '{{ addslashes($s->nama_satuan) }}')" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-12 text-center"><p class="text-gray-400">Belum ada satuan</p><button onclick="openModal('satuanModal')" class="mt-2 text-blue-600 text-sm">+ Tambah pertama</button></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah/Edit Satuan --}}
<div id="satuanModal" class="modal-overlay hidden">
    <div class="modal-content max-w-md">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800" id="satuanModalTitle">Tambah Satuan</h3>
            <button onclick="closeModal('satuanModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="satuanForm" class="p-5 space-y-4">
            <input type="hidden" id="satuanId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Satuan</label>
                <input type="text" id="namaSatuan" required maxlength="50" minlength="2" autocomplete="off" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: pcs, dus, lusin">
                <p id="satuanNamaError" class="text-xs text-red-500 mt-1 hidden"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Unik</label>
                <input type="text" id="kodeSatuan" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Otomatis dari nama satuan" maxlength="10" pattern="[A-Za-z0-9]+" autocomplete="off" spellcheck="false" style="text-transform:uppercase">
                <p id="satuanKodeError" class="text-xs text-red-500 mt-1 hidden"></p>
                <p class="text-xs text-gray-400 mt-1">Kosongkan untuk otomatis.</p>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('satuanModal')" class="btn-secondary text-sm px-4 py-2">Batal</button>
                <button type="submit" id="satuanSubmitBtn" class="btn-primary text-sm px-4 py-2">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let editingSatuanId = null;

    function generateKodePreview(nama) {
        var words = nama.trim().split(/[\s\-]+/);
        return words.length > 1
            ? words.map(function(w) { return w.charAt(0).toUpperCase(); }).join('')
            : nama.substring(0, 3).toUpperCase();
    }

    document.getElementById('namaSatuan').addEventListener('input', function() {
        var kodeEl = document.getElementById('kodeSatuan');
        if (!kodeEl.dataset.userEdited) kodeEl.value = generateKodePreview(this.value);
    });

    document.getElementById('kodeSatuan').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
        this.dataset.userEdited = this.value !== '';
    });

    function showSatuanError(id, msg) {
        const el = document.getElementById(id);
        if (!el) return;
        if (msg) { el.textContent = msg; el.classList.remove('hidden'); }
        else { el.textContent = ''; el.classList.add('hidden'); }
    }
    function clearSatuanErrors() { showSatuanError('satuanNamaError',''); showSatuanError('satuanKodeError',''); document.getElementById('namaSatuan').classList.remove('border-red-500'); document.getElementById('kodeSatuan').classList.remove('border-red-500'); }

    document.getElementById('satuanForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearSatuanErrors();
        const btn = document.getElementById('satuanSubmitBtn');
        const origText = btn.textContent;
        btn.disabled = true; btn.textContent = 'Menyimpan\u2026';
        const id = document.getElementById('satuanId').value;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `/satuan/${id}` : '/satuan';
        const data = { nama_satuan: document.getElementById('namaSatuan').value, kode: document.getElementById('kodeSatuan').value };

        fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(async r => {
            const res = await r.json();
            if (!r.ok) {
                if (r.status === 422 && res.errors) {
                    if (res.errors.nama_satuan) { showSatuanError('satuanNamaError', res.errors.nama_satuan[0]); document.getElementById('namaSatuan').classList.add('border-red-500'); }
                    if (res.errors.kode) { showSatuanError('satuanKodeError', res.errors.kode[0]); document.getElementById('kodeSatuan').classList.add('border-red-500'); }
                    const first = Object.values(res.errors).flat()[0];
                    window.toast(res.message || first || 'Validasi gagal', 'error');
                } else {
                    window.toast(res.message || 'Gagal menyimpan', 'error');
                }
                throw new Error('validation');
            }
            return res;
        })
        .then(res => { if (res.success) { window.toast(res.message || 'Berhasil disimpan', 'success'); if(window.refreshPage){window.refreshPage()}else{location.reload()}; } else window.toast(res.message || 'Gagal menyimpan', 'error'); })
        .catch(err => { if (err.message !== 'validation') window.toast('Terjadi kesalahan, coba lagi.', 'error'); })
        .finally(() => { btn.disabled = false; btn.textContent = origText; });
    });

    function editSatuan(id) {
        editingSatuanId = id;
        document.getElementById('satuanModalTitle').textContent = 'Edit Satuan';
        document.getElementById('satuanId').value = id;
        clearSatuanErrors();

        fetch(`/satuan/${id}/edit`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('namaSatuan').value = data.nama_satuan;
            document.getElementById('kodeSatuan').value = data.kode || '';
            document.getElementById('kodeSatuan').dataset.userEdited = '1';
            openModal('satuanModal');
        });
    }

    function resetSatuanForm() {
        editingSatuanId = null;
        document.getElementById('satuanModalTitle').textContent = 'Tambah Satuan';
        document.getElementById('satuanId').value = '';
        document.getElementById('namaSatuan').value = '';
        document.getElementById('kodeSatuan').value = '';
        delete document.getElementById('kodeSatuan').dataset.userEdited;
        clearSatuanErrors();
    }

    function hapusSatuan(id, nama) {
        const label = nama ? ` "${nama}"` : '';
        if (!confirm(`Yakin ingin menghapus satuan${label}?`)) return;
        fetch(`/satuan/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        })
        .then(r => r.json().then(res => ({ ok: r.ok, status: r.status, res })))
        .then(({ ok, res }) => {
            if (ok && res.success) { window.toast(res.message || 'Satuan dihapus', 'success'); if(window.refreshPage){window.refreshPage()}else{location.reload()}; }
            else window.toast(res.message || 'Gagal menghapus', 'error');
        })
        .catch(() => window.toast('Terjadi kesalahan, coba lagi.', 'error'));
    }

    function openModal(id) {
        if (id === 'satuanModal' && !editingSatuanId) resetSatuanForm();
        document.getElementById(id).classList.remove('hidden');
        if (id === 'satuanModal') setTimeout(() => document.getElementById('namaSatuan').focus(), 50);
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal('satuanModal'); });

    const satuanTableEl = document.getElementById('satuanTable');
    if (satuanTableEl && !satuanTableEl.querySelector('td[colspan]') && satuanTableEl.querySelectorAll('tbody tr').length) {
        new DataTable('#satuanTable', {
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
    }
</script>
@endpush
