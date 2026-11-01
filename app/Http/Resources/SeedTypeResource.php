<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeedTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'cares_by_level' => $this->cares_by_level,
            'harvest_coins'  => $this->harvest_coins,
            'description'    => $this->description,
        ];
    }
}