<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = Section::all();

        return response()->json($sections);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $section = Section::create($validated);

        return response()->json($section);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $section = Section::findOrFail($id);

        return response()->json($section);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $section = Section::findOrFail($id);
        $section->update($validated);

        return response()->json($section);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $section = Section::findOrFail($id);
        $section->delete();

        return response()->json(null, 204);
    }
}
