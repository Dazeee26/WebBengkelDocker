<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Menampilkan daftar semua pelanggan.
     */
    public function index()
    {
        $customers = Customer::all();
        return view('customers.index', compact('customers'));
    }

    /**
     * Menampilkan form untuk membuat pelanggan baru.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Menyimpan pelanggan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
        ]);

        Customer::create($request->all());

        return redirect()->route('customers.index')
                         ->with('success', 'Customer created successfully.');
    }

    /**
     * Menampilkan detail pelanggan tertentu.
     */
    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    /**
     * Menampilkan form untuk mengedit pelanggan.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Memperbarui pelanggan di database.
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id,
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')
                         ->with('success', 'Customer updated successfully.');
    }

    /**
     * Menghapus pelanggan dari database.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')
                         ->with('success', 'Customer deleted successfully.');
    }
}
