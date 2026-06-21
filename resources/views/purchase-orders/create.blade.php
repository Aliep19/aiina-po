@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0">Buat Purchase Order Baru</h4>
                <p class="text-sm text-secondary mb-0">Isi data supplier lalu tambahkan item produk yang dipesan</p>
            </div>
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <p class="mb-1 text-sm fw-bold">Terjadi kesalahan pada input:</p>
                <ul class="mb-0 ps-3 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header pb-0">
                <h6 class="mb-0">Informasi PO</h6>
                <p class="text-sm text-secondary mb-0">Data umum purchase order</p>
            </div>

            <div class="card-body">
                <form id="po-form" action="{{ route('purchase-orders.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-xs">Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" id="supplier_id"
                                    class="form-select form-select-sm @error('supplier_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Supplier --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-xs">Tanggal PO <span class="text-danger">*</span></label>
                            <input type="date" name="po_date"
                                   class="form-control form-control-sm @error('po_date') is-invalid @enderror"
                                   value="{{ old('po_date', now()->format('Y-m-d')) }}" required>
                            @error('po_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label text-xs">Tanggal Delivery</label>
                            <input type="date" name="delivery_date"
                                   class="form-control form-control-sm @error('delivery_date') is-invalid @enderror"
                                   value="{{ old('delivery_date') }}">
                            @error('delivery_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label text-xs">Catatan</label>
                            <textarea name="notes" class="form-control form-control-sm" rows="2"
                                      placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <hr class="my-4">

                    {{-- Item Produk --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">Item Produk</h6>
                            <p class="text-xs text-secondary mb-0">Pilih supplier dahulu agar daftar produk muncul</p>
                        </div>
                        <button type="button" id="add-item" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-plus me-1"></i> Tambah Item
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-items-center mb-0" id="items-table">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Produk</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" width="12%">Qty</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" width="18%">Harga Unit</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" width="18%">Subtotal</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody id="items-body"></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end text-sm fw-bold">Grand Total</td>
                                    <td class="text-sm fw-bold" id="grand-total">Rp 0</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>

                        <div id="items-empty" class="text-center py-4 d-none">
                            <i class="fas fa-box-open fa-lg text-secondary mb-2"></i>
                            <p class="text-secondary text-sm mb-0">Belum ada item. Klik "Tambah Item" untuk menambahkan produk.</p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('purchase-orders.index') }}" class="btn btn-sm btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-sm btn-primary" id="save-btn">
                            <span id="save-btn-text">Simpan</span>
                            <span id="save-btn-spinner" class="spinner-border spinner-border-sm d-none ms-1"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let itemIndex = 0;

    const tbody           = document.getElementById('items-body');
    const itemsTable       = document.getElementById('items-table');
    const emptyState       = document.getElementById('items-empty');
    const supplierSelect   = document.getElementById('supplier_id');
    const supplierProducts = @json($supplierProducts);
    const oldItems         = @json(old('items', []));

    function fmtRp(val) {
        return (parseFloat(val) || 0).toLocaleString('id-ID', { minimumFractionDigits: 0 });
    }

    function calculateSubtotal(row) {
        const qty   = parseFloat(row.querySelector('.quantity').value) || 0;
        const price = parseFloat(row.querySelector('.unit-price').value) || 0;
        row.querySelector('.subtotal').value = fmtRp(qty * price);
        updateGrandTotal();
    }

    function updateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty   = parseFloat(row.querySelector('.quantity').value) || 0;
            const price = parseFloat(row.querySelector('.unit-price').value) || 0;
            total += qty * price;
        });
        document.getElementById('grand-total').textContent = 'Rp ' + fmtRp(total);
    }

    function toggleEmptyState() {
        const hasRows = tbody.querySelectorAll('.item-row').length > 0;
        itemsTable.classList.toggle('d-none', !hasRows);
        emptyState.classList.toggle('d-none', hasRows);
    }

    function populateProductOptions(selectElement, selectedId = null) {
        const supplierId = supplierSelect.value;
        selectElement.innerHTML = '<option value="">-- Pilih Produk --</option>';

        if (supplierId && supplierProducts[supplierId]) {
            Object.keys(supplierProducts[supplierId]).forEach(productId => {
                const data   = supplierProducts[supplierId][productId];
                const option = document.createElement('option');
                option.value = productId;
                option.textContent = data.name;
                if (selectedId && String(selectedId) === String(productId)) {
                    option.selected = true;
                }
                selectElement.appendChild(option);
            });
        }
    }

    function addNewRow(prefill = null) {
        const row = document.createElement('tr');
        row.className = 'item-row';
        row.innerHTML = `
            <td>
                <select name="items[${itemIndex}][product_id]" class="form-select form-select-sm product-select" required>
                    <option value="">-- Pilih Produk --</option>
                </select>
            </td>
            <td>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control form-control-sm quantity text-center"
                       step="1" min="1" placeholder="0" value="${prefill?.quantity ?? ''}" required>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="items[${itemIndex}][unit_price]" class="form-control form-control-sm unit-price"
                           step="0.01" min="0" value="${prefill?.unit_price ?? ''}" readonly>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="text" class="form-control form-control-sm subtotal" readonly>
                </div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-xs btn-outline-danger remove-item" title="Hapus item">
                    <i class="fas fa-trash"></i>
                </button>
            </td>`;

        tbody.appendChild(row);
        itemIndex++;

        const productSelect  = row.querySelector('.product-select');
        const unitPriceInput = row.querySelector('.unit-price');
        const qtyInput        = row.querySelector('.quantity');

        populateProductOptions(productSelect, prefill?.product_id);

        productSelect.addEventListener('change', function () {
            const productId = this.value;
            const supplierId = supplierSelect.value;

            if (productId && supplierId && supplierProducts[supplierId] && supplierProducts[supplierId][productId]) {
                unitPriceInput.value = supplierProducts[supplierId][productId].price;
            } else {
                unitPriceInput.value = '';
            }
            calculateSubtotal(row);
        });

        qtyInput.addEventListener('input', () => calculateSubtotal(row));

        if (prefill) calculateSubtotal(row);
        toggleEmptyState();
    }

    // Ganti Supplier → reset & refresh semua dropdown produk
    supplierSelect.addEventListener('change', function () {
        document.querySelectorAll('.product-select').forEach(select => {
            populateProductOptions(select);
            const row = select.closest('tr');
            row.querySelector('.unit-price').value = '';
            row.querySelector('.subtotal').value   = '';
        });
        updateGrandTotal();
    });

    document.getElementById('add-item').addEventListener('click', () => addNewRow());

    tbody.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-item');
        if (btn) {
            btn.closest('tr').remove();
            toggleEmptyState();
            updateGrandTotal();
        }
    });

    // Restore item lama kalau validasi gagal sebelumnya, atau mulai dengan 1 baris kosong
    if (oldItems.length) {
        oldItems.forEach(item => addNewRow(item));
    } else {
        addNewRow();
    }

    // Validasi minimal 1 item sebelum submit + loading state tombol
    document.getElementById('po-form').addEventListener('submit', function (e) {
        const rows = tbody.querySelectorAll('.item-row');
        if (rows.length === 0) {
            e.preventDefault();
            Swal.fire('Perhatian', 'Tambahkan minimal 1 item produk.', 'warning');
            return;
        }

        const saveBtn = document.getElementById('save-btn');
        saveBtn.disabled = true;
        document.getElementById('save-btn-spinner').classList.remove('d-none');
    });
});
</script>
@endsection
