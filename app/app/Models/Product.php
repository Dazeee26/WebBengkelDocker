<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Product extends Model
    {
        use HasFactory;

        // Mendefinisikan kolom yang dapat diisi secara massal
        protected $fillable = [
            'name',
            'description',
            'price',
            'stock',
            'type',
        ];

        // Relasi many-to-many dengan Transaction
        // Sebuah produk bisa ada di banyak transaksi
        public function transactions()
        {
            return $this->belongsToMany(Transaction::class)
                        ->withPivot('quantity', 'price_at_transaction') // Mengambil kolom pivot
                        ->withTimestamps(); // Jika ada kolom created_at/updated_at di tabel pivot
        }
    }
    