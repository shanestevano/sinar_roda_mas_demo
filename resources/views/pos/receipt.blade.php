<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Penjualan - {{ $transaction->invoice_number }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            background: #f1f5f9;
            color: #000;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.3;
        }
        .receipt-container {
            max-width: 80mm;
            margin: 0 auto;
            background: #fff;
            padding: 15px 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 4px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #475569;
            margin: 8px 0;
        }
        .double-divider {
            border-top: 2px dashed #000;
            margin: 8px 0;
        }
        .flex-between {
            display: flex;
            justify-content: space-between;
        }
        .item-row {
            margin-bottom: 4px;
        }
        .no-print {
            text-align: center;
            margin-bottom: 15px;
        }
        .print-btn {
            background: #0284c7;
            color: #fff;
            border: none;
            padding: 8px 16px;
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
            .receipt-container {
                box-shadow: none;
                padding: 4px 6px;
                max-width: 100%;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="print-btn" onclick="window.print()">🖨️ Cetak Struk (Print)</button>
        <button class="print-btn" style="background:#475569; margin-left:8px;" onclick="window.close()">Tutup</button>
    </div>

    <div class="receipt-container" id="printable-receipt">
        <!-- Store Header -->
        <div class="text-center">
            <div class="font-bold" style="font-size: 15px;">{{ $storeName }}</div>
            <div style="font-size: 11px;">{{ $storeTagline }}</div>
            <div style="font-size: 10px; margin-top: 2px;">{{ $storeAddress }}</div>
            <div style="font-size: 10px;">Telp: {{ $storePhone }}</div>
        </div>

        <div class="double-divider"></div>

        <!-- Transaction Details -->
        <div>
            <div class="flex-between">
                <span>No Faktur :</span>
                <span class="font-bold">{{ $transaction->invoice_number }}</span>
            </div>
            <div class="flex-between">
                <span>Waktu     :</span>
                <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex-between">
                <span>Kasir     :</span>
                <span>{{ $transaction->user ? $transaction->user->name : '-' }}</span>
            </div>
            <div class="flex-between">
                <span>Pelanggan :</span>
                <span>{{ $transaction->customer_name }}</span>
            </div>
            @if($transaction->vehicle_plate)
            <div class="flex-between">
                <span>No Polisi :</span>
                <span class="font-bold">{{ $transaction->vehicle_plate }}</span>
            </div>
            @endif
            @if($transaction->isVoided())
            <div class="flex-between" style="color:red; font-weight:bold; margin-top:4px;">
                <span>STATUS   :</span>
                <span>*** DIBATALKAN (VOID) ***</span>
            </div>
            <div style="font-size:10px; color:red;">
                Alasan: {{ $transaction->void_reason }}
            </div>
            @endif
        </div>

        <div class="divider"></div>

        <!-- Line Items -->
        <div>
            @foreach($transaction->items as $item)
                <div class="item-row">
                    <div class="font-bold">
                        {{ $item->tire ? $item->tire->brand . ' ' . $item->tire->pattern : 'Ban' }}
                    </div>
                    <div style="font-size: 10px; color: #334155;">
                        {{ $item->tire ? $item->tire->size_display : '' }}
                        @if($item->tire && $item->tire->dot_code)
                            (DOT: {{ $item->tire->dot_code }})
                        @endif
                    </div>
                    <div class="flex-between" style="font-size: 11px;">
                        <span>{{ $item->quantity }} x {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                        <span class="font-bold">{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="divider"></div>

        <!-- Totals & Payment -->
        <div>
            <div class="flex-between">
                <span>Subtotal :</span>
                <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($transaction->discount_amount > 0)
            <div class="flex-between">
                <span>Diskon   :</span>
                <span>-Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex-between font-bold" style="font-size: 13px; margin: 4px 0;">
                <span>TOTAL    :</span>
                <span>Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</span>
            </div>
            <div class="flex-between">
                <span>Metode   :</span>
                <span class="font-bold" style="text-transform: uppercase;">{{ $transaction->payment_method }}</span>
            </div>
            <div class="flex-between">
                <span>Dibayar  :</span>
                <span>Rp {{ number_format($transaction->amount_paid, 0, ',', '.') }}</span>
            </div>
            <div class="flex-between font-bold">
                <span>Kembali  :</span>
                <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="double-divider"></div>

        <!-- Footer Notice -->
        <div class="text-center" style="font-size: 10px; white-space: pre-line;">
            {{ $receiptFooter }}
        </div>
    </div>

</body>
</html>
