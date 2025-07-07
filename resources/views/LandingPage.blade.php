@extends('layouts.app')

@section('title', 'Welcome to Bengkel App')

@section('content')
<div class="text-center py-20 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-lg shadow-xl">
    <h1 class="text-5xl font-extrabold mb-4 animate-fade-in-down">Selamat Datang di Aplikasi Bengkel Anda</h1>
    <p class="text-xl mb-8 animate-fade-in-up">Kelola Produk, Pelanggan, dan Transaksi dengan Mudah!</p>
    <div class="space-x-4 animate-zoom-in">
        <a href="{{ route('product.index') }}" class="btn btn-primary bg-yellow-500 hover:bg-yellow-600 border-yellow-700">
            Lihat Produk/Jasa
        </a>
        <a href="{{ route('transactions.create') }}" class="btn btn-primary bg-green-500 hover:bg-green-600 border-green-700">
            Buat Transaksi Baru
        </a>
    </div>
</div>

<div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
    <div class="bg-white p-6 rounded-lg shadow-md text-center">
        <div class="text-4xl text-blue-600 mb-4">⚙️</div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Manajemen Stok Efisien</h3>
        <p class="text-gray-600">Lacak stok produk dan layanan Anda secara real-time. Hindari kekurangan atau kelebihan stok.</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md text-center">
        <div class="text-4xl text-blue-600 mb-4">🧑‍🤝‍🧑</div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Data Pelanggan Terpusat</h3>
        <p class="text-gray-600">Simpan informasi pelanggan penting untuk layanan yang lebih personal dan efektif.</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md text-center">
        <div class="text-4xl text-blue-600 mb-4">💰</div>
        <h3 class="text-xl font-semibold text-gray-800 mb-2">Pencatatan Transaksi Akurat</h3>
        <p class="text-gray-600">Catat setiap penjualan dan layanan dengan detail, memudahkan pelacakan keuangan.</p>
    </div>
</div>

<style>
    /* Keyframe animations for a more dynamic landing page */
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes zoomIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }

    .animate-fade-in-down {
        animation: fadeInDown 1s ease-out forwards;
    }
    .animate-fade-in-up {
        animation: fadeInUp 1s ease-out forwards;
        animation-delay: 0.3s; /* Delay for a staggered effect */
    }
    .animate-zoom-in {
        animation: zoomIn 1s ease-out forwards;
        animation-delay: 0.6s; /* Further delay */
    }
</style>
@endsection
