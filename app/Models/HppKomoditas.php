<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HppKomoditas extends Model
{
    protected $fillable = [
        'hpp_document_id',
        'nama_komoditas',
        'tempat',
        'harga_per_kg',
        'ketentuan',
        'urutan'
    ];

    protected $casts = [
        'harga_per_kg' => 'decimal:2',
        'urutan' => 'integer'
    ];

    /**
     * Relasi ke dokumen HPP (many to one)
     */
    public function hppDocument()
    {
        return $this->belongsTo(HppDocument::class);
    }
}
