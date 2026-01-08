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
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom is_active (default true untuk user baru)
            $table->boolean('is_active')->default(true)->after('role');
            
            // Hapus unique constraint dari kolom email
            $table->dropUnique(['email']);
            
            // Buat composite unique index untuk email + is_active
            // Ini memungkinkan email yang sama jika salah satunya is_active = false
            $table->index(['email', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus index composite
            $table->dropIndex(['email', 'is_active']);
            
            // Hapus kolom is_active
            $table->dropColumn('is_active');
            
            // Kembalikan unique constraint pada email
            $table->unique('email');
        });
    }
};
