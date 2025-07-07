    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Jalankan migrasi.
         */
        public function up(): void
        {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade'); // ID pelanggan (foreign key)
                $table->decimal('total_amount', 10, 2); // Total jumlah transaksi
                $table->text('notes')->nullable(); // Catatan transaksi (opsional)
                $table->timestamps(); // Kolom created_at dan updated_at
            });

            // Tabel pivot untuk relasi many-to-many antara transaksi dan produk
            Schema::create('product_transaction', function (Blueprint $table) {
                $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->integer('quantity'); // Kuantitas produk dalam transaksi
                $table->decimal('price_at_transaction', 10, 2); // Harga produk saat transaksi
                $table->primary(['transaction_id', 'product_id']); // Primary key gabungan
            });
        }

        /**
         * Batalkan migrasi.
         */
        public function down(): void
        {
            Schema::dropIfExists('product_transaction');
            Schema::dropIfExists('transactions');
        }
    };