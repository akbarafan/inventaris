@extends('layouts.app')

@section('title', 'Pengguna')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Data Pengguna</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola akun petugas, admin, dan hak akses</p>
        </div>
        <button onclick="openUserModal()" class="btn-primary text-sm px-4 py-2 inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Pengguna
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="usersTable" class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-left">
                        <th class="px-4 py-3 font-medium">Nama</th>
                        <th class="px-4 py-3 font-medium">Email</th>
                        <th class="px-4 py-3 font-medium">Role</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $u)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 text-[11px] font-bold shrink-0">{{ substr($u->name, 0, 1) }}</div>
                                <span class="font-medium text-gray-800">{{ $u->name }}</span>
                                @if($u->id === Auth::id())<span class="text-[10px] font-medium text-gray-400">(Anda)</span>@endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $u->email }}</td>
                        <td class="px-4 py-3">
                            @if($u->role === 'admin')
                                <span class="badge-baik">Admin</span>
                            @elseif($u->role === 'petugas')
                                <span class="badge-masuk">Petugas</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">User</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $u->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="editUser({{ $u->id }})" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button onclick="resetPassUser({{ $u->id }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Reset Password">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                </button>
                                @if($u->id !== auth()->id())
                                <button onclick="hapusUser({{ $u->id }}, '{{ $u->name }}')" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
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

{{-- Modal Tambah/Edit Pengguna --}}
<div id="userModal" class="modal-overlay hidden">
    <div class="modal-content max-w-lg">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800" id="userModalTitle">Tambah Pengguna</h3>
            <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="userForm" class="p-5 space-y-4">
            <input type="hidden" id="userId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" id="userName" required class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="userEmail" required class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <select id="userRole" required class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="petugas">Petugas</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="userActive" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <div id="passwordField">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="userPassword" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-400 mt-1" id="passwordHint">Minimal 6 karakter. Kosongkan jika tidak ingin mengubah.</p>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeUserModal()" class="btn-secondary text-sm px-4 py-2">Batal</button>
                <button type="submit" class="btn-primary text-sm px-4 py-2">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Reset Password --}}
<div id="resetPassModal" class="modal-overlay hidden">
    <div class="modal-content max-w-md">
        <div class="flex items-center justify-between p-5 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Reset Password</h3>
            <button onclick="closeResetModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="resetPassForm" class="p-5 space-y-4">
            <input type="hidden" id="resetPassUserId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" id="resetPassValue" required minlength="6" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeResetModal()" class="btn-secondary text-sm px-4 py-2">Batal</button>
                <button type="submit" class="btn-primary text-sm px-4 py-2">Reset</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    new DataTable('#usersTable', {
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
        order: [[0, 'asc']],
        columnDefs: [{ orderable: false, targets: [4] }],
    });

    const csrfUser = () => document.querySelector('meta[name="csrf-token"]').content;
    const post = (url, opts = {}) => fetch(url, {
        method: opts.method || 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfUser(), 'Accept': 'application/json' },
        body: opts.body ? JSON.stringify(opts.body) : undefined,
    }).then(r => r.json());

    function openUserModal() {
        document.getElementById('userModalTitle').textContent = 'Tambah Pengguna';
        document.getElementById('userId').value = '';
        document.getElementById('userName').value = '';
        document.getElementById('userEmail').value = '';
        document.getElementById('userPassword').value = '';
        document.getElementById('userPassword').required = true;
        document.getElementById('passwordHint').textContent = 'Minimal 6 karakter.';
        document.getElementById('userRole').value = 'petugas';
        document.getElementById('userActive').value = '1';
        document.getElementById('passwordField').style.display = '';
        document.getElementById('userModal').classList.remove('hidden');
    }

    function editUser(id) {
        fetch('/users/' + id + '/edit', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(u => {
                document.getElementById('userModalTitle').textContent = 'Edit Pengguna';
                document.getElementById('userId').value = u.id;
                document.getElementById('userName').value = u.name;
                document.getElementById('userEmail').value = u.email;
                document.getElementById('userRole').value = u.role;
                document.getElementById('userActive').value = u.is_active ? '1' : '0';
                document.getElementById('userPassword').value = '';
                document.getElementById('userPassword').required = false;
                document.getElementById('passwordHint').textContent = 'Kosongkan jika tidak ingin mengubah password.';
                document.getElementById('userModal').classList.remove('hidden');
                document.getElementById('userPassword').closest('div').style.display = 'none';
            });
    }

    function closeUserModal() {
        document.getElementById('userModal').classList.add('hidden');
        document.getElementById('userPassword').removeAttribute('required');
        document.getElementById('userPassword').closest('div').style.display = '';
    }

    document.getElementById('userForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('userId').value;
        const body = {
            name: document.getElementById('userName').value,
            email: document.getElementById('userEmail').value,
            role: document.getElementById('userRole').value,
            is_active: document.getElementById('userActive').value === '1',
            password: document.getElementById('userPassword').value,
        };
        const url = id ? '/users/' + id : '/users';
        const method = id ? 'PUT' : 'POST';
        post(url, { method, body }).then(res => {
            if (res.success) { location.reload(); } else { alert(res.message || 'Gagal menyimpan'); }
        });
    });

    function resetPassUser(id) {
        document.getElementById('resetPassUserId').value = id;
        document.getElementById('resetPassValue').value = '';
        document.getElementById('resetPassModal').classList.remove('hidden');
    }

    function closeResetModal() {
        document.getElementById('resetPassModal').classList.add('hidden');
    }

    document.getElementById('resetPassForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('resetPassUserId').value;
        const password = document.getElementById('resetPassValue').value;
        post('/users/' + id + '/reset-password', { body: { password } })
            .then(res => { if (res.success) location.reload(); else alert(res.message || 'Gagal reset password'); });
    });

    function hapusUser(id, name) {
        if (!confirm('Hapus pengguna "' + name + '"? Tindakan ini tidak bisa dibatalkan.')) return;
        post('/users/' + id, { method: 'DELETE' }).then(res => {
            if (res.success) location.reload(); else alert(res.message || 'Gagal menghapus');
        });
    }
</script>
@endpush