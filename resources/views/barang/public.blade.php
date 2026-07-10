<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#2563EB">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" href="/images/logo-smk.png">
    <title>{{ $barang->nama_barang }} - Inventaris SMK</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #F8FAFF; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #fff; border: 1px solid #E2E8F0; border-radius: 18px; padding: 32px; max-width: 420px; width: 100%; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; }
        .badge-baik { background: #dcfce7; color: #166534; }
        .badge-rusak { background: #fef9c3; color: #854d0e; }
        .badge-rusakberat { background: #fee2e2; color: #991b1b; }
        .label { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .03em; margin-bottom: 2px; }
        .value { font-size: 14px; color: #0f172a; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <div style="text-align:center;margin-bottom:24px;">
            <img src="{{ asset('images/logo-smk.png') }}" style="width:48px;height:48px;border-radius:50%;object-fit:contain;margin:0 auto 8px;border:2px solid #E2E8F0;padding:2px;background:#fff;" alt="Logo">
            <h1 style="font-size:18px;font-weight:800;color:#0f172a;">Inventaris SMK</h1>
            <p style="font-size:12px;color:#94a3b8;">Sistem Informasi Inventaris</p>
        </div>

        <div style="text-align:center;margin-bottom:20px;">
            {!! $barang->generateQrSvg(120, true) !!}
            <p style="font-size:11px;color:#94a3b8;margin-top:6px;font-family:monospace;">{{ $barang->kode_barang }}</p>
        </div>

        <div style="border-top:1px solid #E2E8F0;padding-top:20px;">
            <h2 style="font-size:16px;font-weight:700;color:#0f172a;margin-bottom:16px;">{{ $barang->nama_barang }}</h2>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <div class="label">Kode</div>
                    <div class="value" style="font-family:monospace;">{{ $barang->kode_barang }}</div>
                </div>
                <div>
                    <div class="label">Kategori</div>
                    <div class="value">{{ $barang->kategori->nama_kategori ?? '-' }}</div>
                </div>
                <div>
                    <div class="label">Jumlah</div>
                    <div class="value">{{ $barang->jumlah }}</div>
                </div>
                <div>
                    <div class="label">Sumber</div>
                    <div class="value">{{ $barang->sumber ?? '-' }}</div>
                </div>
            </div>

            <div style="margin-top:16px;">
                <div class="label">Lokasi</div>
                <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:4px;">
                    @forelse($barang->barangLokasis as $bl)
                        <span style="background:#f1f5f9;padding:3px 10px;border-radius:6px;font-size:12px;color:#334155;">{{ $bl->lokasi->nama_lokasi ?? '-' }}</span>
                    @empty
                        <span style="font-size:12px;color:#94a3b8;">-</span>
                    @endforelse
                </div>
            </div>

            <div style="margin-top:16px;">
                <div class="label">Kondisi</div>
                <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:4px;">
                    <span class="badge badge-baik">Baik: {{ $barang->baik }}</span>
                    <span class="badge badge-rusak">Rusak: {{ $barang->rusak }}</span>
                    <span class="badge badge-rusakberat">Rusak Berat: {{ $barang->rusak_berat }}</span>
                </div>
            </div>
        </div>
    </div>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js');
        }
    </script>
</body>
</html>