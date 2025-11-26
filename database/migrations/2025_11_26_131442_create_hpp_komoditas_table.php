<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hpp_komoditas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hpp_document_id')->constrained('hpp_documents')->onDelete('cascade');
            $table->string('nama_komoditas'); // GKP, Beras, Jagung Pipil Kering
            $table->string('tempat'); // Tingkat Petani, Gudang Perum BULOG
            $table->decimal('harga_per_kg', 10, 2); // Harga per kg
            $table->text('ketentuan')->nullable(); // Ketentuan (Derajat Sosoh, Kadar Air, dll)
            $table->integer('urutan')->default(0); // Urutan tampilan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hpp_komoditas');
    }
};
