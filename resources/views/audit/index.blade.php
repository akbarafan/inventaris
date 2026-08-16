@extends('layouts.app')

@section('title', 'Riwayat Aktivitas')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Riwayat Aktivitas</h2>
        <p class="text-sm text-gray-500 mt-1">Jejak audit seluruh aktivitas pengguna dalam sistem</p>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ url('/audit') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pengguna</label>
            <select name="user_id" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">Semua Pengguna</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->role }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tindakan</label>
            <select name="action" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">Semua Tindakan</option>
                @foreach(['created', 'updated', 'deleted', 'scan', 'login', 'logout', 'import', 'mutasi'] as $act)
                <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ ucfirst($act) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="btn-primary text-sm px-4 py-2">Filter</button>
            <a href="{{ url('audit') }}" class="btn-secondary text-sm px-4 py-2">Reset</a>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="auditTable" class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-left">
                        <th class="px-4 py-3 font-medium w-24">Waktu</th>
                        <th class="px-4 py-3 font-medium">Pengguna</th>
                        <th class="px-4 py-3 font-medium">Tindakan</th>
                        <th class="px-4 py-3 font-medium">Keterangan</th>
                        <th class="px-4 py-3 font-medium">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-gray-800">{{ $log->user->name ?? 'Sistem' }}</span>
                            <span class="text-[10px] text-gray-400 ml-1">{{ $log->user->role ?? '' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @php $colors = [
                                'created' => 'bg-green-50 text-green-700',
                                'updated' => 'bg-blue-50 text-blue-700',
                                'deleted' => 'bg-red-50 text-red-700',
                                'scan' => 'bg-indigo-50 text-indigo-700',
                                'login' => 'bg-gray-100 text-gray-600',
                                'logout' => 'bg-gray-100 text-gray-600',
                                'import' => 'bg-amber-50 text-amber-700',
                                'mutasi' => 'bg-purple-50 text-purple-700',
                            ]; @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors[$log->action] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($log->action) }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $log->description }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $log->ip_address ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">Belum ada aktivitas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    new DataTable('#auditTable', {
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
        order: [[0, 'desc']],
        columnDefs: [{ orderable: false, targets: [4] }],
    });
</script>
@endpush