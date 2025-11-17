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
        Schema::create('hasil_seleksi_mitra', function (Blueprint $table) {
            $table->bigIncrements('id_hasil_seleksi_mitra');

            // Foreign key ke id_seleksi_mitra dari data_seleksi_mitra
            $table->unsignedBigInteger('id_seleksi_mitra');
            $table->foreign('id_seleksi_mitra')
                  ->references('id_seleksi_mitra')
                  ->on('data_seleksi_mitra')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            // Foreign key ke id_mitra dari data_mitra
            $table->unsignedBigInteger('id_mitra');
            $table->foreign('id_mitra')
                  ->references('id_mitra')
                  ->on('data_mitra')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            // Kolom enum untuk setiap dokumen
            $table->enum('surat_permohonan', ['Lolos', 'Tidak Lolos'])->nullable();
            $table->enum('akta_notaris', ['Lolos', 'Tidak Lolos'])->nullable();
            $table->enum('nib', ['Lolos', 'Tidak Lolos'])->nullable();
            $table->enum('ktp', ['Lolos', 'Tidak Lolos'])->nullable();
            $table->enum('no_rekening', ['Lolos', 'Tidak Lolos'])->nullable();
            $table->enum('npwp', ['Lolos', 'Tidak Lolos'])->nullable();
            $table->enum('surat_kuasa', ['Lolos', 'Tidak Lolos'])->nullable();

            // Kolom enum untuk sarana pengeringan
            $table->enum('lantai_jemur', ['Lolos', 'Tidak Lolos'])->nullable();
            $table->enum('sarana_lainnya', ['Lolos', 'Tidak Lolos'])->nullable();

            // Kolom enum untuk sarana penggilingan
            $table->enum('mesin_memecah_kulit', ['Lolos', 'Tidak Lolos'])->nullable();
            $table->enum('mesin_pemisah_gabah_dengan_beras', ['Lolos', 'Tidak Lolos'])->nullable();
            $table->enum('mesin_penyosoh', ['Lolos', 'Tidak Lolos'])->nullable();
            $table->enum('alat_pemisah_beras', ['Lolos', 'Tidak Lolos'])->nullable();

            // Kolom kesimpulan per kategori
            $table->enum('kesimpulan_dokumen', ['Lolos', 'Tidak Lolos'])->nullable();
            $table->enum('kesimpulan_sarana_pengeringan', ['Lolos', 'Tidak Lolos'])->nullable();
            $table->enum('kesimpulan_sarana_penggilingan', ['Lolos', 'Tidak Lolos'])->nullable();

            // Kolom rekap array (JSON)
            $table->json('dokumen_ada_valid')->nullable();
            $table->json('dokumen_ada_tidak_valid')->nullable();
            $table->json('dokumen_tidak_ada')->nullable();
            $table->json('sarana_pengeringan_ada')->nullable();
            $table->json('sarana_pengeringan_tidak_ada')->nullable();
            $table->json('sarana_penggilingan_ada')->nullable();
            $table->json('sarana_penggilingan_tidak_ada')->nullable();

            // Kesimpulan akhir
            $table->enum('kesimpulan_akhir', ['Lolos', 'Tidak Lolos'])->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_seleksi_mitra');
    }
};