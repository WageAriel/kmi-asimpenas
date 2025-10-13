<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilSeleksiMitra extends Model
{
    protected $table = 'hasil_seleksi_mitra';
    protected $primaryKey = 'id_hasil_seleksi_mitra';

    protected $fillable = [
        'id_seleksi_mitra',
        'id_mitra',
        'surat_permohonan',
        'akta_notaris',
        'nib',
        'ktp',
        'no_rekening',
        'npwp',
        'surat_kuasa',
        'lantai_jemur',
        'sarana_lainnya',
        'mesin_memecah_kulit',
        'mesin_pemisah_gabah_dengan_beras',
        'mesin_penyosoh',
        'alat_pemisah_beras',
        'kesimpulan_dokumen',
        'dokumen_ada_valid',
        'dokumen_tidak_ada',
        'kesimpulan_sarana_pengeringan',
        'sarana_pengeringan_ada',
        'sarana_pengeringan_tidak_ada',
        'kesimpulan_sarana_penggilingan',
        'sarana_penggilingan_ada',
        'sarana_penggilingan_tidak_ada',
        'kesimpulan_akhir',
    ];

    // JSON casting untuk kolom array
    protected $casts = [
        'dokumen_ada_valid' => 'array',
        'dokumen_tidak_ada' => 'array',
        'sarana_pengeringan_ada' => 'array',
        'sarana_pengeringan_tidak_ada' => 'array',
        'sarana_penggilingan_ada' => 'array',
        'sarana_penggilingan_tidak_ada' => 'array',
    ];

    // Relasi ke DataMitra
    public function mitra()
    {
        return $this->belongsTo(DataMitra::class, 'id_mitra', 'id_mitra');
    }

    // Relasi ke DataSeleksiMitra
    public function seleksiMitra()
    {
        return $this->belongsTo(DataSeleksiMitra::class, 'id_seleksi_mitra', 'id_seleksi_mitra');
    }

    // Event untuk update status_seleksi di DataSeleksiMitra
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($hasilSeleksi) {
            if ($hasilSeleksi->id_seleksi_mitra && $hasilSeleksi->kesimpulan_akhir) {
                $seleksiMitra = DataSeleksiMitra::find($hasilSeleksi->id_seleksi_mitra);
                if ($seleksiMitra) {
                    $seleksiMitra->status_seleksi = strtolower($hasilSeleksi->kesimpulan_akhir);
                    $seleksiMitra->save();
                }
            }
        });
    }
}