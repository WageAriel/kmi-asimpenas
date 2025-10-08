<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KlasifikasiMitra extends Model
{
    protected $table = 'data_klasifikasi_mitra';
    protected $primaryKey = 'id_klasifikasi_mitra';

    protected $fillable = [
        'id_mitra',
        'mesin_pembersih_gabah',
        'lantai_jemur',
        'mesin_pengering',
        'alat_pengering_lainnya',
        'mesin_pembersih_awal',
        'mesin_pemecah_kulit',
        'mesin_pembersih_sekam',
        'mesin_pemisah_gabah_pecah_kulit',
        'mesin_pemisah_batu',
        'mesin_penyosoh',
        'mesin_pengkabut',
        'mesin_pemesah_menir',
        'mesin_pemisah_katul',
        'mesin_pemisah_berdasarkan_ukuran',
        'mesin_pemisah_berdasarkan_warna',
        'tangki_penyimpanan',
        'mesin_pengemas',
        'mesin_jahit',
        'gudang_konvensional',
        'silo_gkg_hopper',
        'truk',
        'mini_lab',
        'moisture_tester',
        'pembanding_derajat_sosoh',
        'bagian_quality_control',
        'hasil_klasifikasi'
    ];

    // Relasi ke DataMitra (banyak ke satu)
    public function mitra()
    {
        return $this->belongsTo(DataMitra::class, 'id_mitra', 'id_mitra');
    }
}
