<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HppDocument extends Model
{
    protected $fillable = [
        'jenis_hpp',
        'nomor_keputusan',
        'tahun',
        'tentang',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tahun' => 'integer'
    ];

    /**
     * Relasi ke komoditas (one to many)
     */
    public function komoditas()
    {
        return $this->hasMany(HppKomoditas::class)->orderBy('urutan');
    }

    /**
     * Scope untuk mendapatkan dokumen aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope berdasarkan jenis HPP
     */
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_hpp', $jenis);
    }
}
