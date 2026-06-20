@forelse ($products as $i => $p)
<tr>
    <td class="ps-4">
        <p class="text-xs font-weight-bold mb-0">{{ $products->firstItem() + $i }}</p>
    </td>
    <td><p class="text-xs font-weight-bold mb-0" style="max-width:200px">{{ $p->name }}</p></td>
    <td><p class="text-xs mb-0">{{ $p->unit ?? '—' }}</p></td>
    <td><p class="text-xs mb-0">Rp {{ number_format($p->default_price, 0, ',', '.') }}</p></td>
    <td><p class="text-xs mb-0">{{ $p->primary_supplier_name ?? '—' }}</p></td>
    <td class="text-center">
        <span class="badge badge-sm bg-gradient-info">{{ $p->suppliers_count }}</span>
    </td>
    <td class="text-center">
        <button class="btn btn-xs btn-outline-secondary me-1"
                onclick="showDetail({{ $p->id }})" title="Detail">
            <i class="fas fa-eye"></i>
        </button>
        <button class="btn btn-xs btn-outline-info me-1"
                onclick="editProduct({{ $p->id }})" title="Edit">
            <i class="fas fa-edit"></i>
        </button>
        <button class="btn btn-xs btn-outline-danger"
                onclick="deleteProduct({{ $p->id }}, '{{ addslashes($p->name) }}')" title="Hapus">
            <i class="fas fa-trash"></i>
        </button>
    </td>
</tr>
@empty
@endforelse
