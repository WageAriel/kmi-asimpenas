<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'harga',
        'kuantum',
        'nilai',
        'komplek_pergudangan',
        'komplek_pergudangan_custom',
        'kualitas',
        'kualitas_custom'
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'kuantum' => 'decimal:2',
        'nilai' => 'decimal:2'
    ];

    // Relationship dengan PurchaseOrder
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    // Accessor untuk mendapatkan komplek pergudangan yang tepat
    public function getKomplekPergudanganLengkapAttribute()
    {
        if ($this->komplek_pergudangan === 'Custom' && $this->komplek_pergudangan_custom) {
            return $this->komplek_pergudangan_custom;
        }
        return $this->komplek_pergudangan;
    }

    // Accessor untuk mendapatkan kualitas yang tepat
    public function getKualitasLengkapAttribute()
    {
        if ($this->kualitas === 'Lain-lain' && $this->kualitas_custom) {
            return $this->kualitas_custom;
        }
        return $this->kualitas;
    }
}
