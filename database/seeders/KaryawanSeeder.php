<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Karyawan;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama_karyawan' => 'Nanang Harianto',
                'jabatan' => 'Pemimpin',
            ],
            [
                'nama_karyawan' => 'Dicki Yusfarino',
                'jabatan' => 'Wakil Pemimpin',
            ],
            [
                'nama_karyawan' => 'M. Betha Endi Nurrakhman',
                'jabatan' => 'Ketua',
            ],
            [
                'nama_karyawan' => 'Yuki Rezkyana Putri',
                'jabatan' => 'Anggota',
            ],
            [
                'nama_karyawan' => 'Ridlo Banu Sastiko',
                'jabatan' => 'Anggota',
            ],
        ];

        foreach ($data as $karyawan) {
            Karyawan::create($karyawan);
        }
    }
}
