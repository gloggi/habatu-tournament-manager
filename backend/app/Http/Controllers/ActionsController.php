<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Timeslot;
use App\Models\User;
use Illuminate\Http\Request;

class ActionsController extends Controller
{
    public function assignReferees(Request $request)
    {
        $refereeQueue = User::where('role', 'referee')->get();

        $refereePointer = 0;

        $timeslots = Timeslot::all();
        foreach ($timeslots as $timeslot) {
            $games = Game::where('timeslot_id', $timeslot->id)
                ->where('temporary', false)
                ->with('teamA', 'teamB')->get();
            foreach ($games as $game) {
                $referee = $refereeQueue[$refereePointer % $refereeQueue->count()];
                $shiftCount = 1;
                while ($shiftCount < $refereeQueue->count() && ($game->teamA->section_id == $referee->section_id || $game->teamB->section_id == $referee->section_id)) {
                    $referee = $refereeQueue[($refereePointer + $shiftCount) % $refereeQueue->count()];
                    $shiftCount++;
                }
                if ($shiftCount == 1) {
                    $refereePointer++;
                }
                $game->referees()->attach($referee->id);
                $game->save();
            }
        }

    }

    public function clearReferees(Request $request)
    {
        $games = Game::all();
        foreach ($games as $game) {
            $game->referees()->detach();
            $game->save();
        }
    }
}
