<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\V1\StoreTreeRequest;
use App\Http\Resources\TreeResource;
use App\Models\Tree;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class TreeController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $trees = $request->user()->trees()->with('seedType')->latest()->get();

        return TreeResource::collection($trees);
    }

    public function show(Tree $tree): TreeResource
    {
        Gate::authorize('view', $tree);

        return new TreeResource($tree->load('seedType'));
    }

    public function store(StoreTreeRequest $request): TreeResource
    {
        $tree = $request->user()->trees()->create([
            'seed_type_id' => $request->validated('seed_type_id'),
            'planted_at'   => now(),
        ]);

        // Un resource de un modelo recién creado responde 201 automáticamente
        return new TreeResource($tree->load('seedType'));
    }
}