<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tree;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TreeCareController extends Controller
{
    public function __invoke(Tree $tree): JsonResponse
    {
        Gate::authorize('care', $tree);

    return DB::transaction(function () use ($tree) {
        $tree = Tree::with('seedType')
        ->lockForUpdate()
        ->findOrFail($tree->id);

        if (in_array($tree->status, [Tree::DEAD, Tree::MATURE], true)) {
            return response()->json([
                'error' => "El árbol está en estado {$tree->status} y no puede recibir más cuidados.",
                ], 409);
        }

        if ($tree->next_care_at?->isFuture()) {
            return response()->json([
                'error'        => 'El árbol se encuentra en período de enfriamiento.',
                'next_care_at' => $tree->next_care_at->utc()->toIso8601ZuluString(),
            ], 409);
        }

        $now = now();

        $tree->progress++;
        $tree->health = min(100, $tree->health + Tree::HEALTH_PER_CARE);

        if ($tree->progress >= $tree->seedType->cares_by_level) {
            $tree->level++;
            $tree->progress = 0;
        }

        if ($tree->level >= Tree::MAX_LEVEL) {
            $tree->level = Tree::MAX_LEVEL;
            $tree->status = Tree::MATURE;
        }

        $tree->last_cared_at = $now;
        $tree->next_care_at  = $now->copy()->addMinutes(Tree::COOLDOWN_MINUTES);
        $tree->save();

        $tree->cares()->create([
            'type'     => 'WATER',
            'cared_at' => $now,
        ]);

        return response()->json([
            'message' => 'Cuidado aplicado con éxito',
            'tree'    => [
            'id'           => $tree->id,
            'level'        => $tree->level,
            'health'       => $tree->health,
            'progress'     => $tree->progress,
            'status'       => $tree->status,
            'next_care_at' => $tree->next_care_at->utc()->toIso8601ZuluString(),
            ],
        ]);
    });
    }
}
