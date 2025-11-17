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
            $table->enum('surat_permohonan', ['Ada', 'Tidak Ada'])->nullable();
            $table->date('mb_surat_permohonan')->nullable();
            $table->enum('akta_notaris', ['Ada', 'Tidak Ada'])->nullable();
            $table->date('mb_akta_notaris')->nullable();
            $table->enum('nib', ['Ada', 'Tidak Ada'])->nullable();
            $table->date('mb_nib')->nullable();
            $table->enum('ktp', ['Ada', 'Tidak Ada'])->nullable();
            $table->string('mb_ktp')->nullable()->default('Seumur Hidup');
            $table->enum('no_rekening', ['Ada', 'Tidak Ada'])->nullable();
            $table->date('mb_no_rekening')->nullable();
            $table->enum('npwp', ['Ada', 'Tidak Ada'])->nullable();
            $table->date('mb_npwp')->nullable();
            $table->enum('surat_kuasa', ['Ada', 'Tidak Ada'])->nullable();
            $table->date('mb_surat_kuasa')->nullable();
            $table->enum('lantai_jemur', ['Ada', 'Tidak Ada'])->nullable();
            $table->enum('sarana_lainnya', ['Ada', 'Tidak Ada'])->nullable();
            $table->enum('mesin_memecah_kulit', ['Ada', 'Tidak Ada'])->nullable();
            $table->enum('mesin_pemisah_gabah', ['Ada', 'Tidak Ada'])->nullable();
            $table->enum('mesin_penyosoh', ['Ada', 'Tidak Ada'])->nullable();
            $table->enum('alat_pemisah_beras', ['Ada', 'Tidak Ada'])->nullable();
            $table->enum('status_seleksi', ['pending', 'lolos', 'Tidak lolos'])->nullable()->default('pending');
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
