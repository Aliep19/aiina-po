@forelse ($suppliers as $i => $s)
<tr>
    <td class="ps-4">
        <p class="text-xs font-weight-bold mb-0">{{ $suppliers->firstItem() + $i }}</p>
    </td>
    <td>
        <p class="text-xs font-weight-bold mb-0">{{ $s->name }}</p>
    </td>
    <td>
        <p class="text-xs text-secondary mb-0" style="max-width:180px;white-space:normal">
            {{ $s->address ?? '—' }}
        </p>
    </td>
    <td><p class="text-xs mb-0">{{ $s->phone ?? '—' }}</p></td>
    <td><p class="text-xs mb-0">{{ $s->email ?? '—' }}</p></td>
    <td><p class="text-xs mb-0">{{ $s->pic_name ?? '—' }}</p></td>
    <td class="text-center">
        <button class="btn btn-xs btn-outline-info me-1"
                onclick="editSupplier({{ $s->id }})" title="Edit">
            <i class="fas fa-edit"></i>
        </button>
        <button class="btn btn-xs btn-outline-danger"
                onclick="deleteSupplier({{ $s->id }}, '{{ addslashes($s->name) }}')" title="Hapus">
            <i class="fas fa-trash"></i>
        </button>
    </td>
</tr>
@empty
@endforelse
