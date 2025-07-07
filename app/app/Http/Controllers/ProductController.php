<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar semua produk/jasa.
     */
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    /**
     * Menampilkan form untuk membuat produk/jasa baru.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Menyimpan produk/jasa baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'type' => 'required|in:product,service',
        ]);

        Product::create($request->all());

        return redirect()->route('products.index')
                         ->with('success', 'Product/Service created successfully.');
    }

    /**
     * Menampilkan detail produk/jasa tertentu.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Menampilkan form untuk mengedit produk/jasa.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Memperbarui produk/jasa di database.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'type' => 'required|in:product,service',
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')
                         ->with('success', 'Product/Service updated successfully.');
    }

    /**
     * Menghapus produk/jasa dari database.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
                         ->with('success', 'Product/Service deleted successfully.');
    }
}
