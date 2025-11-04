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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->bigInteger('harga'); // Harga per kg dalam rupiah (integer)
            $table->decimal('kuantum', 15, 2); // Kuantum dalam kg (bisa desimal)
            $table->bigInteger('nilai'); // Nilai total dalam rupiah (integer)
            $table->string('komplek_pergudangan');
            $table->string('komplek_pergudangan_custom')->nullable();
            $table->string('kualitas');
            $table->string('kualitas_custom')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
