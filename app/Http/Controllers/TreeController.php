<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTreeRequest;
use App\Models\Tree;
use Illuminate\Http\Request;

class TreeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $request->user()->trees()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTreeRequest $request)
    {
        $data = $request->validated();

        $tree = $request->user()->trees()->create([
            'seed_type_id' => $data['seed_type_id'],
            'level' => 0,
            'health' => 100,
            'progress' => 0,
            'status' => Tree::ACTIVE,
            'planted_at' => now(),
            'next_decay_at' => now()->addHours(Tree::DECAY_INTERVAL_HOURS),
        ]);

        return response()->json($tree, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Tree $tree)
    {
        $this->authorize('view', $tree);

        return $tree;
    }
}
