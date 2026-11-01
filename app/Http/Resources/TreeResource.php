<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TreeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'level'         => $this->level,
            'health'        => $this->health,
            'progress'      => $this->progress,
            'status'        => $this->status,
            'seed_type'     => new SeedTypeResource($this->whenLoaded('seedType')),
            'planted_at'    => $this->planted_at?->utc()->toIso8601ZuluString(),
            'last_cared_at' => $this->last_cared_at?->utc()->format('Y-m-d H:i:s'),
            'next_care_at'  => $this->next_care_at?->utc()->toDateString(),
            'harvested_at'  => $this->harvested_at?->utc()->format('Y-m-d H:i:s'),
        ];
    }
}