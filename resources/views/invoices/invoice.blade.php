<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Invoice {{ $order->order_number }}</title>
<style>
    @page {
        margin: 0;
        size: A4 portrait;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-size: 12px;
        color: #1a1a1a;
        background: #fff;
    }

    .invoice-inner {
        padding: 14mm 14mm 12mm 14mm;
    }

    /* ── Header ── */
    .header {
        width: 100%;
        margin-bottom: 28px;
        border-bottom: 3px solid {{ $primaryColor }};
        padding-bottom: 18px;
        overflow: hidden;
    }
    .header-logo {
        float: left;
        width: 48%;
    }
    .header-logo img {
        max-height: 52px;
        max-width: 170px;
    }
    .header-logo .site-name {
        font-size: 20px;
        font-weight: 700;
        letter-spacing: -0.5px;
        color: {{ $primaryColor }};
    }
    .header-invoice {
        float: right;
        width: 48%;
        text-align: right;
    }
    .header-invoice h1 {
        font-size: 26px;
        font-weight: 800;
        color: {{ $primaryColor }};
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    .header-invoice .order-number {
        font-size: 12px;
        color: #555;
        margin-top: 4px;
    }
    .header-invoice .order-date {
        font-size: 11px;
        color: #888;
        margin-top: 2px;
    }
    .clearfix::after { content: ''; display: block; clear: both; }

    /* ── Info Grid ── */
    .info-grid {
        width: 100%;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .info-col {
        float: left;
        width: 48%;
    }
    .info-col-right {
        float: right;
        width: 48%;
    }
    .info-col h3,
    .info-col-right h3 {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: {{ $primaryColor }};
        border-bottom: 1px solid {{ $primaryColor }}44;
        padding-bottom: 4px;
        margin-bottom: 7px;
    }
    .info-col p,
    .info-col-right p {
        font-size: 11px;
        color: #333;
        line-height: 1.65;
    }
    .info-col .label,
    .info-col-right .label {
        color: #888;
        font-size: 10px;
    }

    /* ── Badge ── */
    .badge {
        display: inline-block;
        padding: 2px 7px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 600;
        text-transform: capitalize;
    }
    .badge-paid    { background: #d1fae5; color: #065f46; }
    .badge-pending { background: #fef9c3; color: #713f12; }
    .badge-failed  { background: #fee2e2; color: #991b1b; }
    .badge-default { background: #e5e7eb; color: #374151; }

    /* ── Detail table inside info-col ── */
    .detail-table { width: 100%; border-collapse: collapse; }
    .detail-table td {
        font-size: 11px;
        color: #333;
        padding: 3px 0;
        vertical-align: top;
    }
    .detail-table td.dt-label { color: #888; width: 52%; }
    .detail-table td.dt-val   { text-align: right; width: 48%; }

    /* ── Section header ── */
    .section-title {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: {{ $primaryColor }};
        border-bottom: 1px solid {{ $primaryColor }}44;
        padding-bottom: 4px;
        margin-bottom: 0;
    }

    /* ── Items Table ── */
    table.items {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 6px;
    }
    table.items thead tr {
        background: {{ $primaryColor }};
    }
    table.items thead th {
        padding: 8px 10px;
        font-size: 10px;
        font-weight: 700;
        color: #fff;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    table.items thead th.right { text-align: right; }
    table.items tbody tr {
        border-bottom: 1px solid #f0f0f0;
    }
    table.items tbody tr:nth-child(even) { background: #fafafa; }
    table.items tbody td {
        padding: 9px 10px;
        font-size: 11px;
        color: #333;
        vertical-align: top;
    }
    table.items tbody td.right { text-align: right; }
    table.items tbody td .sku {
        font-size: 9px;
        color: #bbb;
        margin-top: 2px;
    }

    /* ── Totals (table-based, dompdf-safe) ── */
    .totals-table {
        width: 44%;
        margin-left: auto;
        margin-top: 6px;
        border-collapse: collapse;
        page-break-inside: avoid;
    }
    .totals-table td {
        font-size: 11px;
        padding: 5px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .totals-table td:first-child { color: #555; }
    .totals-table td:last-child { color: #222; text-align: right; }
    .totals-table .discount td { color: #16a34a; }
    .totals-table .grand td {
        border-top: 2px solid {{ $primaryColor }};
        border-bottom: none;
        padding-top: 8px;
        font-size: 14px;
        font-weight: 700;
        color: {{ $primaryColor }};
    }

    /* ── Notes ── */
    .notes-section {
        margin-top: 26px;
        padding: 12px 14px;
        background: #fafafa;
        border-left: 3px solid {{ $primaryColor }};
    }
    .notes-section h3 {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: {{ $primaryColor }};
        margin-bottom: 5px;
    }
    .notes-section p { font-size: 11px; color: #555; line-height: 1.6; }

    /* ── Footer ── */
    .footer {
        text-align: center;
        font-size: 9px;
        color: #bbb;
        border-top: 1px solid #e5e7eb;
        padding-top: 6px;
        margin-top: 18px;
    }
</style>
</head>
<body>
<div class="invoice-inner">

    {{-- ── HEADER ── --}}
    <div class="header clearfix">
        <div class="header-logo">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="{{ $siteName }}">
            @else
                <span class="site-name">{{ $siteName }}</span>
            @endif
        </div>
        <div class="header-invoice">
            <h1>Invoice</h1>
            <div class="order-number"># {{ $order->order_number }}</div>
            <div class="order-date">{{ $order->placed_at?->format('d F Y') }}</div>
        </div>
    </div>

    {{-- ── INFO GRID ── --}}
    <div class="info-grid clearfix">
        <div class="info-col">
            <h3>Kepada</h3>
            <p><strong>{{ $order->customer_name }}</strong></p>
            @if($order->customer_email)
                <p class="label">{{ $order->customer_email }}</p>
            @endif
            @if($order->customer_phone)
                <p class="label">{{ $order->customer_phone }}</p>
            @endif
            @if($order->shipping_address)
                <p style="margin-top:6px">{{ $order->shipping_address['line_1'] ?? '' }}</p>
                <p>{{ $order->shipping_address['city'] ?? '' }}{{ !empty($order->shipping_address['province']) ? ', ' . $order->shipping_address['province'] : '' }}</p>
                <p>{{ $order->shipping_address['postal_code'] ?? '' }}{{ !empty($order->shipping_address['country']) ? ', ' . $order->shipping_address['country'] : '' }}</p>
            @endif
        </div>

        <div class="info-col-right">
            <h3>Detail Pesanan</h3>
            <table class="detail-table">
                <tr>
                    <td class="dt-label">Status Pesanan</td>
                    <td class="dt-val">
                        <span class="badge badge-default">{{ $order->status }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="dt-label">Pembayaran</td>
                    <td class="dt-val">
                        @php
                            $badgeClass = match($order->payment_status) {
                                'paid'    => 'badge-paid',
                                'failed'  => 'badge-failed',
                                'pending' => 'badge-pending',
                                default   => 'badge-default',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $order->payment_status }}</span>
                    </td>
                </tr>
                @if($order->payment)
                <tr>
                    <td class="dt-label">Metode</td>
                    <td class="dt-val" style="text-transform:capitalize">{{ $order->payment->provider }}</td>
                </tr>
                @endif
                @if($order->paid_at)
                <tr>
                    <td class="dt-label">Tanggal Bayar</td>
                    <td class="dt-val">{{ $order->paid_at->format('d M Y') }}</td>
                </tr>
                @endif
                @if(!empty($order->shipping_address['tracking']))
                <tr>
                    <td class="dt-label">No. Resi</td>
                    <td class="dt-val" style="font-family:monospace;font-size:10px">{{ $order->shipping_address['tracking'] }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    {{-- ── ITEMS ── --}}
    <div class="section-title" style="margin-bottom:0">Item Pesanan</div>
    <table class="items">
        <thead>
            <tr>
                <th style="width:46%">Produk</th>
                <th class="right" style="width:9%">Qty</th>
                <th class="right" style="width:22%">Harga Satuan</th>
                <th class="right" style="width:23%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    {{ $item->product_name }}
                    @if($item->product_sku)
                        <div class="sku">SKU: {{ $item->product_sku }}</div>
                    @endif
                </td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── TOTALS ── --}}
    <table class="totals-table">
        <tr>
            <td>Subtotal</td>
            <td>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
        </tr>
        @if($order->discount_total > 0)
        <tr class="discount">
            <td>
                Diskon
                @if($order->voucher)
                    <span style="font-size:9px">({{ $order->voucher->code }})</span>
                @endif
            </td>
            <td>- Rp {{ number_format($order->discount_total, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($order->shipping_total > 0)
        <tr>
            <td>Ongkos Kirim</td>
            <td>Rp {{ number_format($order->shipping_total, 0, ',', '.') }}</td>
        </tr>
        @endif
        @if($order->tax_total > 0)
        <tr>
            <td>Pajak</td>
            <td>Rp {{ number_format($order->tax_total, 0, ',', '.') }}</td>
        </tr>
        @endif
        <tr class="grand">
            <td>Total</td>
            <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- ── NOTES ── --}}
    @if($order->notes)
    <div class="notes-section">
        <h3>Catatan</h3>
        <p>{{ $order->notes }}</p>
    </div>
    @endif

    {{-- ── FOOTER ── --}}
    <div class="footer">
        {{ $siteName }} &mdash; {{ $order->order_number }} &mdash; Digenerate {{ now()->format('d M Y, H:i') }}
    </div>

</div>
</body>
</html>
