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
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Nama produk atau jasa
                $table->text('description')->nullable(); // Deskripsi produk/jasa (opsional)
                $table->decimal('price', 10, 2); // Harga dengan 2 angka desimal (contoh: 12345678.99)
                $table->integer('stock')->default(0); // Jumlah stok produk
                $table->enum('type', ['product', 'service'])->default('product'); // Tipe: produk atau jasa
                $table->timestamps(); // Kolom created_at dan updated_at
            });
        }

        /**
         * Batalkan migrasi.
         */
        public function down(): void
        {
            Schema::dropIfExists('products');
        }
    };
    