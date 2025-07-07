<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Transaction extends Model
    {
        use HasFactory;

        // Mendefinisikan kolom yang dapat diisi secara massal
        protected $fillable = [
            'customer_id',
            'total_amount',
            'notes',
        ];

        // Relasi one-to-many terbalik dengan Customer
        // Sebuah transaksi dimiliki oleh satu pelanggan
        public function customer()
        {
            return $this->belongsTo(Customer::class);
        }

        // Relasi many-to-many dengan Product
        // Sebuah transaksi bisa memiliki banyak produk
        public function products()
        {
            return $this->belongsToMany(Product::class)
                        ->withPivot('quantity', 'price_at_transaction') // Mengambil kolom pivot
                        ->withTimestamps(); // Jika ada kolom created_at/updated_at di tabel pivot
        }
    }
    