<?php

namespace App\Http\Controllers;

use App\Models\Tree;
use Illuminate\Http\Request;

class TreeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $trees = $request->user()
            ->trees()
            ->with('seedType')
            ->get();

        return response()->json($trees, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'seed_type_id' => 'required|exists:seed_types,id',
        ]);

        $tree = $request->user()->trees()->create([
            'seed_type_id' => $fields['seed_type_id'],
            'level' => 1,
            'health' => 100,
            'progress' => 0,
            'status' => 'Active',
            'planted_at' => now(),
        ]);

        $tree->load('seedType');

        return response()->json($tree, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Tree $tree)
    {
        if ($tree->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $tree->load('seedType');

        return response()->json($tree, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
