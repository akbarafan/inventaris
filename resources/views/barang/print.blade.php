<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label - Inventaris SMK</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Libre+Barcode+39&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: #fff;
            padding: 10px;
        }
        .label-card {
            width: 100mm;
            min-height: 80mm;
            border: 1.5px solid #0f172a;
            border-radius: 6px;
            padding: 14px 16px;
            margin: 0 auto 8px;
            page-break-after: always;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .header {
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .header img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: contain;
            flex-shrink: 0;
        }
        .header-text {
            flex: 1;
        }
        .header-text .school {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        .header-text .title {
            font-size: 12px;
            font-weight: 700;
            color: #2563eb;
            line-height: 1.2;
            letter-spacing: 0.05em;
        }
        .asset-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 6px;
            padding: 4px 0;
        }
        .asset-id-area {
            flex: 1;
            min-width: 0;
        }
        .asset-id-label {
            font-size: 9px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        .asset-id-value {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            font-family: monospace;
            letter-spacing: 0.02em;
        }
        .barcode-box {
            flex-shrink: 0;
            text-align: center;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 4px 8px;
            background: #fff;
        }
        .barcode {
            font-family: 'Libre Barcode 39', cursive;
            font-size: 38px;
            line-height: 1;
            color: #0f172a;
            margin-bottom: -2px;
        }
        .barcode-type {
            font-size: 7px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .details-section {
            display: flex;
            gap: 12px;
            flex: 1;
            border-top: 1.5px solid #0f172a;
            padding-top: 8px;
            margin-top: auto;
        }
        .metadata {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
            justify-content: center;
        }
        .metadata .row {
            display: flex;
            font-size: 12px;
            line-height: 1.3;
        }
        .metadata .row .lbl {
            font-weight: 700;
            color: #0f172a;
            width: 110px;
            flex-shrink: 0;
        }
        .metadata .row .dots {
            color: #0f172a;
            width: 12px;
            flex-shrink: 0;
            text-align: center;
        }
        .metadata .row .val {
            font-weight: 600;
            color: #0f172a;
            word-break: break-all;
            flex: 1;
        }
        .status-dot {
            display: inline-block;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            margin-right: 2px;
            vertical-align: middle;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .status-dot.green { background: #22c55e; }
        .status-dot.orange { background: #f59e0b; }
        .status-dot.red { background: #ef4444; }
        .qr-wrap {
            flex-shrink: 0;
            text-align: center;
            width: 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .qr-wrap svg {
            width: 72px;
            height: 72px;
            display: block;
        }
        .qr-text {
            font-size: 8px;
            font-weight: 700;
            color: #2563eb;
            letter-spacing: 0.05em;
            margin-top: 2px;
        }
        @media print {
            body { padding: 0; }
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .label-card {
                border: 1.5px solid #0f172a;
                margin: 0 auto;
                page-break-after: always;
                box-shadow: none;
            }
            .label-card:last-child { page-break-after: avoid; }
        }
        @page {
            size: 100mm 80mm;
            margin: 0;
        }
    </style>
</head>
<body>
    @foreach($barangs as $barang)
    <div class="label-card">
        <div class="header">
            <img src="{{ asset('images/logo-smk.png') }}" alt="Logo">
            <div class="header-text">
                <div class="school">SMK Labschool UNESA 1 Surabaya</div>
                <div class="title">INVENTARIS SMK</div>
            </div>
        </div>

        <div class="asset-section">
            <div class="asset-id-area">
                <div class="asset-id-label">ID ASET</div>
                <div class="asset-id-value">{{ $barang->kode_barang }}</div>
            </div>
            <div class="barcode-box">
                <div class="barcode">*{{ $barang->kode_barang }}*</div>
                <div class="barcode-type">Code 39</div>
            </div>
        </div>

        <div class="details-section">
            <div class="metadata">
                <div class="row">
                    <span class="lbl">NAMA</span>
                    <span class="dots">:</span>
                    <span class="val">{{ strtoupper($barang->nama_barang) }}</span>
                </div>
                <div class="row">
                    <span class="lbl">LOKASI</span>
                    <span class="dots">:</span>
                    <span class="val">{{ strtoupper($barang->barangLokasis->first()->lokasi->nama_lokasi ?? $barang->lokasi->nama_lokasi ?? '-') }}</span>
                </div>
                <div class="row">
                    <span class="lbl">TGL PEROLEHAN</span>
                    <span class="dots">:</span>
                    <span class="val">{{ $barang->tanggal_masuk ? strtoupper(\Carbon\Carbon::parse($barang->tanggal_masuk)->locale('id')->isoFormat('D MMM Y')) : '-' }}</span>
                </div>
                <div class="row">
                    <span class="lbl">KONDISI</span>
                    <span class="dots">:</span>
                    <span class="val">
                        @if($barang->baik > 0)
                            <span class="status-dot green"></span> BAIK
                        @elseif($barang->rusak > 0)
                            <span class="status-dot orange"></span> PERBAIKAN
                        @else
                            <span class="status-dot red"></span> RUSAK BERAT
                        @endif
                    </span>
                </div>
            </div>
            <div class="qr-wrap">
                {!! $barang->generateQrSvg(64, true) !!}
                <div class="qr-text">SCAN QR</div>
            </div>
        </div>
    </div>
    @endforeach
    <script>
        window.onload = function() { window.print(); };
    </script>
</body>
</html>
