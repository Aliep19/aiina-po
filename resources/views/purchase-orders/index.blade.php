@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0">Purchase Order</h4>
                <p class="text-sm text-secondary mb-0">Daftar semua Purchase Order</p>
            </div>
            <a href="{{ route('purchase-orders.create') }}" class="btn btn-sm btn-danger">
                <i class="fas fa-plus me-1"></i> Buat PO Baru
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

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
                                   placeholder="no. PO atau nama supplier"
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Sort --}}
                    <div class="col-6 col-md-3">
                        <label class="form-label text-xs mb-1">Urutkan</label>
                        <select id="sortBySelect" class="form-select form-select-sm">
                            @foreach([
                                'po_date'      => 'Tanggal PO',
                                'po_number'    => 'No. PO',
                                'total_amount' => 'Total',
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
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No. PO</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Supplier</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tgl PO</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Delivery</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Total</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Dibuat Oleh</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="poTableBody">
                            @include('purchase-orders.partials.rows', ['purchaseOrders' => $purchaseOrders])
                        </tbody>
                    </table>

                    <div id="emptyState" class="text-center py-5 {{ $purchaseOrders->isEmpty() ? '' : 'd-none' }}">
                        <i class="fas fa-file-invoice fa-2x text-secondary mb-2"></i>
                        <p class="text-secondary text-sm">Tidak ada data Purchase Order.</p>
                    </div>
                </div>

                <div class="px-3" id="paginationWrapper">
                    <x-pagination :paginator="$purchaseOrders" />
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
const BASE_URL   = '{{ route("purchase-orders.index") }}';
const CSRF_TOKEN = '{{ csrf_token() }}';

let searchTimer  = null;
let currentPage  = {{ $purchaseOrders->currentPage() }};

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
        document.getElementById('poTableBody').innerHTML       = renderRows(json.data.data, json.data.from);
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
    return data.map((po, i) => `
        <tr>
            <td class="ps-4">
                <p class="text-xs font-weight-bold mb-0">${from + i}</p>
            </td>
            <td><p class="text-xs font-weight-bold mb-0">${esc(po.po_number)}</p></td>
            <td><p class="text-xs mb-0">${esc(po.supplier_name)}</p></td>
            <td><p class="text-xs mb-0">${esc(po.po_date ?? '—')}</p></td>
            <td><p class="text-xs mb-0">${esc(po.delivery_date ?? '—')}</p></td>
            <td><p class="text-xs mb-0">${fmtRp(po.total_amount)}</p></td>
            <td><p class="text-xs mb-0">${esc(po.creator_name)}</p></td>
            <td class="text-center">
                <a href="${BASE_URL}/${po.id}" class="btn btn-xs btn-outline-secondary me-1" title="Lihat">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="${BASE_URL}/${po.id}/edit" class="btn btn-xs btn-outline-info me-1" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
                <button type="button" class="btn btn-xs btn-outline-danger"
                        onclick="deletePO(${po.id}, '${esc(po.po_number)}')" title="Hapus">
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
   DELETE
──────────────────────────────── */
async function deletePO(id, poNumber) {
    const result = await Swal.fire({
        title: 'Hapus Purchase Order?',
        html:  `PO <strong>${esc(poNumber)}</strong> akan dihapus permanen.`,
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
        Swal.fire('Error', 'Gagal menghapus Purchase Order.', 'error');
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

document.addEventListener('DOMContentLoaded', () => bindPaginationClicks());
</script>
@endpush
