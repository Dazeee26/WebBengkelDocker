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
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Nama pelanggan
                $table->string('phone')->nullable(); // Nomor telepon pelanggan
                $table->string('address')->nullable(); // Alamat pelanggan
                $table->string('email')->unique()->nullable(); // Email pelanggan (unik, opsional)
                $table->timestamps(); // Kolom created_at dan updated_at
            });
        }

        /**
         * Batalkan migrasi.
         */
        public function down(): void
        {
            Schema::dropIfExists('customers');
        }
    };
    