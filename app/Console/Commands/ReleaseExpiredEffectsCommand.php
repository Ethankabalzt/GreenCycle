<?php

namespace App\Console\Commands;

use App\Models\ActiveEffect;
use Illuminate\Console\Command;

class ReleaseExpiredEffectsCommand extends Command
{
    protected $signature = 'effects:release-expired';

    protected $description = 'Libera los efectos que ya cumplieron su duración';

    public function handle(): int
    {
        $released = ActiveEffect::query()
            ->where('active_flag', ActiveEffect::VIGENT)
            ->where('expires_at', '<=', now())
            ->update(['active_flag' => null]);

        $this->info("Efectos expirados liberados: {$released}.");

        return self::SUCCESS;
    }
}
