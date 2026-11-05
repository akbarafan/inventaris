<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label - Inventaris</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Libre+Barcode+39&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: #eef2f7;
            padding: 24px 12px;
            color: #0f172a;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .label-card {
            width: 100mm;
            height: 80mm;
            background: #fff;
            border: 1px solid #0f172a;
            border-radius: 4mm;
            padding: 4mm;
            margin: 0 auto 10px;
            display: flex;
            flex-direction: column;
            page-break-after: always;
            break-after: page;
            overflow: hidden;
            position: relative;
        }

        /* ---------- Header ---------- */
        .label-head {
            display: flex;
            align-items: center;
            gap: 2.5mm;
            padding-bottom: 2mm;
            border-bottom: 0.8mm solid #0f172a;
        }
        .label-head img {
            width: 8.5mm;
            height: 8.5mm;
            border-radius: 50%;
            object-fit: contain;
            flex-shrink: 0;
        }
        .head-text {
            flex: 1;
            min-width: 0;
        }
        .head-text .school {
            font-size: 8.2pt;
            font-weight: 800;
            line-height: 1.15;
            text-transform: uppercase;
            letter-spacing: 0.01em;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .head-text .title {
            font-size: 6.4pt;
            font-weight: 700;
            color: #2563eb;
            line-height: 1.2;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .head-badge {
            flex-shrink: 0;
            font-size: 5.6pt;
            font-weight: 800;
            letter-spacing: 0.1em;
            color: #fff;
            background: #0f172a;
            border-radius: 1mm;
            padding: 1mm 1.8mm;
            text-transform: uppercase;
        }

        /* ---------- ID aset ---------- */
        .id-block {
            padding: 1.6mm 0 0.8mm;
            border-bottom: 0.4mm dashed #cbd5e1;
        }
        .id-block .caption {
            font-size: 5.8pt;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
        .id-value {
            font-family: 'Courier New', ui-monospace, monospace;
            font-size: 15pt;
            font-weight: 700;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
        }

        /* ---------- Barcode ---------- */
        .barcode-wrap {
            height: 13mm;
            margin-top: 1.2mm;
            padding: 0.6mm 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .barcode {
            font-family: 'Libre Barcode 39', 'Libre Barcode 39 Extended', cursive;
            font-size: 34pt;
            line-height: 1;
            white-space: nowrap;
            color: #0f172a;
        }
        .barcode-caption {
            text-align: center;
            font-size: 5.2pt;
            font-weight: 700;
            letter-spacing: 0.18em;
            color: #94a3b8;
            text-transform: uppercase;
            margin-top: -0.5mm;
        }

        /* ---------- Body ---------- */
        .label-body {
            flex: 1;
            display: flex;
            gap: 3mm;
            margin-top: 2mm;
            min-height: 0;
            overflow: hidden;
        }
        .meta {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 0.6mm;
            justify-content: center;
            overflow: hidden;
        }
        .meta .row {
            display: flex;
            align-items: baseline;
            font-size: 6.8pt;
            line-height: 1.2;
        }
        .meta .lbl {
            width: 22mm;
            flex-shrink: 0;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            font-size: 5.8pt;
            letter-spacing: 0.03em;
        }
        .meta .sep {
            width: 2.5mm;
            flex-shrink: 0;
            color: #94a3b8;
        }
        .meta .val {
            flex: 1;
            min-width: 0;
            font-weight: 700;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            word-break: break-word;
        }

        .qr-panel {
            width: 25mm;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1.2mm;
        }
        .qr-panel .qr-box {
            width: 18mm;
            height: 18mm;
            flex-shrink: 0;
            padding: 1.2mm;
            background: #fff;
            border: 0.25mm solid #e2e8f0;
            border-radius: 1.5mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qr-panel .qr-box svg {
            width: 100% !important;
            height: 100% !important;
            display: block;
        }
        .qr-panel .qr-caption {
            flex-shrink: 0;
            font-size: 5.2pt;
            font-weight: 800;
            letter-spacing: 0.12em;
            color: #2563eb;
            text-transform: uppercase;
            line-height: 1;
        }

        /* ---------- Kondisi ---------- */
        .dot {
            display: inline-block;
            width: 1.8mm;
            height: 1.8mm;
            border-radius: 50%;
            margin-right: 1.2mm;
            vertical-align: middle;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .dot.green { background: #22c55e; }
        .dot.orange { background: #f59e0b; }
        .dot.red { background: #ef4444; }

        /* ---------- Footer ---------- */
        .label-foot {
            flex-shrink: 0;
            position: relative;
            z-index: 1;
            background: #fff;
            margin-top: 2mm;
            padding-top: 1.5mm;
            border-top: 0.3mm solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            gap: 2mm;
            font-size: 5pt;
            font-weight: 600;
            color: #94a3b8;
            letter-spacing: 0.02em;
        }
        .label-foot .right { white-space: nowrap; }

        @media print {
            body { padding: 0; background: #fff; }
            .print-toolbar { display: none !important; }
            .label-card {
                margin: 0;
                border-radius: 4mm;
                box-shadow: none;
            }
            .label-card:last-child {
                page-break-after: auto;
                break-after: auto;
            }
        }

        /* ---------- Screen toolbar ---------- */
        .print-toolbar {
            position: fixed;
            right: 18px;
            bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            background: #0f172a;
            color: #fff;
            padding: 8px 10px 8px 14px;
            border-radius: 999px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.25);
            font-size: 12px;
            font-weight: 600;
            z-index: 50;
        }
        .print-toolbar button {
            border: 0;
            cursor: pointer;
            background: #2563eb;
            color: #fff;
            font-family: inherit;
            font-size: 12px;
            font-weight: 700;
            padding: 7px 14px;
            border-radius: 999px;
        }
        .print-toolbar button:hover { background: #1d4ed8; }

        @page {
            size: 100mm 80mm;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="print-toolbar">
        <span>{{ $labels->count() }} label &middot; 100 &times; 80 mm</span>
        <button type="button" onclick="window.print()">Cetak</button>
    </div>

    @foreach($labels as $label)
    @php
        $kondisi = $label['baik'] > 0 ? 'baik' : ($label['rusak'] > 0 ? 'rusak' : 'rusak_berat');
    @endphp
    <div class="label-card">
        <div class="label-head">
            @php $brandLogo = $settings['logo'] ?? ''; @endphp
            <img src="{{ $brandLogo ? asset('storage/' . $brandLogo) : asset('images/logo-smk.png') }}" alt="Logo">
            <div class="head-text">
                <div class="school">{{ strtoupper($settings['nama_sekolah'] ?? 'SMK Labschool UNESA 1 Surabaya') }}</div>
                <div class="title">Inventaris {{ strtoupper($settings['nama_singkat'] ?? 'SMK') }}</div>
            </div>
            <div class="head-badge">{{ $label['is_unit'] ? 'Unit Aset' : 'Aset' }}</div>
        </div>

        <div class="id-block">
            <div class="caption">ID Aset</div>
            <div class="id-value" data-fit>{{ $label['kode'] }}</div>
        </div>

        <div class="barcode-wrap">
            <span class="barcode" data-fit-barcode>*{{ $label['kode'] }}*</span>
        </div>
        <div class="barcode-caption">Code 39</div>

        <div class="label-body">
            <div class="meta">
                <div class="row">
                    <span class="lbl">Nama</span>
                    <span class="sep">:</span>
                    <span class="val">{{ strtoupper($label['nama']) }}</span>
                </div>
                <div class="row">
                    <span class="lbl">Jumlah</span>
                    <span class="sep">:</span>
                    <span class="val">{{ $label['jumlah'] }} {{ $label['satuan'] ?? 'pcs' }}</span>
                </div>
                <div class="row">
                    <span class="lbl">Lokasi</span>
                    <span class="sep">:</span>
                    <span class="val">{{ strtoupper($label['lokasi']) }}</span>
                </div>
                <div class="row">
                    <span class="lbl">Perolehan</span>
                    <span class="sep">:</span>
                    <span class="val">{{ $label['tanggal_masuk'] ? strtoupper(\Carbon\Carbon::parse($label['tanggal_masuk'])->locale('id')->isoFormat('D MMM Y')) : '-' }}</span>
                </div>
                <div class="row">
                    <span class="lbl">Kondisi</span>
                    <span class="sep">:</span>
                    <span class="val">
                        @if($kondisi === 'baik')
                            <span class="dot green"></span>BAIK
                        @elseif($kondisi === 'rusak')
                            <span class="dot orange"></span>PERBAIKAN
                        @else
                            <span class="dot red"></span>RUSAK BERAT
                        @endif
                    </span>
                </div>
            </div>

            <div class="qr-panel">
                <div class="qr-box">{!! $label['qr_svg'] !!}</div>
                <div class="qr-caption">Scan QR</div>
            </div>
        </div>

        <div class="label-foot">
            <span>Aset milik sekolah &middot; dilarang memindahtangankan tanpa izin</span>
            <span class="right">{{ strtoupper($settings['nama_singkat'] ?? 'SMK') }}</span>
        </div>
    </div>
    @endforeach

    <script>
        function fitText(el, maxPx, minPx, step) {
            if (!el) return;
            var size = maxPx;
            el.style.fontSize = size + 'px';
            var guard = 0;
            while (el.scrollWidth > el.clientWidth && size > minPx && guard < 80) {
                size -= step;
                el.style.fontSize = size + 'px';
                guard++;
            }
        }

        function fitBarcode(el) {
            if (!el) return;
            var box = el.parentElement;
            var size = 60;
            el.style.fontSize = size + 'px';
            var guard = 0;
            while ((el.scrollWidth > box.clientWidth || el.scrollHeight > box.clientHeight) && size > 14 && guard < 120) {
                size -= 1;
                el.style.fontSize = size + 'px';
                guard++;
            }
        }

        function fitAll() {
            document.querySelectorAll('[data-fit]').forEach(function (el) {
                fitText(el, 28, 10, 0.5);
            });
            document.querySelectorAll('[data-fit-barcode]').forEach(fitBarcode);
        }

        function ready() {
            fitAll();
            window.print();
        }

        window.addEventListener('load', function () {
            if (document.fonts && document.fonts.ready) {
                document.fonts.ready.then(ready).catch(ready);
            } else {
                ready();
            }
        });
    </script>
</body>
</html>
