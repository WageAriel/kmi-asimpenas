<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('data_mitra', function (Blueprint $table) {
            $table->bigIncrements('id_mitra');
            $table->string('nama_perusahaan');
            $table->string('badan_hukum_usaha')->nullable();
            $table->string('alamat_perusahaan')->nullable();
            $table->string('kota_kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('nama_cp')->nullable();
            $table->string('nik', 30)->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('no_telp_perusahaan', 20)->nullable();
            $table->string('no_telp_cp', 20)->nullable();
            $table->string('bank_korespondensi')->nullable();
            $table->string('alamat_bank')->nullable();
            $table->string('no_rekening', 30)->nullable();
            $table->string('status_perusahaan')->nullable();
            $table->string('npwp', 30)->nullable();
            $table->enum('pkp', ['pkp', 'non pkp'])->nullable();
            $table->enum('surat_kuasa', ['ada', 'tidak ada'])->nullable();
            $table->date('tanggal_seleksi')->nullable();
            $table->date('tanggal_klasifikasi')->nullable();
            $table->date('tanggal_penilaian')->nullable();
            $table->date('tanggal_penetapan')->nullable();
            $table->date('tanggal_surat_permohonan')->nullable();
            $table->date('tanggal_pakta_integritas')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('no_vms', 50)->nullable();
            $table->string('kode_mitra', 50)->unique()->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('data_mitra');
    }
};