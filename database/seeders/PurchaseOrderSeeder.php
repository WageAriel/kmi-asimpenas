<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\User;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua user dengan role mitra
        $users = User::where('role', 'mitra')->get();

        foreach ($users as $user) {
            // Buat 3-5 Purchase Order untuk setiap user
            $poCount = rand(3, 5);
            
            for ($i = 1; $i <= $poCount; $i++) {
                $po = PurchaseOrder::create([
                    'user_id' => $user->id,
                    'nama_perusahaan' => $user->name,
                    'jenis_komoditas' => ['GKP', 'Beras', 'GKG', 'Jagung'][rand(0, 3)],
                    'jenis_pengadaan' => ['PSO', 'Komersial'][rand(0, 1)],
                    'created_by' => $user->name,
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);

                // Buat 1-3 items untuk setiap PO
                $itemCount = rand(1, 3);
                
                for ($j = 1; $j <= $itemCount; $j++) {
                    $harga = rand(8000, 15000);
                    $kuantum = rand(1000, 10000);
                    
                    PurchaseOrderItem::create([
                        'purchase_order_id' => $po->id,
                        'harga' => $harga,
                        'kuantum' => $kuantum,
                        'nilai' => $harga * $kuantum,
                        'komplek_pergudangan' => ['Karanganom', 'Krikilan', 'Ngabeyan', 'Banaran'][rand(0, 3)],
                        'kualitas' => match($po->jenis_komoditas) {
                            'GKP' => ['Polos', 'Kemasan'][rand(0, 1)],
                            'Beras' => ['Medium', 'Premium', 'Khusus'][rand(0, 2)],
                            'GKG' => 'GKG',
                            'Jagung' => 'Jagung',
                            default => 'Standard'
                        },
                    ]);
                }
            }
        }
    }
}
