<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Inventaris SMK</title>
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
    </style>
</head>
<body class="h-full font-sans antialiased text-gray-900">
    <div x-data="{ sidebarOpen: false }" class="flex h-full overflow-hidden">

        {{-- Sidebar --}}
        <div x-show="sidebarOpen" @@click="sidebarOpen = false" class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"></div>
        <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-[220px] bg-white border-r border-gray-200 flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:z-auto overflow-y-auto">
            <div class="flex items-center gap-2 h-14 px-4 border-b border-gray-200">
                <img src="{{ asset('storage/media/logo-smk.png') }}" alt="Logo SMK" class="w-7 h-7 object-contain rounded-full shrink-0">
                <span class="text-gray-900 font-bold text-sm truncate">Inventaris SMK</span>
            </div>
            <nav class="flex-1 px-2 py-3 space-y-0.5 overflow-y-auto">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('/') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ url('/barang') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('barang*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Data Barang
                </a>
                <a href="{{ url('/kategori') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('kategori*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    Kategori
                </a>
                <a href="{{ url('/lokasi') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('lokasi*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Lokasi
                </a>
                <a href="{{ url('/scan') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('scan*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    Scan QR
                </a>
                <a href="{{ url('/laporan') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->is('laporan*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Laporan
                </a>
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
    @stack('scripts')
</body>
</html>
