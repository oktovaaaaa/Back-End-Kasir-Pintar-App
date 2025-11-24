<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // GET /api/customers
    public function index()
    {
        // bisa dipakai untuk picker dan halaman pelanggan
        $customers = Customer::orderBy('name')->get();

        return response()->json($customers);
    }

    // POST /api/customers
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'nullable|email|max:255',
            'phone'     => 'nullable|string|max:50',
            'address'   => 'nullable|string',
            'company'   => 'nullable|string|max:255',
            'note'      => 'nullable|string',
        ]);

        $customer = Customer::create($data);

        return response()->json($customer, 201);
    }

    // GET /api/customers/{id}
    // termasuk riwayat transaksi & kasbon
    public function show($id)
    {
        $customer = Customer::with(['sales' => function ($q) {
            $q->orderBy('created_at', 'desc')
              ->with('items.product');
        }])->findOrFail($id);

        return response()->json($customer);
    }

    // PUT /api/customers/{id}
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'nullable|email|max:255',
            'phone'     => 'nullable|string|max:50',
            'address'   => 'nullable|string',
            'company'   => 'nullable|string|max:255',
            'note'      => 'nullable|string',
        ]);

        $customer->update($data);

        return response()->json($customer);
    }

    // DELETE /api/customers/{id}
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json(['message' => 'Customer deleted']);
    }
}
