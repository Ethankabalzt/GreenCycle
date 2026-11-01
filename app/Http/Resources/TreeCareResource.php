<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TreeCareResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'level'        => $this->level,
            'health'       => $this->health,
            'progress'     => $this->progress,
            'status'       => $this->status,
            'next_care_at' => $this->next_care_at?->toDateString(),
        ];
    }
}
