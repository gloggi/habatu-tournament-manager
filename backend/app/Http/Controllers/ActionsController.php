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
        $referees = User::where('role', 'referee')->get();
        if ($referees->isEmpty()) {
            return response()->json(['message' => 'No referees found'], 404);
        }

        $refereeCount = $referees->count();
        $refereePointer = 0;

        $timeslots = Timeslot::with([
            'games' => function ($gameQuery) {
                $gameQuery->where('temporary', false)->where('played', false)
                    ->whereHas('teamA', function ($teamAQuery) {
                        $teamAQuery->where('dummy', false);
                    })
                    ->whereHas('teamB', function ($teamBQuery) {
                        $teamBQuery->where('dummy', false);
                    })
                    ->with(['teamA', 'teamB']);
            },
        ])->get();

        foreach ($timeslots as $timeslot) {
            $usedRefereeIds = [];

            foreach ($timeslot->games as $game) {
                $eligibleReferee = null;
                $shiftCount = 0;
                while ($shiftCount < $refereeCount) {
                    $currentReferee = $referees[($refereePointer + $shiftCount) % $refereeCount];
                    if (
                        ! in_array($currentReferee->id, $usedRefereeIds)
                        && $currentReferee->section_id != $game->teamA->section_id
                        && $currentReferee->section_id != $game->teamB->section_id
                    ) {
                        $eligibleReferee = $currentReferee;
                        break;
                    }
                    $shiftCount++;
                }

                if ($eligibleReferee) {
                    $game->referees()->attach($eligibleReferee->id);
                    $usedRefereeIds[] = $eligibleReferee->id;
                    $refereePointer = ($refereePointer + $shiftCount + 1) % $refereeCount;
                }
            }
        }

        return response()->json(['message' => 'Referees assigned successfully']);
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
