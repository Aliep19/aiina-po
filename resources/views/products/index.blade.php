@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0">Product</h4>
                <p class="text-sm text-secondary mb-0">Kelola master data barang / item</p>
            </div>
            <button class="btn btn-sm btn-danger" onclick="openModal()">
                <i class="fas fa-plus me-1"></i> Tambah Produk
            </button>
        </div>

        {{-- Card --}}
        <div class="card">
            <div class="card-header pb-0">
                <div class="row g-2 align-items-end">

                    {{-- Search --}}
                    <div class="col-12 col-md-5">
                        <label class="form-label text-xs mb-1">Cari</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="searchInput" class="form-control form-control-sm"
                                   placeholder="Kode, nama atau satuan"
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Sort --}}
                    <div class="col-6 col-md-3">
                        <label class="form-label text-xs mb-1">Urutkan</label>
                        <select id="sortBySelect" class="form-select form-select-sm">
                            @foreach([
                                'name'          => 'Nama',
                                'unit'          => 'Satuan',
                                'default_price' => 'Harga',
                            ] as $val => $label)
                                <option value="{{ $val }}" {{ request('sort_by','id') == $val ? 'selected':'' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Order --}}
                    <div class="col-3 col-md-2">
                        <label class="form-label text-xs mb-1">Arah</label>
                        <select id="orderSelect" class="form-select form-select-sm">
                            <option value="asc"  {{ request('order','asc')=='asc'  ? 'selected':'' }}>A → Z</option>
                            <option value="desc" {{ request('order','asc')=='desc' ? 'selected':'' }}>Z → A</option>
                        </select>
                    </div>

                    {{-- Per page --}}
                    <div class="col-3 col-md-2">
                        <label class="form-label text-xs mb-1">Per hal.</label>
                        <select id="perPageSelect" class="form-select form-select-sm">
                            @foreach([10,25,50,100] as $n)
                                <option value="{{ $n }}" {{ request('per_page',10)==$n ? 'selected':'' }}>{{ $n }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            <div class="card-body px-0 pt-0 pb-2 mt-3">

                {{-- Loading --}}
                <div id="tableLoading" class="text-center py-4 d-none">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    <span class="ms-2 text-sm">Memuat data…</span>
                </div>

                <div id="tableWrapper" class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Kode</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Produk</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Satuan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Harga Default</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Supplier Utama</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Sup.</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="productTableBody">
                            @include('products.partials.rows', ['products' => $products])
                        </tbody>
                    </table>

                    <div id="emptyState" class="text-center py-5 {{ $products->isEmpty() ? '' : 'd-none' }}">
                        <i class="fas fa-boxes fa-2x text-secondary mb-2"></i>
                        <p class="text-secondary text-sm">Tidak ada data produk.</p>
                    </div>
                </div>

                <div class="px-3" id="paginationWrapper">
                    <x-pagination :paginator="$products" />
                </div>

            </div>
        </div>

    </div>
</div>

{{-- ════════════════════════════════════════════
     MODAL TAMBAH / EDIT
═════════════════════════════════════════════ --}}
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="productModalLabel">Tambah Produk</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                <div id="formErrors" class="alert alert-danger d-none">
                    <ul class="mb-0 ps-3" id="formErrorList"></ul>
                </div>

                <input type="hidden" id="productId">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-xs">Kode Produk <span class="text-danger">*</span></label>
                        <input type="text" id="productCode" class="form-control form-control-sm" placeholder="PRD-001">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label text-xs">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" id="productName" class="form-control form-control-sm" placeholder="Nama barang">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-xs">Satuan</label>
                        <input type="text" id="productUnit" class="form-control form-control-sm" placeholder="pcs / kg / box">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-xs">Harga Default</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="number" id="productPrice" class="form-control form-control-sm" placeholder="0" min="0" step="0.01">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-xs">Spesifikasi</label>
                        <textarea id="productSpec" class="form-control form-control-sm" rows="2" placeholder="Spesifikasi barang…"></textarea>
                    </div>
                </div>

                {{-- Supplier section --}}
                <hr class="my-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label text-xs mb-0 fw-bold">Supplier</label>
                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="addSupplierRow()">
                        <i class="fas fa-plus me-1"></i> Tambah Supplier
                    </button>
                </div>

                <div id="supplierRows">
                    {{-- Diisi dinamis --}}
                </div>
                <p class="text-xxs text-secondary mt-1">Centang <strong>Utama</strong> untuk supplier default produk ini.</p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm btn-danger" id="saveBtn" onclick="saveProduct()">
                    <span id="saveBtnText">Simpan</span>
                    <span id="saveBtnSpinner" class="spinner-border spinner-border-sm d-none ms-1"></span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Detail Produk</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <div class="text-center py-3">
                    <div class="spinner-border spinner-border-sm text-primary"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('dashboard')
<script>
/* ────────────────────────────────
   CONFIG
──────────────────────────────── */
const BASE_URL   = '{{ url("products") }}';
const CSRF_TOKEN = '{{ csrf_token() }}';
const ALL_SUPPLIERS = @json($suppliers);   // [{id, name}] — sudah di-cache dari controller

let searchTimer = null;
let currentPage = {{ $products->currentPage() }};
let supplierRowIdx = 0;

/* ────────────────────────────────
   FETCH TABLE
──────────────────────────────── */
function getParams(page = 1) {
    return new URLSearchParams({
        search:   document.getElementById('searchInput').value.trim(),
        sort_by:  document.getElementById('sortBySelect').value,
        order:    document.getElementById('orderSelect').value,
        per_page: document.getElementById('perPageSelect').value,
        page,
    });
}

function setLoading(state) {
    document.getElementById('tableLoading').classList.toggle('d-none', !state);
    document.getElementById('tableWrapper').classList.toggle('d-none', state);
}

async function fetchTable(page = 1) {
    setLoading(true);
    currentPage = page;
    try {
        const res  = await fetch(`${BASE_URL}?${getParams(page)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const json = await res.json();
        document.getElementById('productTableBody').innerHTML  = renderRows(json.data.data, json.data.from);
        document.getElementById('emptyState').classList.toggle('d-none', json.data.data.length > 0);
        document.getElementById('paginationWrapper').innerHTML = renderPagination(json.data);
        bindPaginationClicks();
        history.replaceState(null, '', `${location.pathname}?${getParams(page)}`);
    } catch (e) { console.error(e); }
    finally { setLoading(false); }
}

/* ────────────────────────────────
   RENDER ROWS
──────────────────────────────── */
function renderRows(data, from) {
    if (!data.length) return '';
    return data.map((p, i) => `
        <tr>
            <td class="ps-4">
                <p class="text-xs font-weight-bold mb-0">${from + i}</p>
            </td>
            <td><p class="text-xs font-weight-bold mb-0">${esc(p.product_code)}</p></td>
            <td><p class="text-xs font-weight-bold mb-0" style="max-width:200px">${esc(p.name)}</p></td>
            <td><p class="text-xs mb-0">${esc(p.unit ?? '—')}</p></td>
            <td><p class="text-xs mb-0">${fmtRp(p.default_price)}</p></td>
            <td>
                <p class="text-xs mb-0">${esc(p.primary_supplier_name ?? '—')}</p>
            </td>
            <td class="text-center">
                <span class="badge badge-sm bg-gradient-info">${p.suppliers_count}</span>
            </td>
            <td class="text-center">
                <button class="btn btn-xs btn-outline-secondary me-1"
                        onclick="showDetail(${p.id})" title="Detail">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-xs btn-outline-info me-1"
                        onclick="editProduct(${p.id})" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-xs btn-outline-danger"
                        onclick="deleteProduct(${p.id}, '${esc(p.name)}')" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`
    ).join('');
}

/* ────────────────────────────────
   PAGINATION
──────────────────────────────── */
function renderPagination(meta) {
    const { current_page: cur, last_page: last, from, to, total } = meta;
    if (!total) return '';
    const s = Math.max(1, cur - 2), e = Math.min(last, s + 4);
    const btn = (p, lbl, active, dis) =>
        `<li class="page-item ${active?'active':''} ${dis?'disabled':''}">
            <a class="page-link pagination-link" href="#" data-page="${p}">${lbl}</a></li>`;
    let b = btn(cur-1,'&laquo;',false,cur===1);
    if (s>1){b+=btn(1,'1'); if(s>2) b+=`<li class="page-item disabled"><span class="page-link">…</span></li>`;}
    for(let p=s;p<=e;p++) b+=btn(p,p,p===cur);
    if(e<last){if(e<last-1) b+=`<li class="page-item disabled"><span class="page-link">…</span></li>`;b+=btn(last,last);}
    b+=btn(cur+1,'&raquo;',false,cur===last);
    return `<div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-3">
        <small class="text-secondary">Menampilkan <strong>${from}</strong>–<strong>${to}</strong> dari <strong>${total}</strong> data</small>
        ${last>1?`<nav><ul class="pagination pagination-sm mb-0">${b}</ul></nav>`:''}
    </div>`;
}

function bindPaginationClicks() {
    document.querySelectorAll('.pagination-link').forEach(el =>
        el.addEventListener('click', e => { e.preventDefault(); const p=parseInt(el.dataset.page); if(p>=1) fetchTable(p); })
    );
}

/* ────────────────────────────────
   LIVE CONTROLS
──────────────────────────────── */
document.getElementById('searchInput').addEventListener('input', () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => fetchTable(1), 400);
});
['sortBySelect','orderSelect','perPageSelect'].forEach(id =>
    document.getElementById(id).addEventListener('change', () => fetchTable(1))
);

/* ────────────────────────────────
   MODAL TAMBAH
──────────────────────────────── */
function openModal(clear = true) {
    if (clear) {
        document.getElementById('productId').value    = '';
        document.getElementById('productCode').value  = '';
        document.getElementById('productName').value  = '';
        document.getElementById('productUnit').value  = '';
        document.getElementById('productPrice').value = '';
        document.getElementById('productSpec').value  = '';
        document.getElementById('supplierRows').innerHTML = '';
        supplierRowIdx = 0;
        document.getElementById('productModalLabel').textContent = 'Tambah Produk';
        document.getElementById('formErrors').classList.add('d-none');
    }
    new bootstrap.Modal(document.getElementById('productModal')).show();
}

/* ────────────────────────────────
   SUPPLIER ROWS (dynamic)
──────────────────────────────── */
function supplierOptions(selectedId = null) {
    return ALL_SUPPLIERS.map(s =>
        `<option value="${s.id}" ${s.id == selectedId ? 'selected' : ''}>${esc(s.name)}</option>`
    ).join('');
}

function addSupplierRow(supplierId = null, supplierPrice = 0, isPrimary = false) {
    const idx = supplierRowIdx++;
    const row = document.createElement('div');
    row.className = 'row g-2 align-items-center mb-2 supplier-row';
    row.dataset.idx = idx;
    row.innerHTML = `
        <div class="col-5">
            <select class="form-select form-select-sm sup-id">
                <option value="">-- Pilih Supplier --</option>
                ${supplierOptions(supplierId)}
            </select>
        </div>
        <div class="col-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text">Rp</span>
                <input type="number" class="form-control form-control-sm sup-price"
                       value="${supplierPrice}" min="0" step="0.01" placeholder="Harga supplier">
            </div>
        </div>
        <div class="col-2 text-center">
            <div class="form-check form-switch d-flex justify-content-center mb-0">
                <input class="form-check-input sup-primary" type="checkbox" ${isPrimary ? 'checked' : ''}
                       onchange="ensureSinglePrimary(this)">
                <label class="form-check-label text-xs ms-1">Utama</label>
            </div>
        </div>
        <div class="col-1 text-center">
            <button type="button" class="btn btn-xs btn-outline-danger" onclick="removeSupplierRow(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>`;
    document.getElementById('supplierRows').appendChild(row);
}

function removeSupplierRow(btn) {
    btn.closest('.supplier-row').remove();
}

function ensureSinglePrimary(checkbox) {
    if (!checkbox.checked) return;
    document.querySelectorAll('.sup-primary').forEach(cb => { if (cb !== checkbox) cb.checked = false; });
}

function collectSuppliers() {
    const rows = document.querySelectorAll('.supplier-row');
    const result = [];
    rows.forEach(row => {
        const id = row.querySelector('.sup-id').value;
        if (!id) return;
        result.push({
            id:             parseInt(id),
            supplier_price: parseFloat(row.querySelector('.sup-price').value) || 0,
            status_supplier:     row.querySelector('.sup-primary').checked,
        });
    });
    return result;
}

/* ────────────────────────────────
   EDIT
──────────────────────────────── */
async function editProduct(id) {
    try {
        const res  = await fetch(`${BASE_URL}/${id}`, { headers: { 'Accept': 'application/json' } });
        const json = await res.json();
        const p    = json.data;

        document.getElementById('productId').value    = p.id;
        document.getElementById('productCode').value  = p.product_code ?? '';
        document.getElementById('productName').value  = p.name ?? '';
        document.getElementById('productUnit').value  = p.unit ?? '';
        document.getElementById('productPrice').value = p.default_price ?? '';
        document.getElementById('productSpec').value  = p.specification ?? '';
        document.getElementById('supplierRows').innerHTML = '';
        supplierRowIdx = 0;

        (p.suppliers ?? []).forEach(s =>
            addSupplierRow(s.id, s.pivot?.supplier_price ?? 0, s.pivot?.status_supplier ?? false)
        );

        document.getElementById('productModalLabel').textContent = 'Edit Produk';
        document.getElementById('formErrors').classList.add('d-none');
        openModal(false);
    } catch (e) {
        Swal.fire('Error', 'Gagal memuat data produk.', 'error');
    }
}

/* ────────────────────────────────
   DETAIL
──────────────────────────────── */
async function showDetail(id) {
    new bootstrap.Modal(document.getElementById('detailModal')).show();
    document.getElementById('detailContent').innerHTML =
        `<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></div>`;
    try {
        const res  = await fetch(`${BASE_URL}/${id}`, { headers: { 'Accept': 'application/json' } });
        const json = await res.json();
        const p    = json.data;
        const supRows = (p.suppliers ?? []).map(s => `
            <tr>
                <td class="text-xs">${esc(s.name)}</td>
                <td class="text-xs">${fmtRp(s.pivot?.supplier_price)}</td>
                <td class="text-xs text-center">
                    ${s.pivot?.status_supplier ? '<span class="badge bg-success">Utama</span>' : '—'}
                </td>
            </tr>`).join('') || `<tr><td colspan="3" class="text-center text-xs text-secondary">Belum ada supplier</td></tr>`;

        document.getElementById('detailContent').innerHTML = `
            <div class="row g-2 mb-2">
                <div class="col-4"><small class="text-secondary">Kode</small><p class="text-xs font-weight-bold mb-0">${esc(p.product_code)}</p></div>
                <div class="col-8"><small class="text-secondary">Nama</small><p class="text-xs font-weight-bold mb-0">${esc(p.name)}</p></div>
                <div class="col-4"><small class="text-secondary">Satuan</small><p class="text-xs mb-0">${esc(p.unit??'—')}</p></div>
                <div class="col-8"><small class="text-secondary">Harga Default</small><p class="text-xs mb-0">${fmtRp(p.default_price)}</p></div>
                <div class="col-12"><small class="text-secondary">Spesifikasi</small><p class="text-xs mb-0" style="white-space:pre-wrap">${esc(p.specification??'—')}</p></div>
            </div>
            <hr class="my-2">
            <p class="text-xs fw-bold mb-2">Supplier</p>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr>
                        <th class="text-xxs text-secondary">Nama</th>
                        <th class="text-xxs text-secondary">Harga</th>
                        <th class="text-xxs text-secondary text-center">Status</th>
                    </tr></thead>
                    <tbody>${supRows}</tbody>
                </table>
            </div>`;
    } catch (e) {
        document.getElementById('detailContent').innerHTML = `<p class="text-danger text-sm">Gagal memuat detail.</p>`;
    }
}

/* ────────────────────────────────
   SAVE
──────────────────────────────── */
async function saveProduct() {
    const id     = document.getElementById('productId').value;
    const isEdit = !!id;

    const payload = {
        product_code:  document.getElementById('productCode').value.trim(),
        name:          document.getElementById('productName').value.trim(),
        specification: document.getElementById('productSpec').value.trim(),
        unit:          document.getElementById('productUnit').value.trim(),
        default_price: parseFloat(document.getElementById('productPrice').value) || 0,
        suppliers:     collectSuppliers(),
    };

    if (!payload.product_code || !payload.name) {
        showFormErrors(['Kode produk dan nama wajib diisi.']);
        return;
    }

    setBtnLoading(true);
    try {
        const res  = await fetch(isEdit ? `${BASE_URL}/${id}` : BASE_URL, {
            method:  isEdit ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
            body:    JSON.stringify(payload),
        });
        const json = await res.json();

        if (!res.ok) {
            showFormErrors(json.errors ? Object.values(json.errors).flat() : [json.message ?? 'Terjadi kesalahan.']);
            return;
        }

        bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
        Swal.fire({ icon: 'success', title: isEdit ? 'Berhasil diperbarui!' : 'Berhasil ditambahkan!', timer: 1500, showConfirmButton: false });
        fetchTable(isEdit ? currentPage : 1);
    } catch (e) {
        Swal.fire('Error', 'Koneksi gagal.', 'error');
    } finally {
        setBtnLoading(false);
    }
}

/* ────────────────────────────────
   DELETE
──────────────────────────────── */
async function deleteProduct(id, name) {
    const result = await Swal.fire({
        title: 'Hapus Produk?',
        html:  `Produk <strong>${esc(name)}</strong> akan dihapus permanen.`,
        icon:  'warning', showCancelButton: true,
        confirmButtonText: 'Ya, hapus', cancelButtonText: 'Batal', confirmButtonColor: '#d33',
    });
    if (!result.isConfirmed) return;

    try {
        const res = await fetch(`${BASE_URL}/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        });
        if (!res.ok) throw new Error();
        Swal.fire({ icon: 'success', title: 'Dihapus!', timer: 1200, showConfirmButton: false });
        fetchTable(currentPage);
    } catch (e) {
        Swal.fire('Error', 'Gagal menghapus produk.', 'error');
    }
}

/* ────────────────────────────────
   HELPERS
──────────────────────────────── */
function esc(str) {
    return String(str ?? '')
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function fmtRp(val) {
    return 'Rp ' + (parseFloat(val) || 0).toLocaleString('id-ID', { minimumFractionDigits: 0 });
}

function showFormErrors(errors) {
    document.getElementById('formErrorList').innerHTML = errors.map(e => `<li>${e}</li>`).join('');
    document.getElementById('formErrors').classList.remove('d-none');
}

function setBtnLoading(state) {
    document.getElementById('saveBtn').disabled        = state;
    document.getElementById('saveBtnText').textContent = state ? 'Menyimpan…' : 'Simpan';
    document.getElementById('saveBtnSpinner').classList.toggle('d-none', !state);
}

document.addEventListener('DOMContentLoaded', () => bindPaginationClicks());
</script>
@endpush
