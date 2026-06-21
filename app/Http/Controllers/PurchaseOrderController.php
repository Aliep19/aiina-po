<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::query()->with(['supplier', 'creator']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('po_number', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $sortBy = in_array($request->get('sort_by'), ['po_number', 'po_date', 'total_amount'])
            ? $request->get('sort_by')
            : 'id';

        $order = $request->get('order') === 'desc' ? 'desc' : 'asc';

        $perPage = in_array((int) $request->get('per_page'), [10, 25, 50, 100])
            ? (int) $request->get('per_page')
            : 10;

        $purchaseOrders = $query->orderBy($sortBy, $order)
            ->paginate($perPage)
            ->withQueryString();

        if ($request->wantsJson()) {
            $purchaseOrders->getCollection()->transform(function ($po) {
                return [
                    'id'            => $po->id,
                    'po_number'     => $po->po_number,
                    'supplier_name' => $po->supplier->name ?? '-',
                    'po_date'       => optional($po->po_date)->format('d M Y'),
                    'delivery_date' => optional($po->delivery_date)->format('d M Y'),
                    'total_amount'  => $po->total_amount,
                    'creator_name'  => $po->creator->name ?? 'System',
                ];
            });

            return response()->json(['data' => $purchaseOrders]);
        }

        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();

        $products = Product::with(['suppliers'])->orderBy('name')->get();

        $supplierProducts = [];

        foreach ($suppliers as $supplier) {
            $supplierProducts[$supplier->id] = [];
            foreach ($supplier->products as $product) {   // relasi dari Supplier model
                $supplierProducts[$supplier->id][$product->id] = [
                    'name'  => $product->name . ' (' . $product->unit . ')',
                    'price' => $product->pivot->supplier_price,
                ];
            }
        }

        return view('purchase-orders.create', compact('suppliers', 'supplierProducts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id'    => 'required|exists:suppliers,id',
            'po_date'        => 'required|date',
            'delivery_date'  => 'nullable|date|after_or_equal:po_date',
            'notes'          => 'nullable|string',
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $poNumber = 'PO-' . date('Ymd') . '-' . str_pad(
                PurchaseOrder::whereDate('created_at', today())->count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );

            $purchaseOrder = PurchaseOrder::create([
                'po_number'     => $poNumber,
                'supplier_id'   => $request->supplier_id,
                'po_date'       => $request->po_date,
                'delivery_date' => $request->delivery_date,
                'total_amount'  => 0,
                'notes'         => $request->notes,
                'created_by'    => Auth::id(),
            ]);

            $totalAmount = 0;

            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id'        => $item['product_id'],
                    'quantity'          => $item['quantity'],
                    'unit_price'        => $item['unit_price'],
                    'subtotal'          => $subtotal,
                ]);

                $totalAmount += $subtotal;
            }

            $purchaseOrder->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()
                ->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase Order berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.product', 'creator']);
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::with('suppliers')->orderBy('name')->get();

        $supplierProducts = [];

        foreach ($suppliers as $supplier) {
            $supplierProducts[$supplier->id] = [];
            foreach ($supplier->products as $product) {
                $supplierProducts[$supplier->id][$product->id] = [
                    'name'  => $product->name . ' (' . $product->unit . ')',
                    'price' => $product->pivot->supplier_price,
                ];
            }
        }

        $purchaseOrder->load('items.product');

        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'supplierProducts'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'supplier_id'    => 'required|exists:suppliers,id',
            'po_date'        => 'required|date',
            'delivery_date'  => 'nullable|date|after_or_equal:po_date',
            'notes'          => 'nullable|string',
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $purchaseOrder->update([
                'supplier_id'   => $request->supplier_id,
                'po_date'       => $request->po_date,
                'delivery_date' => $request->delivery_date,
                'notes'         => $request->notes,
            ]);

            // Hapus item lama
            $purchaseOrder->items()->delete();

            $totalAmount = 0;

            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id'        => $item['product_id'],
                    'quantity'          => $item['quantity'],
                    'unit_price'        => $item['unit_price'],
                    'subtotal'          => $subtotal,
                ]);

                $totalAmount += $subtotal;
            }

            $purchaseOrder->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()
                ->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase Order berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request, PurchaseOrder $purchaseOrder)
    {
        try {
            DB::beginTransaction();

            $purchaseOrder->items()->delete();
            $purchaseOrder->delete();

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Purchase Order berhasil dihapus.']);
            }

            return redirect()
                ->route('purchase-orders.index')
                ->with('success', 'Purchase Order berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Gagal menghapus Purchase Order.'], 500);
            }

            return back()->with('error', 'Gagal menghapus Purchase Order.');
        }
    }

    public function downloadPdf(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.product', 'creator']);

        $pdf = Pdf::loadView('purchase-orders.pdf', compact('purchaseOrder'));

        // Optional: Set paper size (A4 is default)
        $pdf->setPaper('A4', 'portrait');

        // Download the PDF
        return $pdf->download('PO-' . $purchaseOrder->po_number . '.pdf');
    }

    /**
     * Stream PDF for preview
     */
    public function previewPdf(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.product', 'creator']);

        $pdf = Pdf::loadView('purchase-orders.pdf', compact('purchaseOrder'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('PO-' . $purchaseOrder->po_number . '.pdf');
    }
}
