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
        Schema::create('hpp_documents', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_hpp', ['Gabah dan Beras', 'Jagung']); // Jenis HPP
            $table->string('nomor_keputusan'); // Nomor Keputusan (contoh: "14")
            $table->integer('tahun'); // Tahun keputusan (contoh: 2025)
            $table->text('tentang'); // Tentang apa keputusan tersebut
            $table->boolean('is_active')->default(true); // Status aktif/tidak
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hpp_documents');
    }
};
