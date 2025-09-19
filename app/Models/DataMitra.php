<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataMitra extends Model
{
    protected $table = 'data_mitra';
    protected $primaryKey = 'id_mitra';

    protected $fillable = [
        'user_id',
        'nama_perusahaan',
        'badan_hukum_usaha',
        'alamat_perusahaan',
        'kota_kabupaten',
        'provinsi',
        'nama_cp',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'no_telp_perusahaan',
        'no_telp_cp',
        'bank_korespondensi',
        'alamat_bank',
        'no_rekening',
        'status_perusahaan',
        'npwp',
        'pkp',
        'surat_kuasa',
        'tanggal_seleksi',
        'tanggal_klasifikasi',
        'tanggal_penilaian',
        'tanggal_penetapan',
        'tanggal_surat_permohonan',
        'tanggal_pakta_integritas',
        'email',
        'no_vms',
        'kode_mitra',
    ];

    protected $dates = [
        'tanggal_lahir',
        'tanggal_seleksi',
        'tanggal_klasifikasi',
        'tanggal_penilaian',
        'tanggal_penetapan',
        'tanggal_surat_permohonan',
        'tanggal_pakta_integritas',
    ];

    // Relasi 1 ke banyak ke DataSeleksiMitra
    public function seleksiMitra()
    {
        return $this->hasMany(DataSeleksiMitra::class, 'id_mitra', 'id_mitra');
    }

    public function user()
{
    return $this->belongsTo(User::class, 'user_id', 'id');
}
}
