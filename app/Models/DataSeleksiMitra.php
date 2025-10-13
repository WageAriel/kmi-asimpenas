<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSeleksiMitra extends Model
{
    protected $table = 'data_seleksi_mitra';
    protected $primaryKey = 'id_seleksi_mitra';

    protected $fillable = [
        'id_mitra',
        'surat_permohonan',
        'mb_surat_permohonan',
        'akta_notaris',
        'mb_akta_notaris',
        'nib',
        'mb_nib',
        'ktp',
        'mb_ktp',
        'no_rekening',
        'mb_no_rekening',
        'npwp',
        'mb_npwp',
        'surat_kuasa',
        'mb_surat_kuasa',
        'lantai_jemur',
        'sarana_lainnya',
        'mesin_memecah_kulit',
        'mesin_pemisah_gabah',
        'mesin_penyosoh',
        'alat_pemisah_beras',
        'status_seleksi',
    ];

    protected $dates = [
        'mb_surat_permohonan',
        'mb_akta_notaris',
        'mb_nib',
        'mb_ktp',
        'mb_no_rekening',
        'mb_npwp',
        'mb_surat_kuasa',
    ];

    // Relasi ke DataMitra
    public function mitra()
    {
        return $this->belongsTo(DataMitra::class, 'id_mitra', 'id_mitra');
    }

    // Relasi ke HasilSeleksiMitra
    public function hasilSeleksi()
    {
        return $this->hasOne(HasilSeleksiMitra::class, 'id_seleksi_mitra', 'id_seleksi_mitra');
    }
}