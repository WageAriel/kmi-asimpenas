<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('data_klasifikasi_mitra', function (Blueprint $table) {
            $table->bigIncrements('id_klasifikasi_mitra');
            $table->unsignedBigInteger('id_mitra');
            $table->enum('mesin_pembersih_gabah', ['1', '2', '3'])->nullable();
            $table->enum('lantai_jemur', ['1', '2', '3'])->nullable();
            $table->enum('mesin_pengering', ['1', '2', '3'])->nullable();
            $table->enum('alat_pengering_lainnya', ['1', '2', '3'])->nullable();
            $table->enum('mesin_pembersih_awal', ['1', '2', '3'])->nullable();
            $table->enum('mesin_pemecah_kulit', ['1', '2', '3'])->nullable();
            $table->enum('mesin_pembersih_sekam', ['1', '2', '3'])->nullable();
            $table->enum('mesin_pemisah_gabah_pecah_kulit', ['1', '2', '3'])->nullable();
            $table->enum('mesin_pemisah_batu', ['1', '2', '3'])->nullable();
            $table->enum('mesin_penyosoh', ['1', '2', '3'])->nullable();
            $table->enum('mesin_pengkabut', ['1', '2', '3'])->nullable();
            $table->enum('mesin_pemesah_menir', ['1', '2', '3'])->nullable();
            $table->enum('mesin_pemisah_katul', ['1', '2', '3'])->nullable();
            $table->enum('mesin_pemisah_berdasarkan_ukuran', ['1', '2', '3'])->nullable();
            $table->enum('mesin_pemisah_berdasarkan_warna', ['1', '2', '3'])->nullable();
            $table->enum('tangki_penyimpanan', ['1', '2', '3'])->nullable();
            $table->enum('mesin_pengemas', ['1', '2', '3'])->nullable();
            $table->enum('mesin_jahit', ['1', '2', '3'])->nullable();
            $table->enum('gudang_konvensional', ['1', '2', '3'])->nullable();
            $table->enum('silo_gkg_hopper', ['1', '2', '3'])->nullable();
            $table->enum('truk', ['1', '2', '3'])->nullable();
            $table->enum('mini_lab', ['1', '2', '3'])->nullable();
            $table->enum('moisture_tester', ['1', '2', '3'])->nullable();
            $table->enum('pembanding_derajat_sosoh', ['1', '2', '3'])->nullable();
            $table->enum('bagian_quality_control', ['1', '2', '3'])->nullable();

            $table->timestamps();

            $table->foreign('id_mitra')
                  ->references('id_mitra')
                  ->on('data_mitra')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_klasifikasi_mitra');
    }
};
