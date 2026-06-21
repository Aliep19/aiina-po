<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order - {{ $purchaseOrder->po_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        @page {
            margin: 90px 50px 70px 50px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #1f1f1f;
            /* Fallback padding in case the renderer ignores @page margin */
            padding: 28px 34px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        td {
            vertical-align: top;
        }

        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }

        /* ===== TOP BAR: logo / title / page ===== */
        .top-bar-table {
            margin-bottom: 22px;
        }
        .top-bar-table td {
            vertical-align: middle;
        }
        .logo-box {
            border: 1px solid #999;
            border-radius: 4px;
            padding: 7px 16px;
            width: 86px;
        }
        .logo-text {
            font-size: 21px;
            font-weight: bold;
            font-style: italic;
        }
        .logo-text .c1 { color: #cc0000; }
        .logo-text .c2 { color: #0066cc; }

        .po-title-center {
            text-align: center;
            font-size: 21px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #16263f;
        }

        .page-box {
            border: 1px solid #999;
            border-radius: 4px;
            padding: 5px 12px;
            font-size: 10.5px;
            text-align: center;
            white-space: nowrap;
            width: 76px;
            color: #555;
        }

        /* ===== MAIN INFO TABLE (left supplier block / right company block) ===== */
        .main-info-table {
            margin-bottom: 22px;
        }
        .main-info-table > tbody > tr > td {
            vertical-align: top;
            padding: 0;
        }
        .left-col {
            width: 54%;
            padding-right: 26px;
        }
        .right-col {
            width: 46%;
        }

        /* left col inner table: label / colon / value */
        .confirm-table td {
            padding: 3px 0;
            font-size: 11.5px;
            line-height: 1.4;
        }
        .confirm-table .lbl {
            width: 72px;
            white-space: nowrap;
            color: #444;
        }
        .confirm-table .col {
            width: 14px;
            color: #444;
        }



        /* right col: company info */
        .company-name-right {
            font-weight: bold;
            font-size: 13.5px;
            text-align: right;
            margin-bottom: 6px;
            color: #16263f;
        }
        .company-details-right {
            font-size: 10px;
            color: #555;
            text-align: right;
            line-height: 1.65;
        }

        /* PO meta table */
        .po-meta-table {
            margin-top: 14px;
            border: 1px solid #d7dce5;
            border-radius: 3px;
        }
        .po-meta-table td {
            border: 1px solid #d7dce5;
            padding: 7px 10px;
            font-size: 11px;
        }
        .po-meta-table tr.shaded td {
            background-color: #eef1f6;
        }
        .po-meta-table .meta-lbl {
            font-weight: bold;
            width: 96px;
            white-space: nowrap;
            color: #2c3a4f;
        }
        .po-meta-table .meta-col {
            width: 14px;
            color: #777;
        }

        /* ===== ITEMS TABLE ===== */
        .items-table {
            margin: 22px 0 24px 0;
        }
        .items-table th {
            background-color: #335a8f;
            color: #ffffff;
            border: 1px solid #335a8f;
            padding: 9px 6px;
            font-size: 11px;
            letter-spacing: 0.3px;
            text-align: center;
        }
        .items-table td {
            border: 1px solid #e2e5ea;
            padding: 8px 7px;
            font-size: 11px;
        }
        .items-table tbody tr:nth-child(even) {
            background-color: #f8f9fb;
        }

        /* ===== REMARKS + TOTALS BOTTOM ===== */
        .bottom-table {
            margin-top: 26px;
        }
        .bottom-table > tbody > tr > td {
            vertical-align: top;
        }
        .remarks-col {
            width: 56%;
            padding-right: 26px;
        }
        .remarks {
            border-top: 2px solid #5d7eb0;
            padding-top: 12px;
        }
        .remarks-title {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 11.5px;
            color: #16263f;
        }
        .remarks ol {
            margin-left: 18px;
            font-size: 10.5px;
            line-height: 1.7;
            color: #333;
        }

        .totals-col {
            width: 44%;
        }
        .totals-table {
            border: 1px solid #e2e5ea;
            border-radius: 3px;
        }
        .totals-table td {
            padding: 9px 12px;
            font-size: 11.5px;
            border-bottom: 1px solid #e2e5ea;
        }
        .totals-table tr.grand-total td {
            font-weight: bold;
            font-size: 13px;
            border-top: 2px solid #16263f;
            border-bottom: none;
            background-color: #f8f9fb;
        }

        /* ===== APPROVAL AREA ===== */
        .approval-table {
            margin-top: 56px;
        }
        .approval-box {
            width: 230px;
            text-align: center;
        }
        .approval-company-name {
            font-weight: bold;
            font-size: 11.5px;
            margin-bottom: 10px;
            color: #16263f;
        }
        .approval-logo-text {
            font-size: 17px;
            font-weight: bold;
            font-style: italic;
        }
        .approval-signer-name {
            font-weight: bold;
            font-size: 12px;
            margin-top: 6px;
            padding-top: 6px;
        }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 36px;
            padding-top: 14px;
            border-top: 1px solid #e2e5ea;
            font-size: 9px;
            color: #999;
            text-align: center;
            line-height: 1.6;
        }
    </style>
</head>
<body>

    <!-- ===== TOP BAR: Logo / Title / Page ===== -->
    <table class="top-bar-table">
        <tr>
            <td style="width: 110px;">
                <div>
                    <img src="{{ public_path('assets/img/aiina.png') }}" alt="AIINA Logo" height="100">
                </div>
            </td>
            <td class="po-title-center">PURCHASE ORDER</td>
        </tr>
    </table>

    <!-- ===== CONFIRM TO / SUPPLIER / ADDRESS  +  COMPANY INFO ===== -->
    <table class="main-info-table">
        <tr>
            <!-- LEFT: Confirm to / Supplier details -->
            <td class="left-col">
                <table class="confirm-table">
                    <tr>
                        <td class="lbl">Confirm to.</td>
                        <td class="col"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="lbl">Supplier</td>
                        <td class="col">:</td>
                        <td class="bold">{{ $purchaseOrder->supplier->name }}</td>
                    </tr>
                    <tr>
                        <td class="lbl" style="vertical-align: top;">Address</td>
                        <td class="col" style="vertical-align: top;">:</td>
                        <td>
                        {{ $purchaseOrder->supplier->address }}
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Attention</td>
                        <td class="col">:</td>
                        <td>{{ $purchaseOrder->supplier->pic_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">E-mail</td>
                        <td class="col">:</td>
                        <td>{{ $purchaseOrder->supplier->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Mobile</td>
                        <td class="col">:</td>
                        <td>{{ $purchaseOrder->supplier->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Phone</td>
                        <td class="col">:</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td class="lbl">Fax</td>
                        <td class="col">:</td>
                        <td>-</td>
                    </tr>
                </table>
            </td>

            <!-- RIGHT: Company info + PO meta -->
            <td class="right-col">
                <div class="company-name-right">PT. ALPHA INNOVATECH INDONESIA</div>
                <div class="company-details-right">
                    Kebakelan RT 002, RW C14 No 128 Ds. Kebakelan,<br>
                    Kec. Babekan, Kab. Bekasi, Prov. Jawa Barat,<br>
                    Indonesia 17610<br>
                    Tel: +62 857 7014 5284, +62 812 1944 6694<br>
                    www.aiinaindonesia.com<br>
                    info@aiinaindonesia.com
                </div>

                <table class="po-meta-table">
                    <tr class="shaded">
                        <td class="meta-lbl">PO No</td>
                        <td class="meta-col">:</td>
                        <td>{{ $purchaseOrder->po_number }}</td>
                    </tr>
                    <tr>
                        <td class="meta-lbl">PO Date</td>
                        <td class="meta-col">:</td>
                        <td>{{ $purchaseOrder->po_date?->format('d-M-y') }}</td>
                    </tr>
                    <tr class="shaded">
                        <td class="meta-lbl">Delivery Date</td>
                        <td class="meta-col">:</td>
                        <td>{{ $purchaseOrder->delivery_date?->format('d-M-y') ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- ===== ITEMS TABLE ===== -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 34%;">Description / Item</th>
                <th style="width: 12%;">Qty</th>
                <th style="width: 12%;">Unit</th>
                <th style="width: 18%;">Price (IDR)</th>
                <th style="width: 18%;">Amount (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-left">{{ $item->product->name }}</td>
                <td class="text-center">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                <td class="text-center">{{ $item->product->unit ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">Rp {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- ===== REMARKS + TOTALS ===== -->
    <table class="bottom-table">
        <tr>
            <td class="remarks-col">
                <div class="remarks">
                    <div class="remarks-title">Remark :</div>
                    @if($purchaseOrder->notes)
                        <div style="font-size: 10.5px; white-space: pre-line; line-height: 1.7;">{{ $purchaseOrder->notes }}</div>
                    @else
                        <ol>
                            <li>Delivery on {{ $purchaseOrder->delivery_date?->format('d F Y') ?? 'TBD' }}</li>
                            <li>Location delivery AIINA warehouse</li>
                            <li>JL. Lkr. Gg. H. Madun No 71, Cibuntu, Kec. Cibitung, Kabupaten Bekasi, Jawa Barat 17520</li>
                        </ol>
                    @endif
                </div>
            </td>
            <td class="totals-col">
                <table class="totals-table">
                    <tr>
                        <td>SUBTOTAL</td>
                        <td class="text-right">Rp {{ number_format($purchaseOrder->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>DISCOUNT</td>
                        <td class="text-right">Rp 0.00</td>
                    </tr>
                    <tr class="grand-total">
                        <td>TOTAL</td>
                        <td class="text-right">Rp {{ number_format($purchaseOrder->total_amount, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- ===== APPROVAL / SIGNATURE AREA ===== -->
    <table class="approval-table">
        <tr>
            <td class="text-right">
                <table class="approval-box" style="margin-left: auto;">
                    <tr>
                        <td class="approval-company-name">PT. ALPHA INNOVATECH INDONESIA</td>
                    </tr>
                    <tr>
                        <td>
                            <img src="{{ public_path('assets/img/aiina.png') }}" alt="AIINA Logo" height="50"><br>
                        </td>
                    </tr>
                    <tr>
                        <td class="approval-signer-name">Mukhafid</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
