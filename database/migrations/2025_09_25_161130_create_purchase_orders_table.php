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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User relationship dari awal
            $table->string('nama_perusahaan');
            $table->string('jenis_komoditas');
            $table->string('jenis_komoditas_custom')->nullable(); // untuk input manual
            $table->enum('jenis_pengadaan', ['PSO', 'Komersial']);
            $table->string('no_surat')->nullable(); // Nomor surat auto-generated
            // Field harga, kuantum, nilai, komplek_pergudangan, kualitas dipindah ke items table
            $table->string('agenda_no')->nullable();
            $table->date('tanggal_terima')->nullable();
            $table->text('paraf')->nullable();
            $table->enum('kontrak_yll', ['REALISASI S/D', 'DISETUJUI/TIDAK'])->nullable();
            $table->string('created_by')->nullable(); // untuk tracking siapa yang membuat
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
