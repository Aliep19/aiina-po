<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $allowedSort = ['name', 'phone', 'email', 'created_at', 'updated_at'];

        $search  = $request->input('search', '');
        $sortBy  = in_array($request->input('sort_by'), $allowedSort)
                    ? $request->input('sort_by') : 'id';
        $order   = strtolower($request->input('order', 'asc')) === 'desc' ? 'desc' : 'asc';
        $perPage = max(1, (int) $request->input('per_page', 10));

        $query = Customer::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                  ->orWhere('name',     'like', "%{$search}%")
                  ->orWhere('address',  'like', "%{$search}%")
                  ->orWhere('phone',    'like', "%{$search}%")
                  ->orWhere('email',    'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy($sortBy, $order)->paginate($perPage)->withQueryString();

        // AJAX request → JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'  => true,
                'message' => 'OK',
                'data'    => $customers,
                'filters' => compact('search', 'sortBy', 'order', 'perPage'),
            ]);
        }

        // Normal request → view
        return view('customers.index', compact('customers'));
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'address'  => 'nullable|string',
            'phone'    => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:255',
        ]);

        $customer = Customer::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Customer berhasil ditambahkan.',
            'data'    => $customer,
        ], 201);
    }

    /**
     * Display the specified customer.
     */
    public function show(string $id)
    {
        $customer = Customer::findOrFail($id);

        return response()->json([
            'status'  => true,
            'message' => 'Data customer ditemukan.',
            'data'    => $customer,
        ]);
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'address'  => 'nullable|string',
            'phone'    => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:255',
        ]);

        $customer->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Customer berhasil diperbarui.',
            'data'    => $customer,
        ]);
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Customer berhasil dihapus.',
            'data'    => null,
        ]);
    }
}
