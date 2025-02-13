<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hall;

class HallController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $halls = Hall::all();
        return response()->json($halls);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $hall = Hall::create($validated);
        return response()->json($hall);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hall = Hall::findOrFail($id);
        return response()->json($hall);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $hall = Hall::findOrFail($id);
        $hall->update($validated);
        return response()->json($hall);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $hall = Hall::findOrFail($id);
        $hall->delete();
        return response()->json(null, 204);
    }
}
