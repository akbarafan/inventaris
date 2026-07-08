@extends('layouts.app')

@section('title', 'Data Barang')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Data Barang</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola seluruh data barang inventaris</p>
        </div>
        <div class="flex items-center gap-2">
            @if(Auth::user()->isAdmin())
            <button onclick="openModal('importModal')" class="btn-outline text-sm px-4 py-2">
                <svg class="w-4 h-4 mr-1.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 4v12m0 0l-3-3m3 3l3-3"/></svg>
                Import CSV
            </button>
            @endif
            <button onclick="openModal('barangModal')" class="btn-primary text-sm px-4 py-2">
                <svg class="w-4 h-4 mr-1.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Barang
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <select id="filterKategori" class="form-input px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">Semua Kategori</option>
            @foreach($kategoris ?? [] as $k)
            <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
            @endforeach
        </select>
        <select id="filterLokasi" class="form-input px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">Semua Ruangan</option>
            @foreach($lokasis ?? [] as $l)
            <option value="{{ $l->id }}">{{ $l->nama_lokasi }}</option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="barangTable" class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-left">
                        <th class="px-4 py-3 font-medium w-12">No</th>
                        <th class="px-4 py-3 font-medium">Kode</th>
                        <th class="px-4 py-3 font-medium">Nama Barang</th>
                        <th class="px-4 py-3 font-medium">Kategori</th>
                        <th class="px-4 py-3 font-medium">Ruangan</th>
                        <th class="px-4 py-3 font-medium">Jumlah</th>
                        <th class="px-4 py-3 font-medium">Baik</th>
                        <th class="px-4 py-3 font-medium">Rusak</th>
                        <th class="px-4 py-3 font-medium">Rusak Berat</th>
                        <th class="px-4 py-3 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($barangs as $b)
                    <tr class="hover:bg-gray-50 transition-colors" data-id="{{ $b->id }}">
                        <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $b->kode_barang }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $b->nama_barang }}</td>
                        <td class="px-4 py-3">{{ $b->kategori->nama_kategori ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $b->lokasi->nama_lokasi ?? '-' }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $b->jumlah }}</td>
                        <td class="px-4 py-3"><span class="badge-baik">{{ $b->baik }}</span></td>
                        <td class="px-4 py-3"><span class="badge-rusak">{{ $b->rusak }}</span></td>
                        <td class="px-4 py-3"><span class="badge-rusakberat">{{ $b->rusak_berat }}</span></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <a href="/barang/{{ $b->kode_barang }}/qr" target="_blank" class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Download QR">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V4zM3 16a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H4a1 1 0 01-1-1v-4zm10-1a1 1 0 00-1 1v1h-1a1 1 0 000 2h1v1a1 1 0 002 0v-1h1a1 1 0 000-2h-1v-1a1 1 0 00-1-1z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13v4m-2-2h4"/></svg>
                                </a>
                                <button onclick="detailBarang({{ $b->id }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <button onclick="editBarang({{ $b->id }})" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                @if(Auth::user()->isAdmin())
                                <button onclick="hapusBarang({{ $b->id }})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
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

