<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Game;
use App\Models\Group;
use App\Models\Hall;
use App\Models\Option;
use App\Models\Team;
use App\Models\Timeslot;
use App\Models\User;
use App\Services\FinaleService;
use App\Services\TournamentService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    private function validateAndUpdateTorunamentSpecs(Request $request)
    {
        $validated = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'game_duration' => 'required|integer|min:1',
            'break_duration' => 'required|integer|min:0',
            'round_robin' => 'required|boolean',
            'group_phase' => 'required|boolean',
            'groups_per_category' => 'required_if:group_phase,true|array',
            'play_for_third_place' => 'required|boolean',
        ]);
        Option::first()->update(
            [
                'start_time' => Carbon::createFromFormat('H:i', $validated['start_time']),
                'game_duration' => $validated['game_duration'],
                'break_duration' => $validated['break_duration'],
                'round_robin' => $validated['round_robin'],
                'group_phase' => $validated['group_phase'],
                'play_for_third_place' => $validated['play_for_third_place'],
            ]
        );

        foreach ($validated['groups_per_category'] as $category_id => $subgroup_count) {
            $category = Category::find($category_id);
            if (($subgroup_count & ($subgroup_count - 1)) != 0) {
                break;
            }

            $category->subgroups = max(1, $subgroup_count);
            $category->save();
        }

    }

    public function calculateTournamentInfos(Request $request)
    {
        $this->validateAndUpdateTorunamentSpecs($request);

        $tournamentService = new TournamentService(temporary: true);
        $tournamentInfos = $tournamentService->createTournament();

        return response()->json($tournamentInfos);

    }

    public function createTorunament(Request $request)
    {
        $this->validateAndUpdateTorunamentSpecs($request);

        $tournamentService = new TournamentService;
        $tournamentService->createTournament();

        return response()->json([
            'message' => 'Tournament created successfully',
        ]);
    }

    public function getSpecs()
    {
        $options = Option::first();
        $categories = Category::all();

        $groupsPerCategory = $categories->mapWithKeys(function ($category) {
            return [$category->id => $category->subgroups];
        });

        $specs = [
            'start_time' => $options->start_time->format('H:i'),
            'game_duration' => $options->game_duration,
            'break_duration' => $options->break_duration,
            'round_robin' => $options->round_robin,
            'group_phase' => $options->group_phase,
            'groups_per_category' => $groupsPerCategory,
            'play_for_third_place' => $options->play_for_third_place,
        ];

        return response()->json($specs);
    }

    private function getTable($userId = null, $teamId = null)
    {

        // Fetch all halls and timeslots
        $halls = Hall::all();
        $timeslots = Timeslot::where('temporary', false)->get();

        // Initialize groupedGames with empty arrays for all hall-timeslot combinations
        $groupedGames = [];
        foreach ($timeslots as $timeslot) {
            $formattedTimeslot = $timeslot->start_time->format('H:i').' - '.$timeslot->end_time->format('H:i');
            $groupedGames[$formattedTimeslot] = [];

            foreach ($halls as $hall) {
                $groupedGames[$formattedTimeslot][$hall->name] = ['games' => [], 'slot_info' => ['hall_id' => $hall->id, 'hall_name' => $hall->name, 'timeslot_id' => $timeslot->id, 'has_games' => false]];
            }
        }

        // Fetch games and populate the array
        $query = Game::with('teamA', 'teamB', 'hall', 'timeslot', 'category')->where('temporary', false);

        if ($userId) {
            $query->whereHas('referees', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });
        }
        if ($teamId) {
            $query->where(function ($query) use ($teamId) {
                $query->where('team_a_id', $teamId)
                    ->orWhere('team_b_id', $teamId);
            });
        }

        $games = $query->get();
        foreach ($games as $game) {
            $timeslot = $game->timeslot->start_time->format('H:i').' - '.$game->timeslot->end_time->format('H:i');
            $hall = $game->hall->name;

            $groupedGames[$timeslot][$hall]['games'][] = $game;
            $groupedGames[$timeslot][$hall]['slot_info']['has_games'] = true;
        }

        return $groupedGames;
    }

    public function getNormalTable(Request $request)
    {

        $groupedGames = $this->getTable();

        return response()->json($groupedGames);

    }

    public function getRefereeTable(Request $request)
    {
        $userId = $request->user('api')->id;
        $groupedGames = $this->getTable($userId);

        return response()->json($groupedGames);

    }

    public function getTeamTable(Request $request)
    {
        $userId = $request->user('api')->id;
        $userTeamId = User::find($userId)->team_id;
        $groupedGames = $this->getTable(null, $userTeamId);

        return response()->json($groupedGames);

    }

    public function getRanking()
    {
        $finaleService = new FinaleService;

        $categories = Category::all();
        $ranking = [];
        foreach ($categories as $category) {
            $group = Group::where('category_id', $category->id)->where('temporary', false)->get();
            $currentRanking = ['category_name' => $category->name, 'groups' => []];
            if (count($group) > 0) {
                foreach ($group as $subgroup) {
                    $teams = Team::with('section')->where('group_id', $subgroup->id)->where('dummy', false)->get();
                    $currentGroupRanking = $finaleService->rankTeams($teams);
                    $currentRanking['groups'][] = ['group_name' => $subgroup->name, 'ranking' => $currentGroupRanking];
                }
            } else {
                $teams = Team::with('section')->where('category_id', $category->id)->where('dummy', false)->get();
                $currentGroupRanking = $finaleService->rankTeams($teams);
                $currentRanking['groups'][] = ['group_name' => $category->name, 'ranking' => $currentGroupRanking];
            }
            $ranking[] = $currentRanking;

        }

        return response()->json($ranking);
    }

    public function addNewTimeslot(Request $request)
    {

        $tournamentService = new TournamentService;
        $newSlot = $tournamentService->getTimeAndHallSlot(newSlot: true);

        return response()->json($newSlot);
    }
}
