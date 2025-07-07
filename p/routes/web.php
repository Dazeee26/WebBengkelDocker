<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Resource routes untuk CRUD Produk/Jasa
Route::resource('products', ProductController::class);

// Resource routes untuk CRUD Pelanggan
Route::resource('customers', CustomerController::class);

// Resource routes untuk CRUD Transaksi (kecuali edit/update langsung)
Route::resource('transactions', TransactionController::class)->except(['edit', 'update']);

// Contoh route untuk Home/Dashboard (opsional)
// Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
