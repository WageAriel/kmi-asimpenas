<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_perusahaan',
        'jenis_komoditas',
        'jenis_komoditas_custom',
        'jenis_pengadaan',
        'agenda_no',
        'tanggal_terima',
        'paraf',
        'kontrak_yll',
        'created_by'
    ];

    protected $casts = [
        'tanggal_terima' => 'datetime'
    ];

    // Relationship dengan PurchaseOrderItem
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // Accessor untuk total harga
    public function getTotalHargaAttribute()
    {
        return $this->items->sum('harga');
    }

    // Accessor untuk total kuantum
    public function getTotalKuantumAttribute()
    {
        return $this->items->sum('kuantum');
    }

    // Accessor untuk total nilai
    public function getTotalNilaiAttribute()
    {
        return $this->items->sum('nilai');
    }

    // Accessor untuk mendapatkan jenis komoditas yang tepat
    public function getJenisKomoditasLengkapAttribute()
    {
        if ($this->jenis_komoditas === 'Lain-lain' && $this->jenis_komoditas_custom) {
            return $this->jenis_komoditas_custom;
        }
        return $this->jenis_komoditas;
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

    // Scope untuk filter berdasarkan bulan
    public function scopeByMonth($query, $month, $year)
    {
        return $query->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year);
    }

    // Static method untuk mendapatkan opsi jenis komoditas
    public static function getJenisKomoditasOptions()
    {
        return [
            'GKP',
            'GKG', 
            'Beras',
            'Jagung',
            'Gula Pasir',
            'Minyak Goreng',
            'Lain-lain'
        ];
    }

    // Static method untuk mendapatkan opsi komplek pergudangan
    public static function getKomplekPergudanganOptions()
    {
        return [
            'Karanganom', 'Krikilan', 'Ngabeyan', 'Banaran', 'Telukan', 'Triyagan', 'Gedong', 'Meger', 'Duyungan', 'Virtual', 'Custom'
        ];
    }

    // Static method untuk mendapatkan opsi kualitas berdasarkan komoditas
    public static function getKualitasOptions($jenisKomoditas)
    {
        switch ($jenisKomoditas) {
            case 'GKP':
                return ['Polos', 'Kemasan', 'Lain-lain'];
            case 'Beras':
                return ['Medium', 'Premium', 'Khusus', 'Lain-lain'];
            case 'GKG':
                return ['GKG', 'Lain-lain'];
            case 'Jagung':
                return ['Jagung', 'Lain-lain'];
            case 'Gula Pasir':
                return ['Gula Pasir', 'Lain-lain'];
            case 'Minyak Goreng':
                return ['Minyak Goreng', 'Lain-lain'];
            case 'Lain-lain':
                return ['Lain-lain'];
            default:
                return ['Lain-lain'];
        }
    }
}
