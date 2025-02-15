<?php

namespace App\Http\Controllers;

use App\Models\Timeslot;
use Illuminate\Http\Request;

class TimeslotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $timeslots = Timeslot::all();

        return response()->json($timeslots);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_time' => 'required|string|max:255',
            'end_time' => 'required|string|max:255',
        ]);
        $timeslot = Timeslot::create($validated);

        return response()->json($timeslot);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $timeslot = Timeslot::findOrFail($id);

        return response()->json($timeslot);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'start_time' => 'required|string|max:255',
            'end_time' => 'required|string|max:255',
        ]);
        $timeslot = Timeslot::findOrFail($id);
        $timeslot->update($validated);

        return response()->json($timeslot);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $timeslot = Timeslot::findOrFail($id);
        $timeslot->delete();

        return response()->json(null, 204);
    }
}
