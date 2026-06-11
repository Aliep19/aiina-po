@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">Customer</h4>
                    <p class="text-sm text-secondary mb-0">Kelola data customer</p>
                </div>
                <button class="btn btn-sm btn-danger" onclick="openModal()">
                    <i class="fas fa-plus me-1"></i> Tambah Customer
                </button>
            </div>

            {{-- Card Tabel --}}
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row g-2 align-items-end">

                        {{-- Search --}}
                        <div class="col-12 col-md-5">
                            <label class="form-label text-xs mb-1">Cari</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" id="searchInput" class="form-control form-control-sm"
                                    placeholder="Cari id, nama, alamat, telp, email" value="{{ request('search') }}">
                            </div>
                        </div>

                        {{-- Sort by --}}
                        <div class="col-6 col-md-3">
                            <label class="form-label text-xs mb-1">Urutkan</label>
                            <select id="sortBySelect" class="form-select form-select-sm">
                                @foreach ([
            'name' => 'Nama',
            'phone' => 'Telepon',
            'email' => 'Email',
        ] as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ request('sort_by', 'id') == $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Order --}}
                        <div class="col-6 col-md-2">
                            <label class="form-label text-xs mb-1">Arah</label>
                            <select id="orderSelect" class="form-select form-select-sm">
                                <option value="asc" {{ request('order', 'asc') == 'asc' ? 'selected' : '' }}>A → Z
                                </option>
                                <option value="desc" {{ request('order', 'asc') == 'desc' ? 'selected' : '' }}>Z → A
                                </option>
                            </select>
                        </div>

                        {{-- Per page --}}
                        <div class="col-6 col-md-2">
                            <label class="form-label text-xs mb-1">Per halaman</label>
                            <select id="perPageSelect" class="form-select form-select-sm">
                                @foreach ([10, 25, 50, 100] as $n)
                                    <option value="{{ $n }}"
                                        {{ request('per_page', 10) == $n ? 'selected' : '' }}>
                                        {{ $n }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>

                <div class="card-body px-0 pt-0 pb-2 mt-3">

                    {{-- Loading overlay --}}
                    <div id="tableLoading" class="text-center py-4 d-none">
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        <span class="ms-2 text-sm">Memuat data…</span>
                    </div>

                    <div id="tableWrapper" class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Alamat</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Telepon</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Email</th>
                                    <th
                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="customerTableBody">
                                @include('customers.partials.rows', ['customers' => $customers])
                            </tbody>
                        </table>

                        {{-- Empty state --}}
                        <div id="emptyState" class="text-center py-5 {{ $customers->isEmpty() ? '' : 'd-none' }}">
                            <i class="fas fa-box-open fa-2x text-secondary mb-2"></i>
                            <p class="text-secondary text-sm">Tidak ada data customer.</p>
                        </div>
                    </div>

                    {{-- Pagination --}}
                    <div class="px-3" id="paginationWrapper">
                        <x-pagination :paginator="$customers" />
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- modal tambah/edit customer --}}
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="customerModalLabel">Tambah Customer</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="formErrors" class="alert alert-danger d-none">
                        <ul class="mb-0 ps-3" id="formErrorList"></ul>
                    </div>

                    <input type="hidden" id="customerId">

                    <div class="mb-3">
                        <label class="form-label text-xs">Nama <span class="text-danger">*</span></label>
                        <input type="text" id="customerName" class="form-control form-control-sm"
                            placeholder="Nama customer">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-xs">Alamat</label>
                        <textarea id="customerAddress" class="form-control form-control-sm" rows="2" placeholder="Alamat customer"></textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label text-xs">Telepon</label>
                            <input type="text" id="customerPhone" class="form-control form-control-sm"
                                placeholder="08xx">
                        </div>
                        <div class="col-6">
                            <label class="form-label text-xs">Email</label>
                            <input type="email" id="customerEmail" class="form-control form-control-sm"
                                placeholder="email@contoh.com">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-sm btn-danger" id="saveBtn" onclick="saveCustomer()">
                        <span id="saveBtnText">Simpan</span>
                        <span id="saveBtnSpinner" class="spinner-border spinner-border-sm d-none ms-1"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('dashboard')
    <script>
        /* ──────────────────────────────────────────────
               CONFIG
            ────────────────────────────────────────────── */
        const BASE_URL = '{{ url('customers') }}';
        const CSRF_TOKEN = '{{ csrf_token() }}';

        let searchTimer = null;
        let currentPage = {{ $customers->currentPage() }};

        /* ──────────────────────────────────────────────
           HELPERS
        ────────────────────────────────────────────── */
        function getParams(page = 1) {
            return new URLSearchParams({
                search: document.getElementById('searchInput').value.trim(),
                sort_by: document.getElementById('sortBySelect').value,
                order: document.getElementById('orderSelect').value,
                per_page: document.getElementById('perPageSelect').value,
                page,
            });
        }

        function setLoading(state) {
            document.getElementById('tableLoading').classList.toggle('d-none', !state);
            document.getElementById('tableWrapper').classList.toggle('d-none', state);
        }

        /* ──────────────────────────────────────────────
           FETCH TABLE
        ────────────────────────────────────────────── */
        async function fetchTable(page = 1) {
            setLoading(true);
            currentPage = page;

            try {
                const res = await fetch(`${BASE_URL}?${getParams(page)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const json = await res.json();

                // Rows
                document.getElementById('customerTableBody').innerHTML = renderRows(json.data.data, json.data.from);

                // Empty state
                const empty = json.data.data.length === 0;
                document.getElementById('emptyState').classList.toggle('d-none', !empty);

                // Pagination
                document.getElementById('paginationWrapper').innerHTML = renderPagination(json.data);

                // Re-bind pagination clicks
                bindPaginationClicks();

                // Sync URL tanpa reload
                history.replaceState(null, '', `${window.location.pathname}?${getParams(page)}`);
            } catch (e) {
                console.error(e);
            } finally {
                setLoading(false);
            }
        }

        /* ──────────────────────────────────────────────
           RENDER ROWS (JS)
        ────────────────────────────────────────────── */
        function renderRows(data, from) {
            if (!data.length) return '';
            return data.map((s, i) => `
        <tr>
            <td class="ps-4">
                <p class="text-xs font-weight-bold mb-0">${from + i}</p>
            <td>
                <p class="text-xs font-weight-bold mb-0">${esc(s.name)}</p>
            </td>
            <td>
                <p class="text-xs text-secondary mb-0" style="max-width:180px;white-space:normal">
                    ${esc(s.address ?? '—')}
                </p>
            </td>
            <td><p class="text-xs mb-0">${esc(s.phone ?? '—')}</p></td>
            <td><p class="text-xs mb-0">${esc(s.email ?? '—')}</p></td>
            <td class="text-center">
                <button class="btn btn-xs btn-outline-info me-1"
                        onclick="editCustomer(${s.id})" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-xs btn-outline-danger"
                        onclick="deleteCustomer(${s.id}, '${esc(s.name)}')" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`).join('');
        }

        function esc(str) {
            return String(str)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        /* ──────────────────────────────────────────────
           RENDER PAGINATION (JS)
        ────────────────────────────────────────────── */
        function renderPagination(meta) {
            const {
                current_page: cur,
                last_page: last,
                from,
                to,
                total
            } = meta;
            if (!total) return '';

            const start = Math.max(1, cur - 2);
            const end = Math.min(last, start + 4);

            let dots = (p) => `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            let pageBtn = (p, label, active = false, disabled = false) => `
        <li class="page-item ${active ? 'active' : ''} ${disabled ? 'disabled' : ''}">
            <a class="page-link pagination-link" href="#" data-page="${p}">${label}</a>
        </li>`;

            let btns = '';
            btns += pageBtn(cur - 1, '&laquo;', false, cur === 1);
            if (start > 1) {
                btns += pageBtn(1, '1');
                if (start > 2) btns += dots();
            }
            for (let p = start; p <= end; p++) btns += pageBtn(p, p, p === cur);
            if (end < last) {
                if (end < last - 1) btns += dots();
                btns += pageBtn(last, last);
            }
            btns += pageBtn(cur + 1, '&raquo;', false, cur === last);

            return `
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-3">
            <small class="text-secondary">
                Menampilkan <strong>${from}</strong>–<strong>${to}</strong>
                dari <strong>${total}</strong> data
            </small>
            ${last > 1 ? `<nav><ul class="pagination pagination-sm mb-0">${btns}</ul></nav>` : ''}
        </div>`;
        }

        function bindPaginationClicks() {
            document.querySelectorAll('.pagination-link').forEach(el => {
                el.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = parseInt(this.dataset.page);
                    if (page >= 1) fetchTable(page);
                });
            });
        }

        /* ──────────────────────────────────────────────
           LIVE SEARCH & CONTROLS
        ────────────────────────────────────────────── */
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => fetchTable(1), 400);
        });

        ['sortBySelect', 'orderSelect', 'perPageSelect'].forEach(id => {
            document.getElementById(id).addEventListener('change', () => fetchTable(1));
        });

        /* ──────────────────────────────────────────────
           MODAL — TAMBAH
        ────────────────────────────────────────────── */
        function openModal(clear = true) {
            if (clear) {
                document.getElementById('customerId').value = '';
                document.getElementById('customerName').value = '';
                document.getElementById('customerAddress').value = '';
                document.getElementById('customerPhone').value = '';
                document.getElementById('customerEmail').value = '';
                document.getElementById('customerModalLabel').textContent = 'Tambah Customer';
                document.getElementById('formErrors').classList.add('d-none');
            }
            new bootstrap.Modal(document.getElementById('customerModal')).show();
        }

        /* ──────────────────────────────────────────────
           MODAL — EDIT
        ────────────────────────────────────────────── */
        async function editCustomer (id) {
            try {
                const res = await fetch(`${BASE_URL}/${id}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const json = await res.json();
                const s = json.data;

                document.getElementById('customerId').value = s.id;
                document.getElementById('customerName').value = s.name ?? '';
                document.getElementById('customerAddress').value = s.address ?? '';
                document.getElementById('customerPhone').value = s.phone ?? '';
                document.getElementById('customerEmail').value = s.email ?? '';
                document.getElementById('customerModalLabel').textContent = 'Edit Customer';
                document.getElementById('formErrors').classList.add('d-none');

                openModal(false);
            } catch (e) {
                Swal.fire('Error', 'Gagal memuat data customer.', 'error');
            }
        }

        /* ──────────────────────────────────────────────
           SAVE (CREATE / UPDATE)
        ────────────────────────────────────────────── */
        async function saveCustomer() {
            const id = document.getElementById('customerId').value;
            const isEdit = !!id;
            const url = isEdit ? `${BASE_URL}/${id}` : BASE_URL;
            const method = isEdit ? 'PUT' : 'POST';

            const payload = {
                name: document.getElementById('customerName').value.trim(),
                address: document.getElementById('customerAddress').value.trim(),
                phone: document.getElementById('customerPhone').value.trim(),
                email: document.getElementById('customerEmail').value.trim(),
            };

            // Simple client-side check
            if (!payload.name) {
                showFormErrors(['Nama customer wajib diisi.']);
                return;
            }

            setBtnLoading(true);

            try {
                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                    },
                    body: JSON.stringify(payload),
                });
                const json = await res.json();

                if (!res.ok) {
                    // Laravel validation errors
                    const errs = json.errors ?
                        Object.values(json.errors).flat() : [json.message ?? 'Terjadi kesalahan.'];
                    showFormErrors(errs);
                    return;
                }

                bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();

                Swal.fire({
                    icon: 'success',
                    title: isEdit ? 'Berhasil diperbarui!' : 'Berhasil ditambahkan!',
                    timer: 1500,
                    showConfirmButton: false,
                });

                fetchTable(isEdit ? currentPage : 1);
            } catch (e) {
                Swal.fire('Error', 'Koneksi gagal. Coba lagi.', 'error');
            } finally {
                setBtnLoading(false);
            }
        }

        /* ──────────────────────────────────────────────
           DELETE
        ────────────────────────────────────────────── */
        async function deleteCustomer(id, name) {
            const result = await Swal.fire({
                title: 'Hapus Customer?',
                html: `Customer <strong>${esc(name)}</strong> akan dihapus permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
            });

            if (!result.isConfirmed) return;

            try {
                const res = await fetch(`${BASE_URL}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                });

                if (!res.ok) throw new Error();

                Swal.fire({
                    icon: 'success',
                    title: 'Dihapus!',
                    timer: 1200,
                    showConfirmButton: false
                });
                fetchTable(currentPage);
            } catch (e) {
                Swal.fire('Error', 'Gagal menghapus customer.', 'error');
            }
        }

        /* ──────────────────────────────────────────────
           UI HELPERS
        ────────────────────────────────────────────── */
        function showFormErrors(errors) {
            const list = document.getElementById('formErrorList');
            list.innerHTML = errors.map(e => `<li>${e}</li>`).join('');
            document.getElementById('formErrors').classList.remove('d-none');
        }

        function setBtnLoading(state) {
            document.getElementById('saveBtn').disabled = state;
            document.getElementById('saveBtnText').textContent = state ? 'Menyimpan…' : 'Simpan';
            document.getElementById('saveBtnSpinner').classList.toggle('d-none', !state);
        }

        /* ──────────────────────────────────────────────
           INIT
        ────────────────────────────────────────────── */
        document.addEventListener('DOMContentLoaded', () => bindPaginationClicks());
    </script>
@endpush
