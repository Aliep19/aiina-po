<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $allowedSort = [ 'name', 'unit', 'default_price', 'created_at'];

        $search  = trim($request->input('search', ''));
        $sortBy  = in_array($request->input('sort_by'), $allowedSort)
                    ? $request->input('sort_by') : 'id';
        $order   = strtolower($request->input('order', 'asc')) === 'desc' ? 'desc' : 'asc';
        $perPage = max(1, min(100, (int) $request->input('per_page', 10)));

        $products = Product::query()
            // Hanya kolom list — specification sengaja tidak diambil di sini
            ->select(['id','name', 'unit', 'default_price', 'created_at'])
            // Jumlah supplier via sub-query COUNT (tidak join)
            ->withCount('suppliers')
            // Nama supplier primary via sub-select (1 query tambahan total)
            ->withAggregate(
                ['primarySupplier' => fn ($q) => $q->select('name')->limit(1)],
                'name'
            )
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name',          'like', "%{$search}%")
                          ->orWhere('unit',          'like', "%{$search}%");
                });
            })
            ->orderBy($sortBy, $order)
            ->paginate($perPage)
            ->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'data'   => $products,
            ]);
        }

        // Supplier list untuk dropdown di modal (hanya id + name)
        $suppliers = Supplier::select('id', 'name')->orderBy('name')->get();

        return view('products.index', compact('products', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'specification' => 'nullable|string',
            'unit'          => 'nullable|string|max:30',
            'default_price' => 'nullable|numeric|min:0',
            // Suppliers pivot
            'suppliers'               => 'nullable|array',
            'suppliers.*.id'          => 'required|exists:suppliers,id',
            'suppliers.*.supplier_price' => 'nullable|numeric|min:0',
            'suppliers.*.status_supplier'  => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::create([
                'name'          => $validated['name'],
                'specification' => $validated['specification'] ?? null,
                'unit'          => $validated['unit'] ?? null,
                'default_price' => $validated['default_price'] ?? 0,
            ]);

            $this->syncSuppliers($product, $validated['suppliers'] ?? []);
        });

        return response()->json(['status' => true, 'message' => 'Produk berhasil ditambahkan.'], 201);
    }

    public function show(string $id)
    {
        $product = Product::with([
            'suppliers:id,name,phone,email,pic_name',   // hanya kolom perlu
        ])->findOrFail($id);

        return response()->json(['status' => true, 'data' => $product]);
    }

    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'sometimes|required|string|max:255',
            'specification' => 'nullable|string',
            'unit'          => 'nullable|string|max:30',
            'default_price' => 'nullable|numeric|min:0',
            'suppliers'               => 'nullable|array',
            'suppliers.*.id'          => 'required|exists:suppliers,id',
            'suppliers.*.supplier_price' => 'nullable|numeric|min:0',
            'suppliers.*.status_supplier'  => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($product, $validated) {
            $product->update(array_filter([
                'name'          => $validated['name']          ?? null,
                'specification' => $validated['specification'] ?? null,
                'unit'          => $validated['unit']          ?? null,
                'default_price' => $validated['default_price'] ?? null,
            ], fn ($v) => !is_null($v)));

            if (array_key_exists('suppliers', $validated)) {
                $this->syncSuppliers($product, $validated['suppliers'] ?? []);
            }
        });

        return response()->json(['status' => true, 'message' => 'Produk berhasil diperbarui.']);
    }

    public function destroy(string $id)
    {
        // cascadeOnDelete di migration sudah hapus pivot otomatis
        Product::findOrFail($id)->delete();

        return response()->json(['status' => true, 'message' => 'Produk berhasil dihapus.']);
    }

    private function syncSuppliers(Product $product, array $suppliers): void
    {
        if (empty($suppliers)) {
            $product->suppliers()->detach();
            return;
        }

        // Pastikan hanya 1 primary
        $hasPrimary = collect($suppliers)->contains('status_supplier', true);

        $syncData = [];
        foreach ($suppliers as $i => $s) {
            $syncData[$s['id']] = [
                'supplier_price' => $s['supplier_price'] ?? 0,
                'status_supplier'     => $hasPrimary
                    ? (bool) ($s['status_supplier'] ?? false)
                    : ($i === 0),   // fallback: supplier pertama jadi primary
            ];
        }

        // sync() → insert/update/delete dalam 1 operasi, tidak N query
        $product->suppliers()->sync($syncData);
    }
}
