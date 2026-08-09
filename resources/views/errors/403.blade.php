<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>
        * { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        body { background: #F8FAFF; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
    <div class="text-center max-w-md">
        <div class="w-20 h-20 mx-auto bg-red-100 rounded-2xl flex items-center justify-center mb-6">
            <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
        </div>
        <h1 class="text-6xl font-extrabold text-gray-800">403</h1>
        <p class="mt-2 text-lg font-semibold text-gray-700">Akses Ditolak</p>
        <p class="mt-1 text-sm text-gray-500">{{ isset($exception) && $exception->getMessage() && $exception->getMessage() !== '403' ? $exception->getMessage() : 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}</p>
        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url('/') }}" class="btn-primary text-sm px-5 py-2.5">Kembali ke Dashboard</a>
            <a href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn-secondary text-sm px-5 py-2.5">Ganti Akun</a>
            <form id="logout-form" method="POST" action="{{ url('/logout') }}" class="hidden">@csrf</form>
        </div>
    </div>
</body>
</html>