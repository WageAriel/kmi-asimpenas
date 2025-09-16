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
        Schema::create('data_seleksi_mitra', function (Blueprint $table) {
            $table->bigIncrements('id_seleksi_mitra');
            $table->unsignedBigInteger('id_mitra'); 
            $table->enum('surat_permohonan', ['ada', 'tidak ada'])->nullable();
            $table->date('mb_surat_permohonan')->nullable();
            $table->enum('akta_notaris', ['ada', 'tidak ada'])->nullable();
            $table->date('mb_akta_notaris')->nullable();
            $table->enum('nib', ['ada', 'tidak ada'])->nullable();
            $table->date('mb_nib')->nullable();
            $table->enum('ktp', ['ada', 'tidak ada'])->nullable();
            $table->date('mb_ktp')->nullable();
            $table->enum('no_rekening', ['ada', 'tidak ada'])->nullable();
            $table->date('mb_no_rekening')->nullable();
            $table->enum('npwp', ['ada', 'tidak ada'])->nullable();
            $table->date('mb_npwp')->nullable();
            $table->enum('surat_kuasa', ['ada', 'tidak ada'])->nullable();
            $table->date('mb_surat_kuasa')->nullable();
            $table->enum('lantai_jemur', ['ada', 'tidak ada'])->nullable();
            $table->enum('sarana_lainnya', ['ada', 'tidak ada'])->nullable();
            $table->enum('mesin_memecah_kulit', ['ada', 'tidak ada'])->nullable();
            $table->enum('mesin_pemisah_gabah', ['ada', 'tidak ada'])->nullable();
            $table->enum('mesin_penyosoh', ['ada', 'tidak ada'])->nullable();
            $table->enum('alat_pemisah_beras', ['ada', 'tidak ada'])->nullable();
            $table->timestamps();

            $table->foreign('id_mitra')
            ->references('id_mitra')
            ->on('data_mitra')
            ->onUpdate('cascade')
            ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_seleksi_mitra');
    }
};
