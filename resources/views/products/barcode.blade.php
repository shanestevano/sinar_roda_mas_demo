<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Barcode Ban - {{ $tire->sku }}</title>
    <style>
        @page {
            size: 80mm 50mm;
            margin: 0;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 20px;
        }
        .label-card {
            width: 78mm;
            height: 46mm;
            background: #ffffff;
            border: 1px dashed #64748b;
            border-radius: 6px;
            padding: 8px 10px;
            box-sizing: border-box;
            margin: 0 auto;
            display: flex;
            flex-col;
            justify-content: space-between;
        }
        .store-header {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1.5px solid #000;
            padding-bottom: 2px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .tire-title {
            font-size: 13px;
            font-weight: 900;
            margin-top: 4px;
        }
        .tire-size {
            font-size: 15px;
            font-weight: 900;
            color: #000;
            font-family: 'Courier New', Courier, monospace;
        }
        .badge {
            font-size: 10px;
            font-weight: bold;
            padding: 1px 4px;
            border: 1px solid #000;
            border-radius: 3px;
        }
        .barcode-container {
            text-align: center;
            margin: 4px 0;
        }
        .barcode-container svg {
            max-width: 100%;
            height: 40px;
        }
        .footer-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-top: 1px solid #cbd5e1;
            padding-top: 3px;
        }
        .price {
            font-size: 14px;
            font-weight: 900;
        }
        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn {
            background: #0284c7;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 13px;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .label-card {
                border: none;
                margin: 0;
                width: 100%;
                height: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn" onclick="window.print()">🖨️ Cetak Stiker Barcode Ban</button>
        <button class="btn" style="background:#64748b; margin-left:8px;" onclick="window.close()">Tutup</button>
    </div>

    <div class="label-card">
        <div>
            <div class="store-header">
                <span>SINAR RODA MAS</span>
                <span>RAK: {{ $tire->rackLocation ? $tire->rackLocation->code : 'BUFFER' }}</span>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-top: 3px;">
                <div class="tire-title">{{ $tire->brand }} {{ $tire->pattern }}</div>
                @if($tire->dot_code)
                    <span class="badge">DOT {{ $tire->dot_code }}</span>
                @endif
            </div>

            <div class="tire-size">{{ $tire->size_display }}</div>
        </div>

        <!-- Barcode SVG -->
        <div class="barcode-container">
            {!! $barcodeSvg !!}
        </div>

        <!-- Footer -->
        <div class="footer-info">
            <span style="font-size: 9px; font-mono: monospace;">{{ $tire->sku }}</span>
            <span class="price">{{ $tire->formatted_selling_price }}</span>
        </div>
    </div>

</body>
</html>
