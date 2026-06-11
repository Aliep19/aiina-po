<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $allowedSort = ['name', 'phone', 'email', 'pic_name', 'created_at', 'updated_at'];

        $search  = $request->input('search', '');
        $sortBy  = in_array($request->input('sort_by'), $allowedSort)
                    ? $request->input('sort_by') : 'id';
        $order   = strtolower($request->input('order', 'asc')) === 'desc' ? 'desc' : 'asc';
        $perPage = max(1, (int) $request->input('per_page', 10));

        $query = Supplier::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                  ->orWhere('name',     'like', "%{$search}%")
                  ->orWhere('address',  'like', "%{$search}%")
                  ->orWhere('phone',    'like', "%{$search}%")
                  ->orWhere('email',    'like', "%{$search}%")
                  ->orWhere('pic_name', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy($sortBy, $order)->paginate($perPage)->withQueryString();

        // AJAX request → JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => true,
                'message' => 'OK',
                'data'    => $suppliers,
                'filters' => compact('search', 'sortBy', 'order', 'perPage'),
            ]);
        }

        // Normal request → view
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Store a newly created supplier.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'address'  => 'nullable|string',
            'phone'    => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:255',
            'pic_name' => 'nullable|string|max:255',
        ]);

        $supplier = Supplier::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Supplier berhasil ditambahkan.',
            'data'    => $supplier,
        ], 201);
    }

    /**
     * Display the specified supplier.
     */
    public function show(string $id)
    {
        $supplier = Supplier::findOrFail($id);

        return response()->json([
            'status'  => true,
            'message' => 'Data supplier ditemukan.',
            'data'    => $supplier,
        ]);
    }

    /**
     * Update the specified supplier.
     */
    public function update(Request $request, string $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'address'  => 'nullable|string',
            'phone'    => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:255',
            'pic_name' => 'nullable|string|max:255',
        ]);

        $supplier->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Supplier berhasil diperbarui.',
            'data'    => $supplier,
        ]);
    }

    /**
     * Remove the specified supplier.
     */
    public function destroy(string $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Supplier berhasil dihapus.',
            'data'    => null,
        ]);
    }
}
