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
                <input type="text" id="userName" required maxlength="50" autocomplete="name" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nama lengkap">
                <p id="userNameError" class="text-xs text-red-500 mt-1 hidden"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="userEmail" required maxlength="100" autocomplete="email" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="email@contoh.com">
                <p id="userEmailError" class="text-xs text-red-500 mt-1 hidden"></p>
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
                <div class="relative">
                    <input type="password" id="userPassword" autocomplete="new-password" maxlength="100" class="form-input w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Minimal 6 karakter">
                    <button type="button" onclick="togglePassword('userPassword', this)" class="absolute inset-y-0 right-0 px-3 text-gray-400 hover:text-gray-600" title="Lihat password">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1" id="passwordHint">Minimal 6 karakter. Kosongkan jika tidak ingin mengubah.</p>
                <p id="userPasswordError" class="text-xs text-red-500 mt-1 hidden"></p>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeUserModal()" class="btn-secondary text-sm px-4 py-2">Batal</button>
                <button type="submit" id="userSubmitBtn" class="btn-primary text-sm px-4 py-2">Simpan</button>
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
                <div class="relative">
                    <input type="password" id="resetPassValue" required minlength="6" maxlength="100" autocomplete="new-password" class="form-input w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Minimal 6 karakter">
                    <button type="button" onclick="togglePassword('resetPassValue', this)" class="absolute inset-y-0 right-0 px-3 text-gray-400 hover:text-gray-600" title="Lihat password">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
                <p id="resetPassError" class="text-xs text-red-500 mt-1 hidden"></p>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeResetModal()" class="btn-secondary text-sm px-4 py-2">Batal</button>
                <button type="submit" id="resetPassSubmitBtn" class="btn-primary text-sm px-4 py-2">Reset</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const usersTableEl = document.getElementById('usersTable');
    if (usersTableEl && !usersTableEl.querySelector('td[colspan]') && usersTableEl.querySelectorAll('tbody tr').length) {
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
    }

    const csrfUser = () => document.querySelector('meta[name="csrf-token"]').content;
    const post = (url, opts = {}) => fetch(url, {
        method: opts.method || 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfUser(), 'Accept': 'application/json' },
        body: opts.body ? JSON.stringify(opts.body) : undefined,
    }).then(async r => {
        const data = await r.json();
        return { ok: r.ok, status: r.status, data };
    });
    
    function showErr(id, msg) { const el = document.getElementById(id); if (!el) return; if (msg) { el.textContent = msg; el.classList.remove('hidden'); } else { el.textContent = ''; el.classList.add('hidden'); } }
    function clearUserErrors() { showErr('userNameError',''); showErr('userEmailError',''); showErr('userPasswordError',''); document.getElementById('userName').classList.remove('border-red-500'); document.getElementById('userEmail').classList.remove('border-red-500'); }
    function togglePassword(inputId, btn) {
        const inp = document.getElementById(inputId);
        if (!inp) return;
        inp.type = inp.type === 'password' ? 'text' : 'password';
    }

    function openUserModal() {
        clearUserErrors();
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
        setTimeout(() => document.getElementById('userName').focus(), 50);
    }

    function editUser(id) {
        clearUserErrors();
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
                setTimeout(() => document.getElementById('userName').focus(), 50);
            });
    }

    function closeUserModal() {
        document.getElementById('userModal').classList.add('hidden');
        document.getElementById('userPassword').removeAttribute('required');
        document.getElementById('userPassword').closest('div').style.display = '';
        clearUserErrors();
    }

    document.getElementById('userForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearUserErrors();
        const btn = document.getElementById('userSubmitBtn');
        const origText = btn.textContent;
        btn.disabled = true; btn.textContent = 'Menyimpan\u2026';
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
        post(url, { method, body }).then(({ ok, data }) => {
            if (ok && data.success) { window.toast(data.message || 'Berhasil disimpan', 'success'); if(window.refreshPage){window.refreshPage()}else{location.reload()}; }
            else {
                if (data.errors) {
                    if (data.errors.name) { showErr('userNameError', data.errors.name[0]); document.getElementById('userName').classList.add('border-red-500'); }
                    if (data.errors.email) { showErr('userEmailError', data.errors.email[0]); document.getElementById('userEmail').classList.add('border-red-500'); }
                    if (data.errors.password) { showErr('userPasswordError', data.errors.password[0]); }
                    const first = Object.values(data.errors).flat()[0];
                    window.toast(data.message || first || 'Validasi gagal', 'error');
                } else {
                    window.toast(data.message || 'Gagal menyimpan', 'error');
                }
            }
        }).catch(() => window.toast('Terjadi kesalahan, coba lagi.', 'error'))
        .finally(() => { btn.disabled = false; btn.textContent = origText; });
    });

    function resetPassUser(id) {
        showErr('resetPassError','');
        document.getElementById('resetPassUserId').value = id;
        document.getElementById('resetPassValue').value = '';
        document.getElementById('resetPassModal').classList.remove('hidden');
        setTimeout(() => document.getElementById('resetPassValue').focus(), 50);
    }

    function closeResetModal() {
        document.getElementById('resetPassModal').classList.add('hidden');
        showErr('resetPassError','');
    }

    document.getElementById('resetPassForm').addEventListener('submit', function(e) {
        e.preventDefault();
        showErr('resetPassError','');
        const btn = document.getElementById('resetPassSubmitBtn');
        const origText = btn.textContent;
        btn.disabled = true; btn.textContent = 'Menyimpan\u2026';
        const id = document.getElementById('resetPassUserId').value;
        const password = document.getElementById('resetPassValue').value;
        post('/users/' + id + '/reset-password', { body: { password } })
            .then(({ ok, data }) => {
                if (ok && data.success) { window.toast(data.message || 'Password berhasil direset', 'success'); if(window.refreshPage){window.refreshPage()}else{location.reload()}; }
                else {
                    if (data.errors && data.errors.password) showErr('resetPassError', data.errors.password[0]);
                    window.toast(data.message || 'Gagal reset password', 'error');
                }
            }).catch(() => window.toast('Terjadi kesalahan, coba lagi.', 'error'))
            .finally(() => { btn.disabled = false; btn.textContent = origText; });
    });

    function hapusUser(id, name) {
        if (!confirm('Hapus pengguna "' + name + '"? Tindakan ini tidak bisa dibatalkan.')) return;
        post('/users/' + id, { method: 'DELETE' }).then(({ ok, data }) => {
            if (ok && data.success) { window.toast(data.message || 'Pengguna dihapus', 'success'); if(window.refreshPage){window.refreshPage()}else{location.reload()}; } else window.toast(data.message || 'Gagal menghapus', 'error');
        }).catch(() => window.toast('Terjadi kesalahan, coba lagi.', 'error'));
    }
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { closeUserModal(); closeResetModal(); } });
</script>
@endpush