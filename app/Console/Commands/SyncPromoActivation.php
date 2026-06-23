<?php

namespace App\Console\Commands;

use App\Services\PromoCodeService;
use Illuminate\Console\Command;

class SyncPromoActivation extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'promos:sync-activation';

    /**
     * The console command description.
     */
    protected $description = 'Sinkronisasi status aktif promo kode otomatis berdasarkan tanggal berlaku';

    /**
     * Execute the console command.
     */
    public function handle(PromoCodeService $promoCodeService): int
    {
        $result = $promoCodeService->syncAutoActivation();

        $this->info("Promo sync selesai: {$result['activated']} diaktifkan, {$result['deactivated']} dinonaktifkan.");

        return self::SUCCESS;
    }
}
