@extends('layouts.app')

@section('title', 'Product/Service Details')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md max-w-lg mx-auto">
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Product/Service Details</h2>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Name:</label>
        <p class="mt-1 text-lg text-gray-900">{{ $product->name }}</p>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Description:</label>
        <p class="mt-1 text-gray-900">{{ $product->description ?? 'N/A' }}</p>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Price:</label>
        <p class="mt-1 text-lg text-gray-900">Rp{{ number_format($product->price, 2, ',', '.') }}</p>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Stock:</label>
        <p class="mt-1 text-lg text-gray-900">{{ $product->stock }}</p>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Type:</label>
        <p class="mt-1 text-lg text-gray-900">{{ ucfirst($product->type) }}</p>
    </div>

    <div class="flex justify-end">
        <a href="{{ route('products.index') }}" class="btn bg-gray-500 hover:bg-gray-600 text-white mr-2">Back to List</a>
        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">Edit Product/Service</a>
    </div>
</div>
@endsection
