<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2563EB">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" href="/images/logo-smk.png">
    <title>@yield('title', 'Dashboard') - {{ $settings['nama_singkat'] ?? 'Inventaris SMK' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
    <style>
        * { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        body { background: #F8FAFF; }
        div.dt-container .dt-input { border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; line-height: 1.25rem; }
        div.dt-container .dt-input:focus { outline: none; box-shadow: 0 0 0 2px #bfdbfe; border-color: #2563eb; }
        div.dt-container select.dt-input { padding-right: 2rem; }
        div.dt-container .dt-paging .dt-paging-button { border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.375rem 0.75rem; font-size: 0.875rem; font-weight: 500; color: #374151; transition: all 0.15s; }
        div.dt-container .dt-paging .dt-paging-button:hover { background-color: #f3f4f6; }
        div.dt-container .dt-paging .dt-paging-button.current { background-color: #2563eb; color: #fff; border-color: #2563eb; }
        div.dt-container .dt-paging .dt-paging-button.current:hover { background-color: #1d4ed8; }
        div.dt-container .dt-paging .dt-paging-button.disabled { opacity: 0.5; cursor: not-allowed; }
        div.dt-container .dt-info { font-size: 0.875rem; color: #6b7280; }
        div.dt-container .dt-length label, div.dt-container .dt-search label { font-size: 0.875rem; color: #4b5563; }
        div.dt-container .dt-search { margin-bottom: 0.75rem; }
        div.dt-container .dt-length { margin-bottom: 0.75rem; }
        .toast-enter { animation: toastIn 0.3s ease-out; }
        @keyframes toastIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .field-error { font-size: 12px; color: #ef4444; margin-top: 4px; }
        .input-error { border-color: #f87171 !important; box-shadow: 0 0 0 2px #fecaca !important; }
    </style>
</head>
<body class="h-full font-sans antialiased text-gray-900">
    <div x-data="{ sidebarOpen: false }" class="flex h-full overflow-hidden">

        {{-- Sidebar --}}
        <div x-show="sidebarOpen" @@click="sidebarOpen = false" class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"></div>
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-[220px] bg-white border-r border-gray-200 flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:z-auto overflow-y-auto">
            <div class="flex items-center gap-2 h-14 px-4 border-b border-gray-200">
                @php $brandLogo = $settings['logo'] ?? ''; @endphp
                <img src="{{ $brandLogo ? asset('storage/' . $brandLogo) : asset('images/logo-smk.png') }}" alt="Logo Sekolah" class="w-7 h-7 object-contain rounded-full shrink-0">
                <span class="text-gray-900 font-bold text-sm truncate">{{ $settings['nama_sekolah'] ?? 'Inventaris Sekolah' }}</span>
            </div>
            <nav class="flex-1 px-2 py-3 space-y-0.5 overflow-y-auto">
                <p class="px-3 pt-2 pb-1 text-[10px] font-bold uppercase tracking-widest text-gray-400">Menu</p>
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('/') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ url('/barang') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('barang*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Data Barang
                </a>
                <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-widest text-gray-400">Pencatatan</p>
                <a href="{{ url('/scan') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('scan*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    Scan QR
                </a>
                <a href="{{ url('/laporan') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('laporan*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Laporan
                </a>
                <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-widest text-gray-400">Master</p>
                <a href="{{ url('/kategori') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('kategori*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    Kategori
                </a>
                <a href="{{ url('/lokasi') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('lokasi*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Lokasi
                </a>
                <a href="{{ url('/sumber') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('sumber*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Sumber
                </a>
                <a href="{{ url('/satuan') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('satuan*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V5a1 1 0 00-1-1H5a1 1 0 00-1 1v10a1 1 0 001 1h9a1 1 0 001-1v-3m-3 0a2 2 0 012 2 2 2 0 01-2 2H9a2 2 0 01-2-2 2 2 0 012-2h4z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h8"/></svg>
                    Satuan
                </a>
                @if(Auth::user()->isAdmin())
                <p class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-widest text-gray-400">Administrasi</p>
                <a href="{{ url('/users') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('users*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Pengguna
                </a>
                <a href="{{ url('/audit') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('audit') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2m-6 0a2 2 0 00-2 2v4h10V7a2 2 0 00-2-2m-6 0a2 2 0 012-2h2a2 2 0 012 2m-6 5h.01M12 14h.01M16 14h.01M12 18h.01M16 18h.01"/></svg>
                    Riwayat Aktivitas
                </a>
                <a href="{{ url('/settings') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('settings*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Pengaturan
                </a>
                @endif
            </nav>
            <div class="border-t border-gray-200 px-3 py-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 text-[11px] font-bold shrink-0">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-800 truncate">{{ Auth::user()->name ?? 'User' }}</p>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-700">{{ Auth::user()->role ?? 'admin' }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-w-0 lg:pl-0">
            {{-- Topbar --}}
            <header class="sticky top-0 z-30 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between h-14 px-4 sm:px-6">
                    <div class="flex items-center gap-3">
                        <button @@click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 lg:hidden">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <h1 class="text-lg font-bold text-gray-900">@yield('title', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600 hidden sm:block">{{ Auth::user()->name ?? 'User' }}</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 hidden sm:block">{{ Auth::user()->role ?? 'admin' }}</span>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
                <div id="toastContainer" class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none"></div>
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
                        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('success') }}</span>
                        <button @@click="show = false" class="ml-auto text-green-500 hover:text-green-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif
                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 flex items-center gap-2 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
                        <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('error') }}</span>
                        <button @@click="show = false" class="ml-auto text-red-500 hover:text-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    @vite('resources/js/app.js')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <div class="hidden bg-red-500 bg-amber-500 bg-green-600"></div>
    <script>
        window.escapeHtml = function(s) {
            if (s == null) return '';
            return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        };
        window.toast = function(msg, type = 'success') {
            const c = document.getElementById('toastContainer');
            if (!c) return alert(msg);
            const bg = type === 'error' ? '#ef4444' : type === 'warning' ? '#f59e0b' : '#16a34a';
            const icon = type === 'error' ? '✕' : type === 'warning' ? '⚠' : '✓';
            const el = document.createElement('div');
            el.className = 'toast-enter pointer-events-auto text-white text-sm px-4 py-3 rounded-xl shadow-xl flex items-center gap-2.5 max-w-sm border border-white/20';
            el.style.background = bg;
            el.style.color = '#fff';
            el.innerHTML = `<span class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold shrink-0">${icon}</span><span class="flex-1 leading-snug">${window.escapeHtml(msg)}</span><button onclick="this.parentElement.remove()" class="opacity-70 hover:opacity-100 ml-2 text-white text-lg leading-none">&times;</button>`;
            c.appendChild(el);
            setTimeout(() => { el.style.opacity='0'; el.style.transform='translateY(-8px)'; el.style.transition='all 0.3s'; setTimeout(()=>el.remove(),300); }, 3500);
        };
        window.showFieldErrors = function(errors) {
            clearFieldErrors();
            if (!errors) return;
            Object.keys(errors).forEach(k => {
                const el = document.getElementById(k + 'Error') || document.querySelector(`[data-field="${k}"]`);
                if (el) { el.textContent = errors[k][0]; el.classList.remove('hidden'); }
                const input = document.getElementById(k) || document.querySelector(`[name="${k}"]`);
                if (input) input.classList.add('input-error');
            });
        };
        window.clearFieldErrors = function() {
            document.querySelectorAll('.field-error').forEach(e => { e.textContent=''; e.classList.add('hidden'); });
            document.querySelectorAll('.input-error').forEach(e => e.classList.remove('input-error'));
        };
        window.setBtnLoading = function(btn, loading, text) {
            if (!btn) return;
            if (loading) { btn.dataset.origText = btn.innerHTML; btn.disabled = true; btn.innerHTML = `<svg class="animate-spin w-4 h-4 inline mr-1.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>${text || 'Menyimpan...'}`; }
            else { btn.disabled = false; btn.innerHTML = btn.dataset.origText || text || 'Simpan'; }
        };
        window.refreshPage = function(delay = 250) {
            setTimeout(function() {
                try {
                    const u = new URL(window.location.href);
                    u.searchParams.set('r', Date.now());
                    window.location.assign(u.toString());
                    return;
                } catch (e) {}
                window.location.reload();
            }, delay);
        };
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') document.querySelectorAll('.modal-overlay:not(.hidden)').forEach(m => m.classList.add('hidden'));
        });
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) e.target.classList.add('hidden');
        });
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js');
        }
    </script>
    @stack('scripts')
</body>
</html>