{{-- Modal Tambah/Edit Barang --}}
<div id="barangModal" class="modal-overlay hidden">
    <div class="modal-content max-w-2xl">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800" id="barangModalTitle">Tambah Barang</h3>
            <button onclick="closeModal('barangModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="barangForm" class="p-5 space-y-4">
            <input type="hidden" id="barangId">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                    <input type="text" id="namaBarang" required class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Barang</label>
                    <input type="text" id="kodeBarangDisplay" readonly class="form-input w-full px-3 py-2 border border-gray-200 rounded-lg text-sm font-mono bg-gray-50 text-gray-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select id="kategoriBarang" required class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Kategori</option>
                        @foreach($kategoris ?? [] as $k)
                        <option value="{{ $k->id }}" data-kode="{{ $k->kode }}">{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sumber</label>
                    <select id="sumberBarang" required class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Sumber</option>
                        <option value="BOS">BOS</option>
                        <option value="APBD">APBD</option>
                        <option value="Swadaya">Swadaya</option>
                        <option value="Hibah">Hibah</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk</label>
                    <input type="date" id="tanggalMasuk" required class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <select id="lokasiBarang" required class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Lokasi</option>
                        @foreach($lokasis ?? [] as $l)
                        <option value="{{ $l->id }}" data-kode="{{ $l->kode }}">{{ $l->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea id="keteranganBarang" rows="2" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Barang</label>
                    <input type="file" id="fotoBarang" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-400 mt-1">Maksimal 1MB, format JPG/PNG</p>
                    <p id="fotoError" class="text-xs text-red-500 mt-1 hidden"></p>
                    <img id="fotoPreview" class="mt-2 w-24 h-24 object-cover rounded-lg border border-gray-200 hidden">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rincian Kondisi</label>
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600">
                                <th class="px-4 py-2 text-left font-medium">Kondisi</th>
                                <th class="px-4 py-2 text-left font-medium">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="px-4 py-2">Baik</td>
                                <td class="px-4 py-2"><input type="number" id="kondisiBaik" value="0" min="0" class="form-input w-24 px-2 py-1 border border-gray-300 rounded text-sm text-center" oninput="hitungTotalKondisi()"></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">Rusak</td>
                                <td class="px-4 py-2"><input type="number" id="kondisiRusak" value="0" min="0" class="form-input w-24 px-2 py-1 border border-gray-300 rounded text-sm text-center" oninput="hitungTotalKondisi()"></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">Rusak Berat</td>
                                <td class="px-4 py-2"><input type="number" id="kondisiRusakBerat" value="0" min="0" class="form-input w-24 px-2 py-1 border border-gray-300 rounded text-sm text-center" oninput="hitungTotalKondisi()"></td>
                            </tr>
                            <tr class="bg-gray-50 font-semibold">
                                <td class="px-4 py-2">TOTAL</td>
                                <td class="px-4 py-2"><span id="totalKondisi">0</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p id="kondisiError" class="text-xs text-red-500 mt-1 hidden">Total kondisi harus sama dengan jumlah barang</p>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal('barangModal')" class="btn-secondary text-sm px-4 py-2">Batal</button>
                <button type="submit" class="btn-primary text-sm px-4 py-2">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Detail Barang --}}
<div id="detailModal" class="modal-overlay hidden">
    <div class="modal-content max-w-2xl">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Detail Barang</h3>
            <button onclick="closeModal('detailModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-5 space-y-4" id="detailContent">
        </div>
    </div>
</div>

{{-- Modal Import CSV --}}
<div id="importModal" class="modal-overlay hidden">
    <div class="modal-content max-w-3xl">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Import CSV</h3>
            <button onclick="closeModal('importModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-5 space-y-4" id="importStep1">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File CSV</label>
                <input type="file" id="fileImport" accept=".csv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-400 mt-1" id="importFileName"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Default</label>
                <select id="importKategori" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Kategori</option>
                    @foreach($kategoris ?? [] as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kategori }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">Kategori default untuk semua barang, bisa diubah per baris nanti</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sumber Default</label>
                <select id="importSumber" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Sumber</option>
                    <option value="BOS">BOS</option>
                    <option value="APBD">APBD</option>
                    <option value="Swadaya">Swadaya</option>
                    <option value="Hibah">Hibah</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">Sumber default untuk semua barang import</p>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('importModal')" class="btn-secondary text-sm px-4 py-2">Batal</button>
                <button type="button" id="previewBtn" class="btn-primary text-sm px-4 py-2" disabled>Preview</button>
            </div>
        </div>
        <div class="p-5 space-y-4 hidden" id="importStep2">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-700">Ruang: <span id="importRuang" class="font-semibold"></span></p>
                <p class="text-sm text-gray-500"><span id="importRowCount">0</span> barang</p>
            </div>
            <div class="overflow-x-auto max-h-80 overflow-y-auto border border-gray-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr class="text-gray-600 text-left">
                            <th class="px-3 py-2 font-medium w-10">No</th>
                            <th class="px-3 py-2 font-medium">Nama Barang</th>
                            <th class="px-3 py-2 font-medium w-48">Kategori</th>
                            <th class="px-3 py-2 font-medium w-20 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="importPreviewBody" class="divide-y divide-gray-100">
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" id="backToFileBtn" class="btn-secondary text-sm px-4 py-2">Kembali</button>
                <button type="button" id="importBtn" class="btn-primary text-sm px-4 py-2">Import</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let editingId = null;

    function previewKode() {
        const nama = document.getElementById('namaBarang').value;
        const kat = document.getElementById('kategoriBarang');
        const lok = document.getElementById('lokasiBarang');
        const kodeEl = document.getElementById('kodeBarangDisplay');
        const katKode = kat.options[kat.selectedIndex]?.dataset?.kode || 'XXX';
        const lokKode = lok.options[lok.selectedIndex]?.dataset?.kode || 'XX';
        const initials = nama ? nama.split(/[\s\-]+/).map(w => w.charAt(0).toUpperCase()).join('').slice(0, 5) : 'XXXX';
        if (kat.value && lok.value && nama) {
            kodeEl.value = lokKode + '-' + katKode + '-' + initials;
        } else {
            kodeEl.value = '(lengkapi nama, kategori & lokasi)';
        }
    }

    document.getElementById('namaBarang').addEventListener('input', previewKode);
    document.getElementById('kategoriBarang').addEventListener('change', previewKode);
    document.getElementById('lokasiBarang').addEventListener('change', previewKode);

    function hitungTotalKondisi() {
        const baik = parseInt(document.getElementById('kondisiBaik').value) || 0;
        const rusak = parseInt(document.getElementById('kondisiRusak').value) || 0;
        const berat = parseInt(document.getElementById('kondisiRusakBerat').value) || 0;
        document.getElementById('totalKondisi').textContent = baik + rusak + berat;
    }

    document.getElementById('fotoBarang').addEventListener('change', function() {
        const file = this.files[0];
        const preview = document.getElementById('fotoPreview');
        const errEl = document.getElementById('fotoError');
        errEl.classList.add('hidden');
        if (file) {
            if (file.size > 1 * 1024 * 1024) {
                errEl.textContent = 'File tidak boleh melebihi 1MB';
                errEl.classList.remove('hidden');
                this.value = '';
                preview.classList.add('hidden');
                return;
            }
            const allowed = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!allowed.includes(file.type)) {
                errEl.textContent = 'Format harus JPG atau PNG';
                errEl.classList.remove('hidden');
                this.value = '';
                preview.classList.add('hidden');
                return;
            }
            const reader = new FileReader();
            reader.onload = e => { preview.src = e.target.result; preview.classList.remove('hidden'); };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('hidden');
        }
    });

    document.getElementById('barangForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('barangId').value;
        const method = id ? 'PUT' : 'POST';
        const url = id ? `/barang/${id}` : '/barang';

        const total = parseInt(document.getElementById('totalKondisi').textContent) || 0;
        const fd = new FormData();
        fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        fd.append('nama_barang', document.getElementById('namaBarang').value);
        fd.append('kode_barang', document.getElementById('kodeBarangDisplay').value);
        fd.append('kategori_id', document.getElementById('kategoriBarang').value);
        fd.append('sumber', document.getElementById('sumberBarang').value);
        fd.append('tanggal_masuk', document.getElementById('tanggalMasuk').value);
        fd.append('lokasi_id', document.getElementById('lokasiBarang').value);
        fd.append('keterangan', document.getElementById('keteranganBarang').value);
        fd.append('jumlah', total);
        fd.append('baik', parseInt(document.getElementById('kondisiBaik').value) || 0);
        fd.append('rusak', parseInt(document.getElementById('kondisiRusak').value) || 0);
        fd.append('rusak_berat', parseInt(document.getElementById('kondisiRusakBerat').value) || 0);
        const foto = document.getElementById('fotoBarang').files[0];
        if (foto) fd.append('foto', foto);
        if (id) fd.append('_method', 'PUT');

        fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: fd
        })
        .then(r => r.text().then(t => ({ ok: r.ok, status: r.status, text: t })))
        .then(({ ok, status, text }) => {
            if (!ok) { alert('Error ' + status + ': ' + text.slice(0, 300)); return; }
            const res = JSON.parse(text);
            if (res.success) { location.reload(); }
            else { alert(res.message || 'Gagal menyimpan data'); }
        })
        .catch(e => alert('Gagal: ' + e.message));
    });

    function editBarang(id) {
        editingId = id;
        document.getElementById('barangModalTitle').textContent = 'Edit Barang';
        document.getElementById('barangId').value = id;

        fetch(`/barang/${id}/edit`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('namaBarang').value = data.nama_barang;
            document.getElementById('kodeBarangDisplay').value = data.kode_barang;
            document.getElementById('kategoriBarang').value = data.kategori_id;
            document.getElementById('sumberBarang').value = data.sumber;
            document.getElementById('tanggalMasuk').value = data.tanggal_masuk?.split(' ')[0] || '';
            document.getElementById('lokasiBarang').value = data.lokasi_id || data.barang_lokasis?.[0]?.lokasi_id || '';
            document.getElementById('keteranganBarang').value = data.keterangan || '';
            document.getElementById('kondisiBaik').value = data.baik || 0;
            document.getElementById('kondisiRusak').value = data.rusak || 0;
            document.getElementById('kondisiRusakBerat').value = data.rusak_berat || 0;
            const preview = document.getElementById('fotoPreview');
            if (data.foto) {
                preview.src = '/storage/' + data.foto;
                preview.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
            }
            hitungTotalKondisi();
            previewKode();
            openModal('barangModal');
        });
    }

    function resetBarangForm() {
        editingId = null;
        document.getElementById('barangModalTitle').textContent = 'Tambah Barang';
        document.getElementById('barangId').value = '';
        document.getElementById('barangForm').reset();
        document.getElementById('kondisiBaik').value = 0;
        document.getElementById('kondisiRusak').value = 0;
        document.getElementById('kondisiRusakBerat').value = 0;
        document.getElementById('fotoPreview').classList.add('hidden');
        hitungTotalKondisi();
        previewKode();
    }

    function detailBarang(id) {
        fetch(`/barang/${id}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            const fotoUrl = data.foto ? `/storage/${data.foto}` : null;
            const placeholder = 'https://placehold.co/200x200/e2e8f0/64748b?text=No+Image';
            const kondisi = [
                { label: 'Baik', count: data.baik, cls: 'badge-baik' },
                { label: 'Rusak', count: data.rusak, cls: 'badge-rusak' },
                { label: 'Rusak Berat', count: data.rusak_berat, cls: 'badge-rusakberat' },
            ];
            const lokasiNama = data.lokasi?.nama_lokasi || data.barang_lokasis?.[0]?.lokasi?.nama_lokasi || '-';
            document.getElementById('detailContent').innerHTML = `
                <div class="flex flex-col lg:flex-row gap-6">
                    <div class="shrink-0 space-y-3" style="width:96px">
                        <img src="${fotoUrl || placeholder}" alt="Foto ${data.nama_barang}" class="w-24 h-24 object-cover rounded-lg border border-gray-200 bg-gray-50" onerror="this.src='${placeholder}'">
                        <a href="/barang/${data.kode_barang}/qr" class="btn-outline text-sm px-3 py-1.5 w-full flex items-center justify-center gap-1.5" target="_blank">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V4zM3 16a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H4a1 1 0 01-1-1v-4zm10-1a1 1 0 00-1 1v1h-1a1 1 0 000 2h1v1a1 1 0 002 0v-1h1a1 1 0 000-2h-1v-1a1 1 0 00-1-1z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13v4m-2-2h4"/></svg>
                            Download QR
                        </a>
                    </div>
                    <div class="flex-1 min-w-0 space-y-4">
                        <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                            <div class="col-span-2">
                                <span class="text-gray-500 text-xs">Nama Barang</span>
                                <p class="font-semibold text-gray-800">${data.nama_barang}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-xs">Kode Barang</span>
                                <p class="font-mono font-medium">${data.kode_barang}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-xs">Kategori</span>
                                <p class="font-medium">${data.kategori?.nama_kategori || '-'}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-xs">Lokasi</span>
                                <p class="font-medium">${lokasiNama}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-xs">Sumber</span>
                                <p class="font-medium">${data.sumber || '-'}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-xs">Jumlah Total</span>
                                <p class="font-semibold text-lg">${data.jumlah}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-xs">Tanggal Masuk</span>
                                <p class="font-medium">${data.tanggal_masuk || '-'}</p>
                            </div>
                            <div class="col-span-2">
                                <span class="text-gray-500 text-xs">Keterangan</span>
                                <p class="font-medium">${data.keterangan || '-'}</p>
                            </div>
                        </div>
                        <div class="border-t border-gray-100 pt-3">
                            <span class="text-xs text-gray-500 block mb-2">Rincian Kondisi</span>
                            <div class="flex flex-wrap gap-2">${kondisi.map(k => `<span class="${k.cls}">${k.label}: ${k.count}</span>`).join('')}</div>
                        </div>
                    </div>
                </div>
            `;
            openModal('detailModal');
        });
    }

    function hapusBarang(id) {
        if (!confirm('Yakin ingin menghapus barang ini?')) return;
        fetch(`/barang/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => { if (res.success) location.reload(); });
    }

    let importRows = [];
    let importLokasiId = null;

    document.getElementById('fileImport').addEventListener('change', function() {
        const file = this.files[0];
        document.getElementById('importFileName').textContent = file ? file.name : '';
        document.getElementById('previewBtn').disabled = !file;
    });

    document.getElementById('previewBtn').addEventListener('click', function() {
        const file = document.getElementById('fileImport').files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            const text = e.target.result;
            const lines = text.split(/\r?\n/);
            let ruang = null;
            let dataStart = false;
            const rows = [];

            for (let i = 0; i < lines.length; i++) {
                const cols = lines[i].split(',').map(c => c.trim().replace(/^"(.*)"$/, '$1'));
                const joined = cols.join(' ');

                if (!ruang) {
                    const m = joined.match(/Ruang:\s*(.+)/i);
                    if (m) ruang = m[1].trim();
                }

                const lower = cols.map(c => c.toLowerCase());
                const joinedLower = lower.join(',');

                if (!dataStart) {
                    if (joinedLower.includes('nama_barang')) { dataStart = true; continue; }
                    if ((lower[1] ?? '') === 'no' && (lower[2] ?? '') === 'nama barang') { dataStart = true; continue; }
                    continue;
                }

                if (!cols.some(c => c)) continue;

                const no = cols[1] ?? '';
                if (!isNaN(parseFloat(no)) && cols[2] && cols[2].trim()) {
                    const jml = parseInt(cols[3]) || 1;
                    rows.push({
                        nama: cols[2].trim(),
                        jumlah: jml,
                        baik: (cols[4] && cols[4].trim()) ? jml : 0,
                        rusak: (cols[5] && cols[5].trim()) ? jml : 0,
                        rusakBerat: (cols[6] && cols[6].trim()) ? jml : 0,
                        keterangan: cols[7] ?? '',
                    });
                }
            }

            if (rows.length === 0) { alert('Tidak ada data yang ditemukan di file CSV.'); return; }

            importRows = rows;
            document.getElementById('importRuang').textContent = ruang || '(tidak diketahui)';
            document.getElementById('importRowCount').textContent = rows.length;

            const kategoris = @json($kategoris);
            const defaultKat = document.getElementById('importKategori').value;
            const tbody = document.getElementById('importPreviewBody');
            tbody.innerHTML = rows.map((r, i) => {
                const katOpts = kategoris.map(k =>
                    `<option value="${k.id}" ${k.id == defaultKat ? 'selected' : ''}>${k.nama_kategori}</option>`
                ).join('');
                return `<tr>
                    <td class="px-3 py-2 text-gray-500 text-center">${i + 1}</td>
                    <td class="px-3 py-2 font-medium text-gray-800">${r.nama}</td>
                    <td class="px-3 py-2">
                        <select class="import-kategori form-input w-full px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">Pilih</option>
                            ${katOpts}
                        </select>
                    </td>
                    <td class="px-3 py-2 text-center">
                        <button onclick="hapusRowImport(${i})" class="text-red-500 hover:text-red-700 p-1" title="Hapus">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </td>
                </tr>`;
            }).join('');

            document.getElementById('importStep1').classList.add('hidden');
            document.getElementById('importStep2').classList.remove('hidden');
        };
        reader.readAsText(file);
    });

    function hapusRowImport(idx) {
        importRows.splice(idx, 1);
        const tbody = document.getElementById('importPreviewBody');
        const rows = tbody.querySelectorAll('tr');
        rows[idx].remove();
        rows.forEach((r, i) => {
            r.querySelector('td:first-child').textContent = i + 1;
            r.querySelector('button').onclick = function() { hapusRowImport(i); };
        });
        document.getElementById('importRowCount').textContent = importRows.length;
    }

    document.getElementById('backToFileBtn').addEventListener('click', function() {
        document.getElementById('importStep2').classList.add('hidden');
        document.getElementById('importStep1').classList.remove('hidden');
    });

    document.getElementById('importBtn').addEventListener('click', function() {
        if (importRows.length === 0) { alert('Tidak ada barang untuk diimport.'); return; }

        const katSelects = document.querySelectorAll('#importPreviewBody .import-kategori');
        const rows = [];
        let valid = true;
        katSelects.forEach((sel, i) => {
            const katId = sel.value;
            if (!katId) { valid = false; sel.classList.add('border-red-400'); }
            else { sel.classList.remove('border-red-400'); }
            rows.push({
                nama_barang: importRows[i].nama,
                kategori_id: katId || null,
                jumlah: importRows[i].jumlah,
                baik: importRows[i].baik,
                rusak: importRows[i].rusak,
                rusak_berat: importRows[i].rusakBerat,
                keterangan: importRows[i].keterangan,
            });
        });

        if (!valid) { alert('Semua barang harus memiliki kategori.'); return; }

        const ruang = document.getElementById('importRuang').textContent;
        const sumber = document.getElementById('importSumber').value;
        fetch('/barang/import-csv', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify({ ruang, sumber: sumber || null, rows })
        })
        .then(r => r.json())
        .then(res => { if (res.success) location.reload(); else alert(res.message || 'Gagal import'); });
    });

    function openModal(id) {
        if (id === 'barangModal' && !editingId) resetBarangForm();
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    const dt = new DataTable('#barangTable', {
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
        order: [[2, 'asc']],
        columnDefs: [{ orderable: false, targets: [0, 9] }],
    });

    document.getElementById('filterKategori')?.addEventListener('change', function() {
        const text = this.options[this.selectedIndex]?.text || '';
        dt.column(3).search(this.value ? text : '').draw();
    });

    document.getElementById('filterLokasi')?.addEventListener('change', function() {
        const text = this.options[this.selectedIndex]?.text || '';
        dt.column(4).search(this.value ? text : '').draw();
    });
</script>
@endpush
