<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Untuk transaksi database

class TransactionController extends Controller
{
    /**
     * Menampilkan daftar semua transaksi.
     */
    public function index()
    {
        $transactions = Transaction::with('customer', 'products')->latest()->get();
        return view('transactions.index', compact('transactions'));
    }

    /**
     * Menampilkan form untuk membuat transaksi baru.
     */
    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        return view('transactions.create', compact('customers', 'products'));
    }

    /**
     * Menyimpan transaksi baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'notes' => 'nullable|string',
            'products' => 'required|array', // Array produk yang dibeli
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $transaction = Transaction::create([
                'customer_id' => $request->customer_id,
                'total_amount' => 0, // Akan dihitung dan diupdate nanti
                'notes' => $request->notes,
            ]);

            foreach ($request->products as $item) {
                $product = Product::find($item['id']);
                if (!$product || $product->stock < $item['quantity']) {
                    DB::rollBack();
                    return back()->withInput()->withErrors("Stok {$product->name} tidak cukup.");
                }

                $priceAtTransaction = $product->price;
                $subtotal = $priceAtTransaction * $item['quantity'];
                $totalAmount += $subtotal;

                // Lampirkan produk ke transaksi dengan data pivot
                $transaction->products()->attach($product->id, [
                    'quantity' => $item['quantity'],
                    'price_at_transaction' => $priceAtTransaction,
                ]);

                // Kurangi stok produk
                $product->decrement('stock', $item['quantity']);
            }

            $transaction->update(['total_amount' => $totalAmount]);

            DB::commit();
            return redirect()->route('transactions.index')
                             ->with('success', 'Transaction created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors('Error creating transaction: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail transaksi tertentu.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load('customer', 'products');
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Tidak ada fungsi edit/update langsung untuk transaksi di sini,
     * karena perubahan transaksi seringkali rumit dan melibatkan stok.
     * Jika diperlukan, Anda bisa menambahkan logika yang lebih kompleks
     * atau membuat fitur "pembatalan/retur" terpisah.
     */
    public function edit(Transaction $transaction)
    {
        // Mengarahkan ke halaman detail atau menampilkan pesan bahwa edit tidak didukung
        return redirect()->route('transactions.show', $transaction->id)
                         ->with('info', 'Editing transactions is not directly supported. View details instead.');
    }

    public function update(Request $request, Transaction $transaction)
    {
        // Tidak diimplementasikan, lihat metode edit()
        return redirect()->route('transactions.show', $transaction->id)
                         ->with('info', 'Updating transactions is not directly supported.');
    }


    /**
     * Menghapus transaksi dari database (dengan mengembalikan stok).
     */
    public function destroy(Transaction $transaction)
    {
        DB::beginTransaction();
        try {
            // Kembalikan stok produk yang terkait dengan transaksi ini
            foreach ($transaction->products as $product) {
                $product->increment('stock', $product->pivot->quantity);
            }

            $transaction->delete();

            DB::commit();
            return redirect()->route('transactions.index')
                             ->with('success', 'Transaction deleted successfully (stock returned).');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error deleting transaction: ' . $e->getMessage());
        }
    }
}
