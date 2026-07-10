<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2563EB">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" href="/images/logo-smk.png">
    <title>Masuk – Inventaris SMK</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    <style>
        * { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>
</head>
<body style="background:#F8FAFF;margin:0;min-height:100vh;display:flex;flex-direction:column">

    <nav style="background:#fff;border-bottom:1px solid #E2E8F0;padding:0 16px;height:52px;display:flex;align-items:center;gap:10px">
        <div style="display:flex;align-items:center;gap:8px;flex:1">
            <img src="{{ asset('images/logo-smk.png') }}" style="width:30px;height:30px;border-radius:50%;object-fit:contain;flex-shrink:0" alt="Logo SMK">
            <span style="font-weight:800;font-size:13px;color:#0F172A;white-space:nowrap">Inventaris SMK</span>
        </div>
    </nav>

    <div style="flex:1;display:flex;align-items:center;justify-content:center;padding:40px 20px">
        <div style="width:100%;max-width:400px">
            <div style="text-align:center;margin-bottom:30px">
                <img src="{{ asset('images/logo-smk.png') }}" style="width:100px;height:100px;border-radius:50%;object-fit:contain;margin:0 auto 16px;display:block;border:3px solid #E2E8F0;background:#fff;padding:4px" alt="Logo SMK">
                <div style="font-size:26px;font-weight:800;margin-bottom:7px;color:#0F172A">Masuk ke Inventaris</div>
                <div style="font-size:13px;color:#94A3B8">Platform inventaris SMK Labschool UNESA 1 Surabaya</div>
            </div>

            <div style="background:#fff;border:1px solid #E2E8F0;border-radius:18px;padding:28px 26px">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    @if($errors->any())
                        <div style="margin-bottom:16px;background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:10px 14px">
                            <ul style="margin:0;padding-left:16px;font-size:12px;color:#B91C1C">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div style="margin-bottom:13px">
                        <label style="display:block;font-size:11px;font-weight:700;color:#334155;margin-bottom:5px;text-transform:uppercase;letter-spacing:.03em">Email</label>
                        <input type="text" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com"
                            style="width:100%;padding:10px 13px;border:1.5px solid #E2E8F0;border-radius:10px;font-size:13px;color:#0F172A;background:#fff;outline:none;box-sizing:border-box">
                    </div>

                    <div style="margin-bottom:16px">
                        <label style="display:block;font-size:11px;font-weight:700;color:#334155;margin-bottom:5px;text-transform:uppercase;letter-spacing:.03em">Password</label>
                        <div style="position:relative">
                            <input type="password" name="password" id="loginPassword" required placeholder="••••••••"
                                style="width:100%;padding:10px 42px 10px 13px;border:1.5px solid #E2E8F0;border-radius:10px;font-size:13px;color:#0F172A;background:#fff;outline:none;box-sizing:border-box">
                            <button type="button" onclick="togglePass()"
                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:2px;color:#94A3B8;display:flex;align-items:center;justify-content:center;font-size:14px"
                                tabindex="-1">👁</button>
                        </div>
                    </div>

                    <button type="submit"
                        style="width:100%;background:#2563EB;color:#fff;border:none;border-radius:11px;padding:12px;font-size:14px;font-weight:700;cursor:pointer">
                        Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function togglePass() {
        var inp = document.getElementById('loginPassword');
        inp.type = inp.type === 'password' ? 'text' : 'password';
    }
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js');
    }
    </script>
</body>
</html>
