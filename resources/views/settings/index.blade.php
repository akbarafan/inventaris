@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Pengaturan</h2>
        <p class="text-sm text-gray-500 mt-1">Identitas sekolah dan branding aplikasi</p>
    </div>

    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        <div class="space-y-6 lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="font-semibold text-gray-800">Identitas Sekolah</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Sekolah</label>
                    <input type="text" name="nama_sekolah" required value="{{ $settings['nama_sekolah'] ?? '' }}" placeholder="Contoh: SMK Negeri 1 Surabaya" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Singkat</label>
                        <input type="text" name="nama_singkat" value="{{ $settings['nama_singkat'] ?? '' }}" placeholder="Contoh: SMK" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                        <input type="text" name="telepon" value="{{ $settings['telepon'] ?? '' }}" placeholder="Contoh: 031-12345678" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <input type="text" name="alamat" value="{{ $settings['alamat'] ?? '' }}" placeholder="Alamat lengkap sekolah" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teks Footer</label>
                    <input type="text" name="footer_text" value="{{ $settings['footer_text'] ?? '' }}" placeholder="Contoh: Sistem Informasi Inventaris - 2026" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Logo Aplikasi</h3>
                <div class="text-center mb-4">
                    @php $logo = $settings['logo'] ?? ''; @endphp
                    <img id="logoPreview" src="{{ $logo ? asset('storage/' . $logo) : asset('images/logo-smk.png') }}" alt="Logo" class="w-28 h-28 object-contain rounded-full mx-auto border-4 border-gray-100 bg-white p-1">
                </div>
                <input type="file" name="logo" id="logoInput" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-400 mt-2">JPG/PNG/WebP maks 2MB. Kosongkan untuk mempertahankan logo saat ini.</p>
            </div>
        </div>

        <div class="lg:col-span-3 flex justify-end">
            <button type="submit" class="btn-primary px-5 py-2.5">Simpan Pengaturan</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('logoInput').addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) { alert('Logo maksimal 2MB'); this.value = ''; return; }
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
</script>
@endpush