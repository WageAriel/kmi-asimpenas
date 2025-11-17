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
            $table->enum('mesin_pembersih_gabah', ['1. Tidak Ada', '2. Ada | ≤ 20', '3. Ada | > 20'])->nullable();
            $table->enum('lantai_jemur', ['1. Tidak ada', '2. Ada | 1 s/d 10', '3. Ada | > 10'])->nullable();
            $table->enum('mesin_pengering', ['1. Tidak ada', '2. Ada | ≤ 20', '3. Ada | > 20'])->nullable();
            $table->enum('alat_pengering_lainnya', ['1. Ada | ≤ 1', '2. Tidak Disyaratkan', '3. Tidak Disyaratkan'])->nullable();
            $table->enum('mesin_pembersih_awal', ['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])->nullable();
            $table->enum('mesin_pemecah_kulit', ['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])->nullable();
            $table->enum('mesin_pembersih_sekam', ['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])->nullable();
            $table->enum('mesin_pemisah_gabah_pecah_kulit', ['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])->nullable();
            $table->enum('mesin_pemisah_batu', ['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])->nullable();
            $table->enum('mesin_penyosoh', ['1. Ada | ≤ 1 | 1', '2. Ada | 1 s/d 3 | 1', '3. Ada | > 3 | 2'])->nullable();
            $table->enum('mesin_pengkabut', ['1. Ada | ≤ 1 | 1', '2. Ada | 1 s/d 3 | 1', '3. Ada | > 3 | 2'])->nullable();
            $table->enum('mesin_pemesah_menir', ['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])->nullable();
            $table->enum('mesin_pemisah_katul', ['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])->nullable();
            $table->enum('mesin_pemisah_berdasarkan_ukuran', ['1. Ada | ≤ 1', '2. Ada | 1 s/d 3', '3. Ada | > 3'])->nullable();
            $table->enum('mesin_pemisah_berdasarkan_warna', ['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3'])->nullable();
            $table->enum('tangki_penyimpanan', ['1. Tidak ada', '2. Ada | ≤ 10', '3. Ada | > 10'])->nullable();
            $table->enum('mesin_pengemas', ['1. Tidak ada', '2. Ada | Semi Otomatis', '3. Ada | Full Otomatis'])->nullable();
            $table->enum('mesin_jahit', ['1. Tidak ada', '2. Ada | Semi Otomatis', '3. Ada | Full Otomatis'])->nullable();
            $table->enum('gudang_konvensional', ['1. Tidak ada', '2. Ada | < 3000', '3. Ada | > 3000'])->nullable();
            $table->enum('silo_gkg_hopper', ['1. Tidak ada', '2. Tidak Ada', '3. Ada | > 2000'])->nullable();
            $table->enum('truk', ['1. Tidak ada', '2. Ada | 1 s/d 5', '3. Ada | > 5'])->nullable();
            $table->enum('mini_lab', ['1. Tidak ada', '2. Ada | Tidak Khusus', '3. Ada | Ruang Khusus'])->nullable();
            $table->enum('moisture_tester', ['1. Tidak ada', '2. Ada | Berfungsi', '3. Ada | Lengkap | Berfungsi'])->nullable();
            $table->enum('pembanding_derajat_sosoh', ['1. Tidak ada', '2. Ada | Tidak Sesuai', '3. Ada | Sesuai Standar'])->nullable();
            $table->enum('bagian_quality_control', ['1. Tidak ada', '2. Ada | Merangkap', '3. Ada | Tidak Merangkap'])->nullable();
            $table->enum('hasil_klasifikasi', ['A', 'B', 'C'])->nullable();

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
