<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>
        * { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        body { background: #F8FAFF; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
    <div class="text-center max-w-md">
        <div class="w-20 h-20 mx-auto bg-blue-100 rounded-2xl flex items-center justify-center mb-6">
            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <h1 class="text-6xl font-extrabold text-gray-800">404</h1>
        <p class="mt-2 text-lg font-semibold text-gray-700">Halaman Tidak Ditemukan</p>
        <p class="mt-1 text-sm text-gray-500">Maaf, halaman atau data yang Anda cari tidak tersedia.</p>
        <a href="{{ url('/') }}" class="btn-primary text-sm px-5 py-2.5 inline-flex mt-8">Kembali ke Dashboard</a>
    </div>
</body>
</html>