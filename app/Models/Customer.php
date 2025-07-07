<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Customer extends Model
    {
        use HasFactory;

        // Mendefinisikan kolom yang dapat diisi secara massal
        protected $fillable = [
            'name',
            'phone',
            'address',
            'email',
        ];

        // Relasi one-to-many dengan Transaction
        // Seorang pelanggan bisa memiliki banyak transaksi
        public function transactions()
        {
            return $this->hasMany(Transaction::class);
        }
    }
    