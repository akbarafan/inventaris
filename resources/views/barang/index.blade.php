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
            <button onclick="cetakTerpilih()" class="btn-outline text-sm px-4 py-2">
                <svg class="w-4 h-4 mr-1.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 14h12v6H6z"/></svg>
                Cetak Terpilih
            </button>
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
                        <th class="px-4 py-3 font-medium w-10"><input type="checkbox" id="checkAll" onchange="toggleAllCheckbox(this)"></th>
                        <th class="px-4 py-3 font-medium w-12">No</th>
                        <th class="px-4 py-3 font-medium">Kode</th>
                        <th class="px-4 py-3 font-medium">Nama Barang</th>
                        <th class="px-4 py-3 font-medium">Kategori</th>
                        <th class="px-4 py-3 font-medium">Ruangan</th>
                        <th class="px-4 py-3 font-medium">Jumlah</th>
                        <th class="px-4 py-3 font-medium">Satuan</th>
                        <th class="px-4 py-3 font-medium">Baik</th>
                        <th class="px-4 py-3 font-medium">Rusak</th>
                        <th class="px-4 py-3 font-medium">Rusak Berat</th>
                        <th class="px-4 py-3 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($barangs as $b)
                    <tr class="hover:bg-gray-50 transition-colors" data-id="{{ $b->id }}">
                        <td class="px-4 py-3"><input type="checkbox" class="cb-barang" value="{{ $b->kode_barang }}"></td>
                        <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $b->kode_barang }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $b->nama_barang }}</td>
                        <td class="px-4 py-3">{{ $b->kategori->nama_kategori ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $b->lokasi->nama_lokasi ?? '-' }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $b->jumlah }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $b->satuan->nama_satuan ?? '-' }}</td>
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
                                <button onclick="openMutasi({{ $b->id }})" class="p-1.5 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="Mutasi / Pindah Lokasi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 6l-2 2m2-2l2 2m14 10H4m16 0l-2 2m2-2l-2-2M9 3v6m0 0L7 7m2 2l2-2m4 5v6m0 0l-2-2m2 2l2-2"/></svg>
                                </button>
                                <button onclick="hapusBarang({{ $b->id }}, '{{ addslashes($b->nama_barang) }}')" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="12" class="py-12 text-center">
                        <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293H8.586a1 1 0 00-.707.293l-2.414 2.414A1 1 0 005 21h14a2 2 0 002-2v-5z"/></svg>
                        <p class="text-gray-400 text-sm">Belum ada barang</p>
                        <button onclick="openModal('barangModal')" class="mt-2 text-blue-600 text-sm font-medium hover:underline">+ Tambah pertama</button>
                    </td></tr>
                    @endforelse
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                    <input type="text" id="namaBarang" required maxlength="255" minlength="2" autocomplete="off" spellcheck="false" placeholder="Contoh: Proyektor Epson" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p id="nama_barangError" class="field-error hidden"></p>
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
                    <p id="kategori_idError" class="field-error hidden"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sumber</label>
                    <select id="sumberBarang" required class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Sumber</option>
                        @foreach($sumbers ?? [] as $s)
                        <option value="{{ $s->id }}">{{ $s->nama_sumber }}</option>
                        @endforeach
                    </select>
                    <p id="sumber_idError" class="field-error hidden"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk <span class="text-red-500">*</span></label>
                    <input type="date" id="tanggalMasuk" required max="{{ now()->format('Y-m-d') }}" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p id="tanggal_masukError" class="field-error hidden"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <select id="lokasiBarang" required class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Lokasi</option>
                        @foreach($lokasis ?? [] as $l)
                        <option value="{{ $l->id }}" data-kode="{{ $l->kode }}">{{ $l->nama_lokasi }}</option>
                        @endforeach
                    </select>
                    <p id="lokasi_idError" class="field-error hidden"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                    <select id="satuanBarang" required class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Satuan</option>
                        @foreach($satuans ?? [] as $st)
                        <option value="{{ $st->id }}">{{ $st->nama_satuan }}</option>
                        @endforeach
                    </select>
                    <p id="satuan_idError" class="field-error hidden"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea id="keteranganBarang" rows="2" maxlength="1000" placeholder="Keterangan tambahan (opsional)" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    <p id="keteranganError" class="field-error hidden"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Barang</label>
                    <input type="file" id="fotoBarang" accept="image/jpeg,image/png,image/jpg,image/webp" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-400 mt-1">Maksimal 1MB, format JPG/PNG/WebP</p>
                    <p id="fotoError" class="text-xs text-red-500 mt-1 hidden"></p>
                    <img id="fotoPreview" class="mt-2 w-24 h-24 object-cover rounded-lg border border-gray-200 hidden">
                    <button type="button" id="hapusFotoBtn" onclick="hapusFotoPreview()" class="hidden mt-1 text-xs text-red-500 hover:text-red-700">Hapus foto</button>
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
                                <td class="px-4 py-2"><input type="number" id="kondisiBaik" value="0" min="0" max="99999" step="1" inputmode="numeric" class="form-input w-24 px-2 py-1 border border-gray-300 rounded text-sm text-center" oninput="hitungTotalKondisi()"></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">Rusak</td>
                                <td class="px-4 py-2"><input type="number" id="kondisiRusak" value="0" min="0" max="99999" step="1" inputmode="numeric" class="form-input w-24 px-2 py-1 border border-gray-300 rounded text-sm text-center" oninput="hitungTotalKondisi()"></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">Rusak Berat</td>
                                <td class="px-4 py-2"><input type="number" id="kondisiRusakBerat" value="0" min="0" max="99999" step="1" inputmode="numeric" class="form-input w-24 px-2 py-1 border border-gray-300 rounded text-sm text-center" oninput="hitungTotalKondisi()"></td>
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
                <button type="submit" id="barangSubmitBtn" class="btn-primary text-sm px-4 py-2">Simpan</button>
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
                <a href="#" onclick="downloadTemplate(); return false;" class="text-xs text-blue-600 hover:underline mt-1 inline-block">Download template CSV</a>
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
                    @foreach($sumbers ?? [] as $s)
                    <option value="{{ $s->id }}">{{ $s->nama_sumber }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">Sumber default untuk semua barang import</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Tujuan <span class="text-red-500">*</span></label>
                <select id="importLokasi" required class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Lokasi</option>
                    @foreach($lokasis ?? [] as $l)
                    <option value="{{ $l->id }}">{{ $l->nama_lokasi }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">Lokasi tujuan untuk semua barang import</p>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal('importModal')" class="btn-secondary text-sm px-4 py-2">Batal</button>
                <button type="button" id="previewBtn" class="btn-primary text-sm px-4 py-2" disabled>Preview</button>
            </div>
        </div>
        <div class="p-5 space-y-4 hidden" id="importStep2">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-700">Lokasi: <span id="importRuang" class="font-semibold"></span></p>
                <p class="text-sm text-gray-500"><span id="importRowCount">0</span> barang</p>
            </div>
            <div class="overflow-x-auto max-h-80 overflow-y-auto border border-gray-200 rounded-lg">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr class="text-gray-600 text-left">
                            <th class="px-3 py-2 font-medium w-10">No</th>
                            <th class="px-3 py-2 font-medium">Nama Barang</th>
                            <th class="px-3 py-2 font-medium w-16 text-center">Jumlah</th>
                            <th class="px-3 py-2 font-medium w-16 text-center">Baik</th>
                            <th class="px-3 py-2 font-medium w-16 text-center">Rusak</th>
                            <th class="px-3 py-2 font-medium w-20 text-center">Rusak Berat</th>
                            <th class="px-3 py-2 font-medium w-36">Kategori</th>
                            <th class="px-3 py-2 font-medium w-32">Sumber</th>
                            <th class="px-3 py-2 font-medium w-28">Satuan</th>
                            <th class="px-3 py-2 font-medium w-12 text-center">Aksi</th>
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

{{-- Modal Mutasi Barang --}}
<div id="mutasiModal" class="modal-overlay hidden">
    <div class="modal-content max-w-lg">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Mutasi Barang</h3>
            <button onclick="closeModal('mutasiModal')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="mutasiForm" class="p-5 space-y-4">
            <input type="hidden" id="mutasiBarangId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Barang</label>
                <p id="mutasiNamaBarang" class="text-sm font-semibold text-gray-800"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Tujuan</label>
                <select id="mutasiLokasiTujuan" required class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih Lokasi</option>
                    @foreach($lokasis ?? [] as $l)
                    <option value="{{ $l->id }}">{{ $l->nama_lokasi }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Mutasi</label>
                <input type="number" id="mutasiJumlah" required min="1" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Baik</label>
                    <input type="number" id="mutasiBaik" min="0" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rusak</label>
                    <input type="number" id="mutasiRusak" min="0" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rusak Berat</label>
                    <input type="number" id="mutasiRusakBerat" min="0" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                <input type="text" id="mutasiKeterangan" maxlength="255" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal('mutasiModal')" class="btn-secondary text-sm px-4 py-2">Batal</button>
                <button type="submit" class="btn-primary text-sm px-4 py-2">Simpan Mutasi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let editingId = null;
    let selectedKodes = new Set();
    const ALL_KODES = @json($barangs->pluck('kode_barang')->values());

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
        const hapusBtn = document.getElementById('hapusFotoBtn');
        const errEl = document.getElementById('fotoError');
        errEl.classList.add('hidden');
        if (file) {
            if (file.size > 1 * 1024 * 1024) {
                errEl.textContent = 'File tidak boleh melebihi 1MB';
                errEl.classList.remove('hidden');
                this.value = '';
                preview.classList.add('hidden');
                hapusBtn.classList.add('hidden');
                return;
            }
            const allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            if (!allowed.includes(file.type)) {
                errEl.textContent = 'Format harus JPG, PNG, atau WebP';
                errEl.classList.remove('hidden');
                this.value = '';
                preview.classList.add('hidden');
                hapusBtn.classList.add('hidden');
                return;
            }
            const reader = new FileReader();
            reader.onload = e => { preview.src = e.target.result; preview.classList.remove('hidden'); hapusBtn.classList.remove('hidden'); };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('hidden');
            hapusBtn.classList.add('hidden');
        }
    });

    function hapusFotoPreview() {
        document.getElementById('fotoBarang').value = '';
        document.getElementById('fotoPreview').classList.add('hidden');
        document.getElementById('hapusFotoBtn').classList.add('hidden');
    }

    document.getElementById('barangForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('barangSubmitBtn');
        window.clearFieldErrors?.();
        const id = document.getElementById('barangId').value;
        const url = id ? `/barang/${id}` : '/barang';

        const total = parseInt(document.getElementById('totalKondisi').textContent) || 0;
        const fd = new FormData();
        fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        fd.append('nama_barang', document.getElementById('namaBarang').value);
        const kodeVal = document.getElementById('kodeBarangDisplay').value;
        if (kodeVal && !kodeVal.startsWith('(')) fd.append('kode_barang', kodeVal);
        fd.append('kategori_id', document.getElementById('kategoriBarang').value);
        fd.append('sumber_id', document.getElementById('sumberBarang').value);
        fd.append('tanggal_masuk', document.getElementById('tanggalMasuk').value);
        fd.append('lokasi_id', document.getElementById('lokasiBarang').value);
        fd.append('satuan_id', document.getElementById('satuanBarang').value);
        fd.append('keterangan', document.getElementById('keteranganBarang').value);
        fd.append('jumlah', total);
        fd.append('baik', parseInt(document.getElementById('kondisiBaik').value) || 0);
        fd.append('rusak', parseInt(document.getElementById('kondisiRusak').value) || 0);
        fd.append('rusak_berat', parseInt(document.getElementById('kondisiRusakBerat').value) || 0);
        const foto = document.getElementById('fotoBarang').files[0];
        if (foto) fd.append('foto', foto);
        if (id) fd.append('_method', 'PUT');

        window.setBtnLoading?.(btn, true, 'Menyimpan...');

        fetch(url, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: fd
        })
        .then(async r => {
            const text = await r.text();
            let data;
            try { data = JSON.parse(text); } catch { data = { message: text.slice(0, 300) }; }
            if (!r.ok) {
                try { if (r.status === 422 && data.errors) window.showFieldErrors?.(data.errors); } catch {}
                try { window.toast?.(data.message || 'Gagal menyimpan data', 'error'); } catch {}
                return;
            }
            try { window.toast?.(data.message || 'Berhasil!', 'success'); } catch {}
            closeModal('barangModal');
            if(window.refreshPage){window.refreshPage()}else{location.reload()};
        })
        .catch(err => { try { window.toast?.('Gagal: ' + err.message, 'error'); } catch {} })
        .finally(() => { try { window.setBtnLoading?.(btn, false); } catch {} if(btn){ btn.disabled=false; btn.textContent='Simpan'; } });
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
            document.getElementById('sumberBarang').value = data.sumber_id;
            document.getElementById('tanggalMasuk').value = data.tanggal_masuk?.split(' ')[0] || '';
            document.getElementById('lokasiBarang').value = data.lokasi_id || data.barang_lokasis?.[0]?.lokasi_id || '';
            document.getElementById('satuanBarang').value = data.satuan_id || '';
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
            const e = window.escapeHtml;
            const kondisi = [
                { label: 'Baik', count: data.baik, cls: 'badge-baik' },
                { label: 'Rusak', count: data.rusak, cls: 'badge-rusak' },
                { label: 'Rusak Berat', count: data.rusak_berat, cls: 'badge-rusakberat' },
            ];
            const lokasiNama = e(data.lokasi?.nama_lokasi || data.barang_lokasis?.[0]?.lokasi?.nama_lokasi || '-');
            document.getElementById('detailContent').innerHTML = `
                <div class="flex flex-col lg:flex-row gap-6">
                    <div class="shrink-0 space-y-3" style="width:96px">
                        <img src="${fotoUrl || placeholder}" alt="Foto ${e(data.nama_barang)}" class="w-24 h-24 object-cover rounded-lg border border-gray-200 bg-gray-50" onerror="this.src='${placeholder}'">
                        <a href="/barang/${e(data.kode_barang)}/qr" class="btn-outline text-sm px-3 py-1.5 w-full flex items-center justify-center gap-1.5" target="_blank">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V4zM3 16a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H4a1 1 0 01-1-1v-4zm10-1a1 1 0 00-1 1v1h-1a1 1 0 000 2h1v1a1 1 0 002 0v-1h1a1 1 0 000-2h-1v-1a1 1 0 00-1-1z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13v4m-2-2h4"/></svg>
                            Download QR
                        </a>
                        <a href="/barang/print-label?kodes=${e(data.kode_barang)}" target="_blank" class="btn-outline text-sm px-3 py-1.5 w-full flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 14h12v6H6z"/></svg>
                            Cetak Label
                        </a>
                    </div>
                    <div class="flex-1 min-w-0 space-y-4">
                        <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                            <div class="col-span-2">
                                <span class="text-gray-500 text-xs">Nama Barang</span>
                                <p class="font-semibold text-gray-800">${e(data.nama_barang)}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-xs">Kode Barang</span>
                                <p class="font-mono font-medium">${e(data.kode_barang)}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-xs">Kategori</span>
                                <p class="font-medium">${e(data.kategori?.nama_kategori) || '-'}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-xs">Lokasi</span>
                                <p class="font-medium">${lokasiNama}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-xs">Sumber</span>
                                <p class="font-medium">${e(data.sumber?.nama_sumber) || '-'}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-xs">Jumlah Total</span>
                                <p class="font-semibold text-lg">${data.jumlah} ${data.satuan?.nama_satuan||''}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-xs">Tanggal Masuk</span>
                                <p class="font-medium">${e(data.tanggal_masuk) || '-'}</p>
                            </div>
                            <div class="col-span-2">
                                <span class="text-gray-500 text-xs">Keterangan</span>
                                <p class="font-medium">${e(data.keterangan) || '-'}</p>
                            </div>
                        </div>
                        <div class="border-t border-gray-100 pt-3">
                            <span class="text-xs text-gray-500 block mb-2">Rincian Kondisi</span>
                            <div class="flex flex-wrap gap-2">${kondisi.map(k => `<span class="${k.cls}">${e(k.label)}: ${k.count}</span>`).join('')}</div>
                        </div>
                    </div>
                </div>
            `;
            openModal('detailModal');
        });
    }

    function hapusBarang(id, nama) {
        if (!confirm(`Yakin ingin menghapus barang "${nama || ''}" ?`)) return;
        fetch(`/barang/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        })
        .then(async r => {
            const data = await r.json().catch(() => ({}));
            if (r.ok && data.success) { window.toast(data.message || 'Berhasil dihapus', 'success'); if(window.refreshPage){window.refreshPage()}else{location.reload()}; }
            else window.toast(data.message || 'Gagal menghapus', 'error');
        })
        .catch(() => window.toast('Gagal menghapus', 'error'));
    }

    let importRows = [];
    let importLokasiId = null;

    document.getElementById('fileImport').addEventListener('change', function() {
        const file = this.files[0];
        document.getElementById('importFileName').textContent = file ? file.name : '';
        document.getElementById('previewBtn').disabled = !file;
    });

    document.getElementById('previewBtn').addEventListener('click', function() {
        const lokasiSel = document.getElementById('importLokasi');
        if (!lokasiSel.value) { window.toast('Pilih lokasi tujuan terlebih dahulu.', 'error'); lokasiSel.focus(); return; }
        const file = document.getElementById('fileImport').files[0];
        if (!file) return;
        if (file.size > 5 * 1024 * 1024) { window.toast('File terlalu besar (maks 5MB)', 'error'); return; }
        const btn = this;
        window.setBtnLoading(btn, true, 'Memproses...');
        const reader = new FileReader();
        reader.onload = function(e) {
            window.setBtnLoading(btn, false);
            const text = e.target.result;
            const lines = text.split(/\r?\n/);
            let ruang = null;
            let dataStart = false;
            let colIdx = null;
            let delimiter = ',';
            const headerLine = lines.find(l => l.toLowerCase().includes('nama_barang') || l.toLowerCase().includes('nama barang'));
            if (headerLine && (headerLine.split(';').length > headerLine.split(',').length)) delimiter = ';';
            const rows = [];

            for (let i = 0; i < lines.length; i++) {
                const cols = lines[i].split(delimiter).map(c => c.trim().replace(/^"(.*)"$/, '$1'));
                const joined = cols.join(' ');

                if (!ruang) {
                    const m = joined.match(/Ruang:\s*(.+)/i);
                    if (m) ruang = m[1].trim();
                }

                const lower = cols.map(c => c.toLowerCase());
                const joinedLower = lower.join(',');

                if (!dataStart) {
                    if (joinedLower.includes('nama_barang') || ((lower[1] ?? '') === 'no' && (lower[2] ?? '') === 'nama barang')) {
                        dataStart = true;
                        colIdx = {};
                        lower.forEach((c, i) => {
                            const key = c.replace(/[_\-\s]+/g, '').toLowerCase();
                            if (key === 'no' || key === 'nomor') colIdx.no = i;
                            else if (key === 'namabarang' || key === 'nama') colIdx.nama = i;
                            else if (key === 'jumlah') colIdx.jumlah = i;
                            else if (key === 'baik') colIdx.baik = i;
                            else if (key === 'rusakberat') colIdx.rusakBerat = i;
                            else if (key === 'rusak') colIdx.rusak = i;
                            else if (key === 'keterangan' || key === 'ket') colIdx.keterangan = i;
                            else if (key === 'sumber') colIdx.sumber = i;
                            else if (key === 'satuan') colIdx.satuan = i;
                        });
                    }
                    continue;
                }

                if (!cols.some(c => c)) continue;

                const no = colIdx?.no !== undefined ? (cols[colIdx.no] ?? '') : (cols[1] ?? '');
                const nama = colIdx?.nama !== undefined ? (cols[colIdx.nama] ?? '') : (cols[2] ?? '');
                if (!isNaN(parseFloat(no)) && nama && nama.trim()) {
                    function parseJumlahSatuan(raw) {
                        let s = String(raw || '').trim();
                        if (!s || s === '-' || s === '—') return { jumlah: 1, satuan: 'pcs' };
                        let isApprox = /^(Lebih dari|lebih dari|±|~|>)/.test(s);
                        if (isApprox) s = s.replace(/^(Lebih dari|lebih dari|±|~|>)\s*/, '');
                        let m = s.match(/^(\d+(?:[.,]\d+)?)\s*([a-zA-Z]+)?/);
                        if (m) {
                            let j = parseInt(m[1].replace(',', '')) || 1;
                            let su = (m[2] || 'pcs').toLowerCase();
                            const map = {pcs:'pcs',buah:'pcs',biji:'pcs',bh:'pcs',unit:'pcs',dus:'dus',kardus:'dus',karton:'dus',box:'dus',kotak:'dus',pack:'pack',pak:'pack',bendel:'pack',bendle:'pack',bundle:'pack',lusin:'lusin',lsn:'lusin',dozen:'lusin',kodi:'kodi',rim:'rim',gross:'gross',set:'set',stel:'set',lembar:'lembar',lbr:'lembar',roll:'roll',gulung:'roll',meter:'meter',m:'meter',karung:'karung',sak:'karung',kaleng:'kaleng',botol:'botol',btl:'botol',batang:'batang',btg:'batang'};
                            su = map[su] || su;
                            return { jumlah: j, satuan: su, isApprox };
                        }
                        let n = parseInt(s);
                        if (!isNaN(n)) return { jumlah: n, satuan: 'pcs', isApprox: true };
                        return { jumlah: 1, satuan: 'pcs', isApprox: true };
                    }
                    const rawJumlah = cols[colIdx?.jumlah ?? 3] ?? '';
                    const parsed = parseJumlahSatuan(rawJumlah);
                    const jml = parsed.jumlah;
                    const parsedSatuan = colIdx?.satuan !== undefined ? (cols[colIdx.satuan] ?? '').trim() : '';
                    const satuanStr = parsedSatuan || parsed.satuan;
                    const b = parseInt(cols[colIdx?.baik ?? 4]) || 0;
                    const rs = parseInt(cols[colIdx?.rusak ?? 5]) || 0;
                    const rb = parseInt(cols[colIdx?.rusakBerat ?? 6]) || 0;
                    let baik = b, rusak = rs, rusakBerat = rb;
                    if (b + rs + rb === 0) baik = jml;
                    rows.push({
                        nama: nama.trim(),
                        jumlah: jml,
                        baik,
                        rusak,
                        rusakBerat,
                        keterangan: colIdx?.keterangan !== undefined ? (cols[colIdx.keterangan] ?? '') : (cols[7] ?? ''),
                        sumber: colIdx?.sumber !== undefined ? (cols[colIdx.sumber] ?? '').trim() : '',
                        satuan: satuanStr,
                    });
                }
            }

            if (rows.length === 0) { window.toast('Tidak ada data yang ditemukan di file CSV.', 'error'); return; }
            if (rows.length > 500) { window.toast('Maksimal 500 baris per import. File ini memiliki ' + rows.length + ' baris — hanya 500 pertama akan diproses.', 'warning'); rows.splice(500); }

            importRows = rows;
            document.getElementById('importRuang').textContent = lokasiSel.options[lokasiSel.selectedIndex].text;
            document.getElementById('importRowCount').textContent = rows.length;

            const kategoris = @json($kategoris);
            const sumbers = @json($sumbers);
            const satuans = @json($satuans ?? []);
            const defaultKat = document.getElementById('importKategori').value;
            const defaultSumber = document.getElementById('importSumber').value;
            const tbody = document.getElementById('importPreviewBody');
            const sumberById = {};
            sumbers.forEach(s => sumberById[s.id] = s.nama_sumber);
            const sumberByName = {};
            sumbers.forEach(s => sumberByName[s.nama_sumber.toLowerCase()] = s.id);
            const satuanByName = {};
            satuans.forEach(s => satuanByName[s.nama_satuan.toLowerCase()] = s.id);
            const esc = window.escapeHtml;
            tbody.innerHTML = rows.map((r, i) => {
                const katOpts = kategoris.map(k =>
                    `<option value="${k.id}" ${k.id == defaultKat ? 'selected' : ''}>${esc(k.nama_kategori)}</option>`
                ).join('');
                const rowSumberId = sumberByName[(r.sumber || '').toLowerCase()] || defaultSumber;
                const srcOpts = `<option value="">Pilih</option>` + sumbers.map(s =>
                    `<option value="${s.id}" ${String(s.id) === String(rowSumberId) ? 'selected' : ''}>${esc(s.nama_sumber)}</option>`
                ).join('');
                const rowSatuanId = satuanByName[(r.satuan || '').toLowerCase()] || '';
                const satOpts = `<option value="">Pilih</option>` + satuans.map(s =>
                    `<option value="${s.id}" ${String(s.id) === String(rowSatuanId) ? 'selected' : ''}>${esc(s.nama_satuan)}</option>`
                ).join('');
                const total = (r.baik || 0) + (r.rusak || 0) + (r.rusakBerat || 0);
                const mismatch = total !== r.jumlah;
                const jmlCell = `<span class="import-jml font-semibold">${r.jumlah}</span>` + (mismatch
                    ? `<span class="import-warn inline-flex items-center gap-1 text-red-600 font-semibold ml-1" title="Baik+Rusak+Rusak Berat (${total}) tidak sama dengan Jumlah (${r.jumlah})">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.702c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                      </span>`
                    : '');
                return `<tr>
                    <td class="px-3 py-2 text-gray-500 text-center">${i + 1}</td>
                    <td class="px-3 py-2 font-medium text-gray-800">${esc(r.nama)}</td>
                    <td class="px-3 py-2 text-center whitespace-nowrap">${jmlCell}</td>
                    <td class="px-3 py-2 text-center">
                        <input type="number" min="0" value="${r.baik || 0}" class="import-kondisi import-baik w-16 px-2 py-1 border border-gray-300 rounded text-center text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </td>
                    <td class="px-3 py-2 text-center">
                        <input type="number" min="0" value="${r.rusak || 0}" class="import-kondisi import-rusak w-16 px-2 py-1 border border-gray-300 rounded text-center text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </td>
                    <td class="px-3 py-2 text-center">
                        <input type="number" min="0" value="${r.rusakBerat || 0}" class="import-kondisi import-rusakberat w-16 px-2 py-1 border border-gray-300 rounded text-center text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </td>
                    <td class="px-3 py-2">
                        <select class="import-kategori form-input w-full px-2 py-1 border border-gray-300 rounded text-sm">
                            <option value="">Pilih</option>
                            ${katOpts}
                        </select>
                    </td>
                    <td class="px-3 py-2">
                        <select class="import-sumber form-input w-full px-2 py-1 border border-gray-300 rounded text-sm">
                            ${srcOpts}
                        </select>
                    </td>
                    <td class="px-3 py-2">
                        <select class="import-satuan form-input w-full px-2 py-1 border border-gray-300 rounded text-sm">
                            ${satOpts}
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

    function refreshKondisiRow(i) {
        const tr = document.querySelectorAll('#importPreviewBody tr')[i];
        if (!tr) return;
        const baik = Math.max(0, parseInt(tr.querySelector('.import-baik').value) || 0);
        const rusak = Math.max(0, parseInt(tr.querySelector('.import-rusak').value) || 0);
        const rusakBerat = Math.max(0, parseInt(tr.querySelector('.import-rusakberat').value) || 0);
        const jumlah = parseInt(tr.querySelector('.import-jml').textContent) || 0;
        const warn = tr.querySelector('.import-warn');
        const total = baik + rusak + rusakBerat;
        if (total !== jumlah) {
            warn.classList.remove('hidden');
            warn.title = 'Baik+Rusak+Rusak Berat (' + total + ') tidak sama dengan Jumlah (' + jumlah + ')';
        } else {
            warn.classList.add('hidden');
        }
    }

    document.getElementById('importPreviewBody').addEventListener('input', function(e) {
        if (e.target.classList.contains('import-kondisi')) {
            const tr = e.target.closest('tr');
            const idx = Array.prototype.indexOf.call(tr.parentNode.children, tr);
            refreshKondisiRow(idx);
        }
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
        const btn = this;
        const trs = document.querySelectorAll('#importPreviewBody tr');
        if (trs.length === 0) { window.toast('Tidak ada barang untuk diimport.', 'warning'); return; }

        const rows = [];
        let valid = true;
        trs.forEach((tr, i) => {
            const sel = tr.querySelector('.import-kategori');
            const katId = sel.value;
            if (!katId) { valid = false; sel.classList.add('border-red-400'); }
            else { sel.classList.remove('border-red-400'); }
            const baik = Math.max(0, parseInt(tr.querySelector('.import-baik').value) || 0);
            const rusak = Math.max(0, parseInt(tr.querySelector('.import-rusak').value) || 0);
            const rusakBerat = Math.max(0, parseInt(tr.querySelector('.import-rusakberat').value) || 0);
            rows.push({
                nama_barang: importRows[i].nama,
                kategori_id: katId || null,
                jumlah: importRows[i].jumlah,
                baik,
                rusak,
                rusak_berat: rusakBerat,
                keterangan: importRows[i].keterangan,
                sumber_id: tr.querySelector('.import-sumber').value || null,
                satuan_id: tr.querySelector('.import-satuan').value || null,
            });
        });

        if (!valid) { window.toast('Semua barang harus memiliki kategori.', 'error'); return; }

        const lokasi_id = document.getElementById('importLokasi').value;
        const sumber_id = document.getElementById('importSumber').value;
        window.setBtnLoading(btn, true, 'Mengimpor...');
        fetch('/barang/import-csv', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify({ lokasi_id, sumber_id: sumber_id || null, rows })
        })
        .then(async r => {
            const data = await r.json().catch(() => ({}));
            if (!r.ok) { if (r.status === 422 && data.errors) window.showFieldErrors(data.errors); window.toast(data.message || 'Gagal import', 'error'); return; }
            if (data.success) { window.toast(data.message || 'Import berhasil!', 'success'); if (data.errors && data.errors.length) data.errors.forEach(e => window.toast(e, 'warning')); window.refreshPage(600); }
            else window.toast(data.message || 'Gagal import', 'error');
        })
        .catch(() => window.toast('Gagal import', 'error'))
        .finally(() => window.setBtnLoading(btn, false));
    });

    function downloadTemplate() {
        const csv = "No,Nama Barang,Jumlah,Satuan,Baik,Rusak,Rusak Berat,Keterangan,Sumber\n1,Contoh Proyektor,5,pcs,4,1,0,Keterangan,BOS\n2,Contoh Gantungan,4,lusin,4,0,0,,PCS\n";
        const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a'); a.href = url; a.download = 'template_import_barang.csv'; a.click();
        URL.revokeObjectURL(url);
    }

    function toggleAllCheckbox(src) {
        if (src.checked) {
            ALL_KODES.forEach(k => selectedKodes.add(k));
        } else {
            selectedKodes.clear();
        }
        syncCheckboxUI();
    }

    function syncCheckboxUI() {
        document.querySelectorAll('.cb-barang').forEach(cb => {
            cb.checked = selectedKodes.has(cb.value);
        });
        const checkAll = document.getElementById('checkAll');
        if (checkAll) {
            checkAll.checked = ALL_KODES.length > 0 && ALL_KODES.every(k => selectedKodes.has(k));
        }
    }

    document.getElementById('barangTable')?.addEventListener('change', function(e) {
        if (e.target && e.target.classList && e.target.classList.contains('cb-barang')) {
            if (e.target.checked) selectedKodes.add(e.target.value);
            else selectedKodes.delete(e.target.value);
            const checkAll = document.getElementById('checkAll');
            if (checkAll) {
                checkAll.checked = ALL_KODES.length > 0 && ALL_KODES.every(k => selectedKodes.has(k));
            }
        }
    });

    function cetakTerpilih() {
        const codes = Array.from(selectedKodes);
        if (codes.length === 0) { window.toast('Pilih barang terlebih dahulu.', 'warning'); return; }
        window.open('/barang/print-label?kodes=' + encodeURIComponent(codes.join(',')), '_blank');
    }

    function openModal(id) {
        if (id === 'barangModal' && !editingId) resetBarangForm();
        const el = document.getElementById(id);
        el.classList.remove('hidden');
        setTimeout(() => { const f = el.querySelector('input[required], select[required]'); if (f) f.focus(); }, 80);
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        window.clearFieldErrors();
    }

    function openMutasi(id) {
        fetch('/barang/' + id + '/edit', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(b => {
                document.getElementById('mutasiBarangId').value = b.id;
                document.getElementById('mutasiNamaBarang').textContent = b.nama_barang + '  •  ' + b.kode_barang;
                document.getElementById('mutasiJumlah').value = b.jumlah;
                document.getElementById('mutasiBaik').value = b.baik || 0;
                document.getElementById('mutasiRusak').value = b.rusak || 0;
                document.getElementById('mutasiRusakBerat').value = b.rusak_berat || 0;
                document.getElementById('mutasiKeterangan').value = '';
                document.getElementById('mutasiLokasiTujuan').value = '';
                openModal('mutasiModal');
            });
    }

    document.getElementById('mutasiForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        window.setBtnLoading(btn, true, 'Menyimpan...');
        const id = document.getElementById('mutasiBarangId').value;
        fetch('/barang/' + id + '/mutasi', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                lokasi_tujuan: document.getElementById('mutasiLokasiTujuan').value,
                jumlah: parseInt(document.getElementById('mutasiJumlah').value) || 0,
                baik: parseInt(document.getElementById('mutasiBaik').value) || 0,
                rusak: parseInt(document.getElementById('mutasiRusak').value) || 0,
                rusak_berat: parseInt(document.getElementById('mutasiRusakBerat').value) || 0,
                keterangan: document.getElementById('mutasiKeterangan').value,
            })
        })
        .then(async r => {
            const data = await r.json().catch(() => ({}));
            if (!r.ok) { if (r.status === 422 && data.errors) window.showFieldErrors(data.errors); window.toast(data.message || 'Gagal melakukan mutasi', 'error'); return; }
            if (data.success) { window.toast('Mutasi berhasil!', 'success'); if(window.refreshPage){window.refreshPage()}else{location.reload()}; }
            else window.toast(data.message || 'Gagal melakukan mutasi', 'error');
        })
        .catch(() => window.toast('Gagal melakukan mutasi', 'error'))
        .finally(() => window.setBtnLoading(btn, false));
    });

    let dt;
    const barangTableEl = document.getElementById('barangTable');
    if (barangTableEl && !barangTableEl.querySelector('td[colspan]') && barangTableEl.querySelectorAll('tbody tr').length) {
        dt = new DataTable('#barangTable', {
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
            columnDefs: [{ orderable: false, targets: [0, 11] }],
        });
        dt.on('draw', function() { syncCheckboxUI(); });
    }

    document.getElementById('filterKategori')?.addEventListener('change', function() {
        if (!dt) return;
        const text = this.options[this.selectedIndex]?.text || '';
        dt.column(4).search(this.value ? text : '').draw();
    });

    document.getElementById('filterLokasi')?.addEventListener('change', function() {
        if (!dt) return;
        const text = this.options[this.selectedIndex]?.text || '';
        dt.column(5).search(this.value ? text : '').draw();
    });
</script>
@endpush
