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
        Schema::table('hasil_seleksi_mitra', function (Blueprint $table) {
           $table->json('dokumen_ada_tidak_valid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_seleksi_mitra', function (Blueprint $table) {
            $table->dropColumn('dokumen_ada_tidak_valid');
        });
    }
};
