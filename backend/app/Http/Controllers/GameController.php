<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Services\FinaleService;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $games = Game::with(['teamA', 'teamB', 'hall', 'timeslot', 'category', 'referees'])
            ->whereNull('finale_type')
            ->where('temporary', false)
            ->get();

        return response()->json($games);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_a_id' => 'required|exists:teams,id',
            'team_b_id' => 'required|exists:teams,id',
            'hall_id' => 'required|exists:halls,id',
            'timeslot_id' => 'required|exists:timeslots,id',
            'category_id' => 'required|exists:categories,id',
            'played' => 'sometimes|boolean',
        ]);

        $game = Game::create($validated);

        return response()->json($game, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $game = Game::with(['teamA', 'teamA.section', 'teamB', 'teamB.section', 'hall', 'timeslot', 'category', 'referees'])->findOrFail($id);

        return response()->json($game);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'team_a_id' => 'sometimes|exists:teams,id',
            'team_b_id' => 'sometimes|exists:teams,id',
            'points_team_a' => 'sometimes|integer',
            'points_team_b' => 'sometimes|integer',
            'hall_id' => 'sometimes|exists:halls,id',
            'timeslot_id' => 'sometimes|exists:timeslots,id',
            'category_id' => 'sometimes|exists:categories,id',
            'referees' => 'sometimes|array',
            'played' => 'sometimes|boolean',
        ]);
        if (isset($validated['points_team_a']) && isset($validated['points_team_b']) && ($validated['points_team_a'] > 0 || $validated['points_team_b'] > 0)) {
            $validated['played'] = true;
        }

        $referees = $validated['referees'] ?? null;
        // Remove referees from the validated array
        unset($validated['referees']);

        $game = Game::findOrFail($id);
        $game->update($validated);

        // check if referee is array
        if ($referees !== null) {
            $referee_ids = array_column($referees, 'id');
            $game->referees()->sync($referee_ids);
        }

        $newGame = Game::with(['teamA', 'teamB', 'hall', 'timeslot', 'category', 'referees'])->findOrFail($id);
        $finaleService = new FinaleService;
        $finaleService->assignFinaleTeams();

        return response()->json($newGame);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $game = Game::findOrFail($id);
        $game->delete();

        return response()->json(['message' => 'Game deleted successfully']);
    }
}
