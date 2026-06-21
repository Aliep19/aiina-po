@extends('layouts.app')

@section('content')
    <style>
        .po-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 20px 24px;
        }

        .po-header h4 {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
        }

        .info-box {
            background: #fafafa;
            border: 1px solid #ececec;
            border-radius: 10px;
            padding: 18px;
            height: 100%;
        }

        .total-box {
            background: #f8fbff;
            border: 1px solid #dbeafe;
            border-radius: 10px;
            padding: 18px;
            height: 100%;
        }

        .section-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6c757d;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .total-box h3 {
            color: #0d6efd;
            font-weight: 700;
            margin: 0;
        }

        .custom-table {
            margin-bottom: 0;
        }

        .custom-table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
            padding: 14px;
            white-space: nowrap;
        }

        .custom-table td {
            padding: 14px;
            vertical-align: middle;
        }

        .custom-table tbody tr:hover {
            background: #fafafa;
        }

        .custom-table tfoot th {
            background: #f8f9fa;
            font-size: 15px;
            padding: 15px;
        }

        .notes-box {
            background: #fafafa;
            border-left: 4px solid #0d6efd;
            padding: 15px;
            border-radius: 6px;
        }

        .card-footer {
            background: #fff;
            border-top: 1px solid #ececec;
            padding: 16px 24px;
        }

        .btn {
            border-radius: 8px;
        }

        .fw-semibold {
            font-weight: 600;
        }
    </style>

    <div class="row">
        <div class="col-12">

            <div class="card">

                {{-- HEADER --}}
                <div class="card-header po-header d-flex justify-content-between align-items-center">

                    <div>
                        <h4>Purchase Order</h4>
                        <small class="text-muted">
                            No. {{ $purchaseOrder->po_number }}
                        </small>
                    </div>

                    <div>
                        <a href="{{ route('purchase-orders.preview', $purchaseOrder) }}"
                            class="btn btn-primary btn-sm" target="_blank">
                            <i class="fas fa-eye me-1"></i>
                            Preview
                        </a>

                        <a href="{{ route('purchase-orders.pdf', $purchaseOrder) }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf me-1"></i>
                            Download PDF
                        </a>
                    </div>

                </div>

                {{-- BODY --}}
                <div class="card-body">

                    <div class="row g-3 mb-4">

                        {{-- INFO --}}
                        <div class="col-md-8">
                            <div class="info-box">

                                <div class="section-title">
                                    Informasi Purchase Order
                                </div>

                                <div class="row">

                                    <div class="col-md-6 mb-3">
                                        <div class="text-muted small">
                                            Supplier
                                        </div>

                                        <div class="fw-semibold">
                                            {{ $purchaseOrder->supplier->name }}
                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <div class="text-muted small">
                                            Tanggal PO
                                        </div>

                                        <div class="fw-semibold">
                                            {{ $purchaseOrder->po_date?->format('d M Y') }}
                                        </div>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <div class="text-muted small">
                                            Delivery Date
                                        </div>

                                        <div class="fw-semibold">
                                            {{ $purchaseOrder->delivery_date?->format('d M Y') ?? '-' }}
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                        {{-- TOTAL --}}
                        <div class="col-md-4">

                            <div class="total-box text-end">

                                <div class="section-title">
                                    Total Amount
                                </div>

                                <h3>
                                    Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}
                                </h3>

                            </div>

                        </div>

                    </div>

                    {{-- TABLE --}}
                    <div class="table-responsive">

                        <table class="table custom-table">

                            <thead>
                                <tr class="text-center">
                                    <th>Produk</th>
                                    <th width="120">Qty</th>
                                    <th width="120">Unit</th>
                                    <th width="180">Harga</th>
                                    <th width="180">Subtotal</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($purchaseOrder->items as $item)
                                    <tr>

                                        <td>
                                            <div class="fw-semibold">
                                                {{ $item->product->name }}
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            {{ number_format($item->quantity, 0, ',', '.') }}
                                        </td>

                                        <td class="text-center">
                                            {{ $item->product->unit }}
                                        </td>

                                        <td class="text-end">
                                            Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                        </td>

                                        <td class="text-end fw-bold">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                            <tfoot>

                                <tr>

                                    <th colspan="4" class="text-end">
                                        Total Purchase Order
                                    </th>

                                    <th class="text-end text-primary fw-bold">
                                        Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}
                                    </th>

                                </tr>

                            </tfoot>

                        </table>

                    </div>

                    {{-- NOTES --}}
                    @if ($purchaseOrder->notes)
                        <div class="mt-4">

                            <div class="section-title">
                                Catatan
                            </div>

                            <div class="notes-box">
                                {{ $purchaseOrder->notes }}
                            </div>

                        </div>
                    @endif

                </div>

                {{-- FOOTER --}}
                <div class="card-footer d-flex justify-content-between">

                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Kembali
                    </a>

                    <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-warning text-dark">
                        <i class="fas fa-edit me-1"></i>
                        Edit Purchase Order
                    </a>

                </div>

            </div>

        </div>
    </div>
@endsection
