<?php

namespace Database\Seeders;

use App\Models\HppDocument;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // HPP Gabah dan Beras 2025
        $gabahBeras = HppDocument::create([
            'jenis_hpp' => 'Gabah dan Beras',
            'nomor_keputusan' => '14',
            'tahun' => 2025,
            'tentang' => 'Perubahan Atas Keputusan Kepala Badan Pangan Nasional Nomor 2 Tahun 2025 Tentang Perubahan Atas Harga Pembelian Pemerintah dan Rafraksi Harga Gabah dan Beras',
            'is_active' => true,
        ]);

        // Komoditas Gabah dan Beras
        $gabahBeras->komoditas()->createMany([
            [
                'nama_komoditas' => 'GKP',
                'tempat' => 'Tingkat Petani',
                'harga_per_kg' => 6500,
                'ketentuan' => null,
                'urutan' => 0,
            ],
            [
                'nama_komoditas' => 'Beras',
                'tempat' => 'Gudang Perum BULOG',
                'harga_per_kg' => 12000,
                'ketentuan' => 'Derajat Sosoh Min 95 %, Kadar Air maks 14 %, Butir Patah maks 25 % , Butir Menir maks 2 %',
                'urutan' => 1,
            ],
        ]);

        // HPP Jagung 2025
        $jagung = HppDocument::create([
            'jenis_hpp' => 'Jagung',
            'nomor_keputusan' => '216',
            'tahun' => 2025,
            'tentang' => 'Harga Pembelian Pemerintah Komoditas Jagung',
            'is_active' => true,
        ]);

        // Komoditas Jagung
        $jagung->komoditas()->createMany([
            [
                'nama_komoditas' => 'Jagung Pipil Kering',
                'tempat' => 'Tingkat Petani',
                'harga_per_kg' => 5500,
                'ketentuan' => 'Kadar Air 18-20%',
                'urutan' => 0,
            ],
            [
                'nama_komoditas' => 'Jagung Pipil Kering',
                'tempat' => 'Gudang Perum BULOG',
                'harga_per_kg' => 6400,
                'ketentuan' => 'Kadar Air maks 14 %, aflatoksin maks 50 ppb',
                'urutan' => 1,
            ],
        ]);

        $this->command->info('HPP data seeded successfully!');
    }
}

