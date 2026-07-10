@extends('layouts.app')

@section('title', 'Scan QR Code')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Scan QR Code</h2>
        <p class="text-sm text-gray-500 mt-1">Scan kode QR untuk melihat detail barang</p>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-gray-200" x-data="{ tab: 'camera' }" @@tab-change.window="tab = $event.detail">
        <nav class="flex gap-4">
            <button @@click="tab = 'camera'; startCamera()" :class="tab === 'camera' ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent hover:text-gray-700'" class="px-1 py-3 text-sm font-medium border-b-2 transition-colors">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Scan Kamera
            </button>
            <button @@click="tab = 'upload'; stopCamera()" :class="tab === 'upload' ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent hover:text-gray-700'" class="px-1 py-3 text-sm font-medium border-b-2 transition-colors">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Upload Gambar
            </button>
            <button @@click="tab = 'manual'; stopCamera()" :class="tab === 'manual' ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent hover:text-gray-700'" class="px-1 py-3 text-sm font-medium border-b-2 transition-colors">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Input Manual
            </button>
            <button @@click="tab = 'riwayat'; stopCamera()" :class="tab === 'riwayat' ? 'text-blue-600 border-blue-600' : 'text-gray-500 border-transparent hover:text-gray-700'" class="px-1 py-3 text-sm font-medium border-b-2 transition-colors">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Riwayat Scan
            </button>
        </nav>

        {{-- Tab Camera --}}
        <div x-show="tab === 'camera'" class="py-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="relative max-w-md mx-auto">
                    <video id="qr-video" class="w-full rounded-lg border border-gray-200 bg-black" autoplay playsinline></video>
                    <canvas id="qr-canvas" class="hidden"></canvas>
                    <div id="cameraError" class="hidden mt-3 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm text-center">
                        Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.
                    </div>
                    <p class="text-sm text-gray-500 text-center mt-3">Arahkan kode QR ke kamera</p>
                </div>
            </div>
        </div>

        {{-- Tab Upload --}}
        <div x-show="tab === 'upload'" class="py-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="max-w-md mx-auto space-y-4">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors">
                        <input type="file" id="qr-image-input" accept="image/*" class="hidden" onchange="decodeImageQR(event)">
                        <button onclick="document.getElementById('qr-image-input').click()" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="block">Klik untuk upload gambar QR</span>
                        </button>
                    </div>
                    <div id="imagePreview" class="hidden">
                        <img id="qr-image-display" class="max-w-full rounded-lg border border-gray-200 mx-auto" alt="QR Image">
                    </div>
                    <p id="imageResult" class="text-sm text-center text-gray-500"></p>
                </div>
            </div>
        </div>

        {{-- Tab Manual --}}
        <div x-show="tab === 'manual'" class="py-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="max-w-md mx-auto space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Masukkan Kode Barang</label>
                        <div class="flex gap-2">
                            <input type="text" id="manualKode" placeholder="Contoh: BRG-001" class="form-input flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button onclick="cariManual()" class="btn-primary text-sm px-4 py-2">Cari</button>
                        </div>
                    </div>
                    <p id="manualResult" class="text-sm text-center text-gray-500"></p>
                </div>
            </div>
        </div>

        {{-- Tab Riwayat --}}
        <div x-show="tab === 'riwayat'" class="py-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table id="scanRiwayatTable" class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600 text-left">
                                <th class="px-4 py-3 font-medium">No</th>
                                <th class="px-4 py-3 font-medium">Kode Barang</th>
                                <th class="px-4 py-3 font-medium">Nama Barang</th>
                                <th class="px-4 py-3 font-medium">Waktu</th>
                                <th class="px-4 py-3 font-medium">Device</th>
                                <th class="px-4 py-3 font-medium">IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($logs ?? [] as $log)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-mono text-xs">{{ $log->kode_barang ?? '-' }}</td>
                                <td class="px-4 py-3 font-medium">{{ $log->barang->nama_barang ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs">{{ $log->created_at ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs">{{ $log->device ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs">{{ $log->ip_address ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
    let scanning = false;
    let stream = null;

    function startCamera() {
        const video = document.getElementById('qr-video');
        const canvas = document.getElementById('qr-canvas');
        const ctx = canvas.getContext('2d');
        const errDiv = document.getElementById('cameraError');

        if (scanning || stream) return;

        if (navigator.mediaDevices?.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(s => {
                stream = s;
                video.srcObject = stream;
                video.play();
                scanning = true;
                errDiv.classList.add('hidden');
                scanFrame();
            })
            .catch(() => {
                errDiv.classList.remove('hidden');
            });
        } else {
            errDiv.classList.remove('hidden');
        }

        function scanFrame() {
            if (!scanning) return;
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height);
                if (code) {
                    var d = code.data;
                    var parts = d.split('/');
                    var kode = parts[parts.length - 1];
                    window.location.href = '/scan/' + encodeURIComponent(kode);
                    return;
                }
            }
            requestAnimationFrame(scanFrame);
        }
    }

    function stopCamera() {
        scanning = false;
        if (stream) {
            stream.getTracks().forEach(t => t.stop());
            stream = null;
        }
    }

    function decodeImageQR(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('qr-image-display');
            img.src = e.target.result;
            document.getElementById('imagePreview').classList.remove('hidden');

            const image = new Image();
            image.onload = function() {
                const canvas = document.createElement('canvas');
                canvas.width = image.width;
                canvas.height = image.height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(image, 0, 0);
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height);
                if (code) {
                    var d = code.data;
                    var parts = d.split('/');
                    var kode = parts[parts.length - 1];
                    window.location.href = '/scan/' + encodeURIComponent(kode);
                } else {
                    document.getElementById('imageResult').textContent = 'Tidak dapat membaca QR code dari gambar ini';
                    document.getElementById('imageResult').classList = 'text-sm text-center text-red-500';
                }
            };
            image.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    function cariManual() {
        const kode = document.getElementById('manualKode').value.trim();
        if (!kode) { alert('Masukkan kode barang'); return; }
        window.location.href = '/scan/' + encodeURIComponent(kode);
    }

    document.getElementById('manualKode').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') cariManual();
    });

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(startCamera, 500);
        new DataTable('#scanRiwayatTable', {
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
            order: [[3, 'desc']],
            columnDefs: [{ orderable: false, targets: [0, 5] }],
        });
    });
</script>
@endpush
