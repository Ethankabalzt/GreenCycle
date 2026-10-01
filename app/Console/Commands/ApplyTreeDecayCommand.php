<?php

namespace App\Console\Commands;

use App\Models\Tree;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ApplyTreeDecayCommand extends Command
{
    protected $signature = 'trees:apply-decay';

    protected $description = 'Aplica el deterioro a los árboles cuyo intervalo de daño ya venció';

    public function handle(): int
    {
        $now = now();

        $decayed = 0;

        Tree::query()
            ->where('status', Tree::ACTIVE)
            ->where(fn ($query) => $query
                ->whereNull('next_decay_at')
                ->orWhere('next_decay_at', '<=', $now))
            ->chunkById(100, function ($trees) use ($now, &$decayed) {
                foreach ($trees as $tree) {
                    $decayed += (int) $this->applyDecay($tree, $now);
                }
            });

        $this->info("Deterioro aplicado a {$decayed} árbol(es).");

        return self::SUCCESS;
    }

    private function applyDecay(Tree $tree, Carbon $now): bool
    {
        $lastProcessedAt = $tree->last_decay_at ?? $tree->planted_at;

        if ($lastProcessedAt === null) {
            return false;
        }

        $elapsedHours = (int) $lastProcessedAt->diffInHours($now, absolute: false);
        $intervals = intdiv($elapsedHours, Tree::DECAY_INTERVAL_HOURS);

        if ($intervals < 1) {
            return false;
        }

        $checkpoint = $lastProcessedAt->copy()->addHours($intervals * Tree::DECAY_INTERVAL_HOURS);
        $health = max(0, $tree->health - ($intervals * Tree::DECAY_DAMAGE));

        $tree->forceFill([
            'health' => $health,
            'last_decay_at' => $checkpoint,
            'next_decay_at' => $checkpoint->copy()->addHours(Tree::DECAY_INTERVAL_HOURS),
            'status' => $health === 0 ? Tree::DEAD : $tree->status,
        ])->save();

        return true;
    }
}
