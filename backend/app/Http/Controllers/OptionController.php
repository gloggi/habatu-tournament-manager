<?php

namespace App\Http\Controllers;

use App\Models\Option;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $options = Option::first();

        return response()->json($options);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tournament_name' => 'required|string|max:255',
            'start_time' => 'required|string|max:255',
            'game_duration' => 'required|string|max:255',
            'break_duration' => 'required|string|max:255',
            'additional_slots' => 'required|integer',
            'started_tournament' => 'required|boolean',
            'ended_round_games' => 'required|boolean',
        ]);
        $option = Option::updateOrCreate(['id' => 1], $validated);

        return response()->json($option);
    }
}
