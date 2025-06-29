@extends('layouts.app')

@section('title', 'Edit Product/Service')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md max-w-lg mx-auto">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Edit Product/Service</h2>

    <form action="{{ route('products.update', $product->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $product->name) }}" required>
        </div>
        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" id="description" rows="3" class="form-input">{{ old('description', $product->description) }}</textarea>
        </div>
        <div class="mb-4">
            <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
            <input type="number" name="price" id="price" step="0.01" class="form-input" value="{{ old('price', $product->price) }}" required>
        </div>
        <div class="mb-4">
            <label for="stock" class="block text-sm font-medium text-gray-700">Stock</label>
            <input type="number" name="stock" id="stock" class="form-input" value="{{ old('stock', $product->stock) }}" required>
        </div>
        <div class="mb-4">
            <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
            <select name="type" id="type" class="form-input" required>
                <option value="product" {{ old('type', $product->type) == 'product' ? 'selected' : '' }}>Product</option>
                <option value="service" {{ old('type', $product->type) == 'service' ? 'selected' : '' }}>Service</option>
            </select>
        </div>
        <div class="flex justify-end">
            <a href="{{ route('products.index') }}" class="btn bg-gray-500 hover:bg-gray-600 text-white mr-2">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Product/Service</button>
        </div>
    </form>
</div>
@endsection
