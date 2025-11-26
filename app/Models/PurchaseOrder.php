<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_perusahaan',
        'jenis_komoditas',
        'jenis_komoditas_custom',
        'jenis_pengadaan',
        'no_surat',
        'agenda_no',
        'tanggal_terima',
        'paraf',
        'kontrak_yll',
        'created_by'
    ];

    protected $casts = [
        'tanggal_terima' => 'datetime'
    ];

    // Relationship dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship dengan DataMitra
    public function mitra()
    {
        return $this->belongsTo(DataMitra::class, 'nama_perusahaan', 'nama_perusahaan');
    }

    // Relationship dengan PurchaseOrderItem
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // Accessor untuk total harga
    public function getTotalHargaAttribute()
    {
        if (!$this->relationLoaded('items')) {
            $this->load('items');
        }
        return (int) $this->items->sum('harga') ?? 0;
    }

    // Accessor untuk total kuantum
    public function getTotalKuantumAttribute()
    {
        if (!$this->relationLoaded('items')) {
            $this->load('items');
        }
        return (int) $this->items->sum('kuantum') ?? 0;
    }

    // Accessor untuk total nilai
    public function getTotalNilaiAttribute()
    {
        if (!$this->relationLoaded('items')) {
            $this->load('items');
        }
        return (int) $this->items->sum('nilai') ?? 0;
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

    // Method untuk generate nomor surat otomatis
    // Format: NO_VMS/XXX/KODE_MITRA/MM/YYYY
    // NO_VMS = no_vms dari data_mitra (sesuai yang ada di database)
    // XXX = urutan nomor (reset setiap bulan, 3 digit)
    // KODE_MITRA = kode_mitra dari data_mitra (sesuai yang ada di database)
    // MM = bulan (2 digit)
    // YYYY = tahun (4 digit)
    public function generateNoSurat()
    {
        $now = now();
        $month = $now->month;
        $year = $now->year;
        
        // Cari data mitra berdasarkan nama perusahaan
        $mitra = DataMitra::where('nama_perusahaan', $this->nama_perusahaan)->first();
        
        if (!$mitra) {
            // Jika mitra tidak ditemukan, gunakan default
            $noVms = '000000';
            $kodeMitra = 'XXX';
        } else {
            // Gunakan data langsung dari mitra, jika kosong baru pakai default
            $noVms = !empty($mitra->no_vms) ? $mitra->no_vms : '000000';
            $kodeMitra = !empty($mitra->kode_mitra) ? $mitra->kode_mitra : 'XXX';
        }
        
        // Hitung urutan PO bulan ini untuk mitra tertentu (reset setiap bulan)
        $count = self::where('nama_perusahaan', $this->nama_perusahaan)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->count() + 1;
        
        // Format nomor surat: NO_VMS/XXX/KODE_MITRA/MM/YYYY
        $noSurat = sprintf(
            "%s/%03d/%s/%02d/%s",
            $noVms,
            $count,
            strtoupper($kodeMitra),
            $month,
            $year
        );
        
        return $noSurat;
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
            'Karanganom', 
            'Krikilan', 
            'Ngabeyan', 
            'Banaran', 
            'Telukan', 
            'Triyagan', 
            'Gedong', 
            'Meger', 
            'Duyungan', 
            'Virtual', 
            'Custom'
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
