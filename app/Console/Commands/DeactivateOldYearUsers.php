<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class DeactivateOldYearUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:deactivate-old-year';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menonaktifkan user yang dibuat pada tahun sebelumnya (untuk registrasi ulang tahunan)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentYear = Carbon::now()->year;
        
        $this->info("Memproses penonaktifan user MITRA dari tahun sebelum {$currentYear}...");
        
        // Ambil user MITRA yang masih aktif dan dibuat sebelum tahun ini
        // Admin dan Super Admin TIDAK dinonaktifkan
        $oldUsers = User::where('is_active', true)
            ->where('role', 'mitra') // Hanya mitra yang dinonaktifkan
            ->whereYear('created_at', '<', $currentYear)
            ->get();
        
        if ($oldUsers->isEmpty()) {
            $this->info('Tidak ada user MITRA yang perlu dinonaktifkan.');
            return Command::SUCCESS;
        }
        
        $count = $oldUsers->count();
        $this->info("Ditemukan {$count} user MITRA yang akan dinonaktifkan.");
        $this->info("Note: User dengan role Admin dan Super Admin tidak akan dinonaktifkan.");
        $this->newLine();
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        $deactivatedCount = 0;
        
        foreach ($oldUsers as $user) {
            try {
                $user->is_active = false;
                $user->save();
                $deactivatedCount++;
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\nGagal menonaktifkan user ID {$user->id}: " . $e->getMessage());
            }
        }
        
        $bar->finish();
        
        $this->newLine(2);
        $this->info("✓ Berhasil menonaktifkan {$deactivatedCount} user MITRA dari tahun sebelumnya.");
        $this->info("User-user tersebut dapat mendaftar ulang dengan email yang sama.");
        $this->info("User dengan role Admin dan Super Admin tetap aktif.");
        
        return Command::SUCCESS;
    }
}
