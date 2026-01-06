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
        Schema::table('purchase_order_items', function (Blueprint $table) {
            // Ubah kolom nilai dari bigInteger ke decimal untuk menghindari overflow
            // decimal(20, 2) bisa menyimpan angka hingga 999,999,999,999,999,999.99
            $table->decimal('nilai', 20, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            // Kembalikan ke bigInteger jika rollback
            $table->bigInteger('nilai')->change();
        });
    }
};
