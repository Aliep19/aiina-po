@foreach ($purchaseOrders as $po)
    <tr>
        <td class="ps-4">
            <p class="text-xs font-weight-bold mb-0">{{ $purchaseOrders->firstItem() + $loop->index }}</p>
        </td>
        <td>
            <p class="text-xs font-weight-bold mb-0">{{ $po->po_number }}</p>
        </td>
        <td>
            <p class="text-xs mb-0">{{ $po->supplier->name ?? '—' }}</p>
        </td>
        <td>
            <p class="text-xs mb-0">{{ $po->po_date?->format('d M Y') ?? '—' }}</p>
        </td>
        <td>
            <p class="text-xs mb-0">{{ $po->delivery_date?->format('d M Y') ?? '—' }}</p>
        </td>
        <td>
            <p class="text-xs mb-0">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</p>
        </td>
        <td>
            <p class="text-xs mb-0">{{ $po->creator->name ?? 'System' }}</p>
        </td>
        <td class="text-center">
            <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-xs btn-outline-secondary me-1" title="Lihat">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('purchase-orders.edit', $po) }}" class="btn btn-xs btn-outline-info me-1" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            <button type="button" class="btn btn-xs btn-outline-danger"
                    onclick="deletePO({{ $po->id }}, '{{ $po->po_number }}')" title="Hapus">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
@endforeach
