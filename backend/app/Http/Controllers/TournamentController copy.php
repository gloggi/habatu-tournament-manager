<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Team;
use App\Models\Game;
use App\Models\Option;
use App\Models\Hall;
use App\Models\Timeslot;
use App\Models\User;
use App\Models\Group;

class TournamentControlleCopy extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function createGames(Request $request)
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
        Game::truncate();
        Timeslot::truncate();
        Group::truncate();
        try {
            [$gameList, $timeslots, $allSubGroups, $allChangedTeams] = $this->scheduleAllGames($validated['round_robin'], $validated['group_phase'], $validated['groups_per_category']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        foreach ($timeslots as $timeslot) {
            $timeslot->save();
        }
        foreach ($allSubGroups as $subGroup) {
            $subGroup->save();
        }
        foreach ($allChangedTeams as $changedTeam) {
            $changedTeam->group_id = $changedTeam->group->id;
            unset($changedTeam->group);
            $changedTeam->save();
        }
        foreach ($gameList as $game) {
            $game->timeslot_id = $game->timeslot->id;
            unset($game->timeslot);
            $game->save();
        }
        $allGames = Game::with('teamA', 'teamB', 'hall', 'timeslot')->get();

        return response()->json($allGames);
    }
    public function testScheduleAllGames()
    {
        [$gameList, $timeslots] = $this->scheduleAllGames();
        foreach ($timeslots as $timeslot) {
            $timeslot->save();
        }
        return response()->json($gameList);
    }

    private function scheduleAllGames($roundRobin = true, $groupPhase = false, $groupsPerCategory = [])
    {
        // structure groupsPerCategory: [ category_id => subgroup_count, ... ]
        $categories = Category::all();
        $halls = Hall::all();
        $gamesByCategory = [];
        $options = Option::first();
        $category_pointer = 0;

        $gameList = [];
        $timeslots = [];
        $allSubGroups = [];
        $allChangedTeams = [];

        // Prepare the games by category (using round-robin or group-phase)
        foreach ($categories as $category) {
            $teams = Team::where('category_id', $category->id)->get();

            // If groupPhase is true AND this category has an entry in $groupsPerCategory
            // then schedule group-phase. Otherwise, schedule normal round-robin.
            if ($groupPhase && isset($groupsPerCategory[$category->id])) {
                $subgroupCount = $groupsPerCategory[$category->id];
                [$gamesByCategory[$category->id], $subGroups, $changedTeams] = $this->createGamesByGroupPhase($category, $teams, $subgroupCount);
                $allSubGroups = array_merge($allSubGroups, $subGroups);
                $allChangedTeams = array_merge($allChangedTeams, $changedTeams);

            } else {
                // Fallback to round-robin scheduling
                if ($roundRobin) {
                    $gamesByCategory[$category->id] = $this->createGamesByCategory($category, $teams);
                } else {
                    // If you ever needed a scenario with no group-phase and no round-robin,
                    // you'd handle it here, but presumably you'll always do one or the other.
                    $gamesByCategory[$category->id] = [];
                }
            }
        }

        // Initialize the first Timeslot
        $timeslot = new Timeslot([
            'start_time' => $options->start_time->copy(),
            'end_time' => $options->start_time->copy()->addMinutes($options->game_duration),
        ]);
        $timeslots[] = $timeslot;

        // Schedule all games across halls / timeslots
        $all_games_scheduled = false;
        while (!$all_games_scheduled) {
            for ($i = 0; $i < $halls->count(); $i++) {

                // Move to the correct category
                $current_category_id = $categories[$category_pointer]->id;

                // If no games left for this category, skip to next
                if (count($gamesByCategory[$current_category_id]) === 0) {
                    $category_pointer = ($category_pointer + 1) % $categories->count();
                    continue;
                }

                // Take the first unscheduled game in this category
                $current_game = $gamesByCategory[$current_category_id][0];
                $current_game->hall_id = $halls[$i]->id;
                $current_game->timeslot = $timeslot;
                $current_game->points_team_a = 0;
                $current_game->points_team_b = 0;
                $gameList[] = $current_game;

                // Remove it from the pending list
                array_shift($gamesByCategory[$current_category_id]);

                // After filling up the last hall, move to the next timeslot
                if ($i == $halls->count() - 1) {
                    $timeslot = new Timeslot([
                        'start_time' => $timeslot->end_time->copy()->addMinutes($options->break_duration),
                        'end_time' => $timeslot->end_time->copy()
                            ->addMinutes($options->break_duration)
                            ->addMinutes($options->game_duration),
                    ]);
                    $timeslots[] = $timeslot;
                }

                // Check if there are still games left at all
                $category_pointer = ($category_pointer + 1) % $categories->count();
                foreach ($gamesByCategory as $catId => $games) {
                    if (count($games) > 0) {
                        $all_games_scheduled = false;
                        break;
                    }
                    $all_games_scheduled = true;
                }

                // If no games remain, break completely
                if ($all_games_scheduled) {
                    break;
                }
            }
        }

        // Return the same output structure
        return [$gameList, $timeslots, $allSubGroups, $allChangedTeams];
    }

    /**
     * Create round-robin games for one category, all teams.
     */
    private function createGamesByCategory($category, $teams)
    {
        $teams = $teams->toArray();
        $teamCount = count($teams);

        // Can't schedule if < 2 teams
        if ($teamCount < 2) {
            return [];
        }

        // Make sure we have even number of teams (add null "bye" if odd)
        if ($teamCount % 2 !== 0) {
            $teams[] = null;
            $teamCount++;
        }

        $games = [];
        // Standard round-robin scheduling
        for ($round = 0; $round < $teamCount - 1; $round++) {
            for ($i = 0; $i < $teamCount / 2; $i++) {
                $teamA = $teams[$i];
                $teamB = $teams[$teamCount - 1 - $i];

                if ($teamA !== null && $teamB !== null) {
                    $games[] = new Game([
                        'category_id' => $category->id,
                        'team_a_id' => $teamA['id'],
                        'team_b_id' => $teamB['id'],
                    ]);
                }
            }
            // Rotate teams for next round
            $lastTeam = array_pop($teams);
            array_splice($teams, 1, 0, [$lastTeam]);
        }

        return $games;
    }

    /**
     * Create group-phase games for one category by splitting into subgroups,
     * then doing a round-robin within each subgroup.
     */
    private function createGamesByGroupPhase($category, $teams, $subgroupCount)
    {
        $teamsArray = $teams->toArray();
        $totalTeams = count($teamsArray);
        $changedTeams = [];

        $subGroups = [];

        for ($i = 0; $i < $subgroupCount; $i++) {
            $subGroups[] = new Group([
                'category_id' => $category->id,
                'name' => chr(65 + $i),
            ]);
        }

        // If not enough teams or subgroups, just return empty
        if ($totalTeams < 2 || $subgroupCount < 1) {
            return [];
        }

        // Shuffle if you want random group assignment; optional.
        shuffle($teamsArray);

        // Figure out how many teams per group
        // (We use ceil so that all teams end up in some group, 
        // but you can distribute differently if you want.)
        $teamsPerGroup = ceil($totalTeams / $subgroupCount);

        $games = [];
        $offset = 0;
        $gamesBySubgroup = [];

        // For each subgroup, slice out its chunk of teams and do round-robin
        for ($g = 0; $g < $subgroupCount; $g++) {
            $subgroupTeams = array_slice($teamsArray, $offset, $teamsPerGroup);
            $offset += $teamsPerGroup;

            // Reuse the round-robin logic on just this subgroup
            if (count($subgroupTeams) >= 2) {
                // You could directly replicate the round-robin code,
                // or reuse createGamesByCategory if you want to unify logic.
                /* $games = array_merge(
                    $games,
                    $this->createRoundRobinForSubgroup($category, $subgroupTeams)
                ); */
                $gamesBySubgroup[] = $this->createRoundRobinForSubgroup($category, $subgroupTeams);
                foreach ($subgroupTeams as $team) {
                    $tempTeam = Team::find($team['id']);
                    $tempTeam->group = $subGroups[$g];
                    $changedTeams[] = $tempTeam;
                }
            } else {
                return [];
            }
        }
        $maxGames = max(array_map('count', $gamesBySubgroup));
        for ($i = 0; $i < $maxGames; $i++) {
            foreach ($gamesBySubgroup as $subgroupGames) {
                if (isset($subgroupGames[$i])) {
                    $games[] = $subgroupGames[$i];
                }
            }
        }


        return [$games, $subGroups, $changedTeams];
    }

    /**
     * Helper to generate round-robin matches for a *subgroup array*.
     * This is the same logic as createGamesByCategory, but operates on an array
     * of teams (rather than a collection), and doesn't do the DB query again.
     */
    private function createRoundRobinForSubgroup($category, array $teams)
    {
        $teamCount = count($teams);

        // If odd, add a null "bye"
        if ($teamCount % 2 !== 0) {
            $teams[] = null;
            $teamCount++;
        }

        $games = [];
        for ($round = 0; $round < $teamCount - 1; $round++) {
            for ($i = 0; $i < $teamCount / 2; $i++) {
                $teamA = $teams[$i];
                $teamB = $teams[$teamCount - 1 - $i];

                // If neither is null (bye), create a game
                if ($teamA !== null && $teamB !== null) {
                    $games[] = new Game([
                        'category_id' => $category->id,
                        'team_a_id' => $teamA['id'],
                        'team_b_id' => $teamB['id'],
                    ]);
                }
            }
            // Rotate
            $lastTeam = array_pop($teams);
            array_splice($teams, 1, 0, [$lastTeam]);
        }

        return $games;
    }



    public function calculateTournamentInfos(Request $request)
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
        $options = Option::first();
        $options->start_time = Carbon::createFromFormat('H:i', $validated['start_time']);
        $options->game_duration = $validated['game_duration'];
        $options->break_duration = $validated['break_duration'];
        $options->round_robin = $validated['round_robin'];
        $options->group_phase = $validated['group_phase'];
        $options->play_for_third_place = $validated['play_for_third_place'];
        $options->save();
        foreach ($validated['groups_per_category'] as $category_id => $subgroup_count) {
            $category = Category::find($category_id);
            $category->subgroups = $subgroup_count;
            $category->save();
        }

        [$gameList, $timeslots, $allSubGroups, $allChangedTeams] = $this->scheduleAllGames($validated['round_robin'], $validated['group_phase'], $validated['groups_per_category']);

        $lastGameTime = $gameList[count($gameList) - 1]->timeslot->end_time;

        return response()->json([
            'game_count' => count($gameList),
            'last_game_time' => $lastGameTime->format('H:i'),
        ]);


    }

    private function getTable($userId = null, $teamId = null)
    {


        // Fetch all halls and timeslots
        $halls = Hall::all();
        $timeslots = Timeslot::all();

        // Initialize groupedGames with empty arrays for all hall-timeslot combinations
        $groupedGames = [];
        foreach ($timeslots as $timeslot) {
            $formattedTimeslot = $timeslot->start_time->format('H:i') . " - " . $timeslot->end_time->format('H:i');
            $groupedGames[$formattedTimeslot] = [];

            foreach ($halls as $hall) {
                $groupedGames[$formattedTimeslot][$hall->name] = ['games' => [], 'slot_info' => ['hall_id' => $hall->id, 'hall_name' => $hall->name, 'timeslot_id' => $timeslot->id, 'has_games' => false]];
            }
        }

        // Fetch games and populate the array
        $query = Game::with('teamA', 'teamB', 'hall', 'timeslot', 'category');

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
            $timeslot = $game->timeslot->start_time->format('H:i') . " - " . $game->timeslot->end_time->format('H:i');
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
        $userId = $request->user("api")->id;
        $groupedGames = $this->getTable($userId);

        return response()->json($groupedGames);

    }

    public function getTeamTable(Request $request)
    {
        $userId = $request->user("api")->id;
        $userTeamId = User::find($userId)->team_id;
        $groupedGames = $this->getTable(null, $userTeamId);

        return response()->json($groupedGames);

    }
    public function createAllFinaleGames()
    {
        $categories = Category::all();
        foreach ($categories as $category) {
            $this->createFinaleGames($category->id);
        }
        return response()->json(['message' => 'All finale games created']);
    }

    public function createFinaleGames($category_id)
    {
        $options = Option::first();
        $category = Category::find($category_id);
        $teams = Team::where('category_id', $category_id)->get();
        $games = Game::where('played', false)->where('category_id', $category_id)->get();
        if (count($games) > 0) {
            return response()->json(['error' => 'There are still games to be played'], status: 400);
        }

        if ($options->round_robin || ($options->group_phase && $category->subgroups == 1)) {
            $ranking = $this->rankTeams($teams);
            if ($options->play_for_third_place) {
                [$thirdPlaceTimeslot, $hall] = $this->getAvailableTimeSlotAndHall();
                Game::create([
                    'category_id' => $category_id,
                    'team_a_id' => $ranking[2]['team']->id,
                    'team_b_id' => $ranking[3]['team']->id,
                    'timeslot_id' => $thirdPlaceTimeslot->id,
                    'hall_id' => $hall->id,
                    'finale_type' => 1,
                ]);
            }
            [$finaleTimeslot, $hall] = $this->getAvailableTimeSlotAndHall();
            Game::create([
                'category_id' => $category_id,
                'team_a_id' => $ranking[0]['team']->id,
                'team_b_id' => $ranking[1]['team']->id,
                'timeslot_id' => $finaleTimeslot->id,
                'hall_id' => $hall->id,
                'finale_type' => 1,
            ]);
        } else {
            $minFinaleType = Game::whereNotNull('finale_type')
                ->where('category_id', $category_id)
                ->min('finale_type');
            if ($minFinaleType) {
                $lastFinalGames = Game::where('finale_type', $minFinaleType)
                    ->where('category_id', $category_id)
                    ->get();
            } else {
                $lastFinalGames = [];
            }
            if ($minFinaleType == 1) {
                return response()->json(['error' => 'Final games already created'], status: 400);
            }
            $newFinalType = count($teams) / 2;
            if (count($lastFinalGames) > 0) {
                $newFinalType = $lastFinalGames[0]->finale_type / 2;
                $lastTimeslot = Timeslot::orderBy('end_time', 'desc')->first();
                for ($i = 0; $i < count($lastFinalGames); $i += 2) {
                    $finaleTimeslot = Timeslot::create([
                        'start_time' => $lastTimeslot->end_time->copy()->addMinutes($options->break_duration),
                        'end_time' => $lastTimeslot->end_time->copy()->addMinutes($options->break_duration)->addMinutes($options->game_duration),
                    ]);
                    Game::create([
                        'category_id' => $category_id,
                        'team_a_id' => $lastFinalGames[$i]->points_team_a > $lastFinalGames[$i]->points_team_b ? $lastFinalGames[$i]->team_a_id : $lastFinalGames[$i]->team_b_id,
                        'team_b_id' => $lastFinalGames[$i + 1]->points_team_a > $lastFinalGames[$i + 1]->points_team_b ? $lastFinalGames[$i + 1]->team_a_id : $lastFinalGames[$i + 1]->team_b_id,
                        'timeslot_id' => $finaleTimeslot->id,
                        'hall_id' => Hall::first()->id,
                        'finale_type' => $newFinalType,
                    ]);

                }
            } else {
                $groups = Group::where('category_id', $category_id)->get();
                $finaleType = count($groups);
                error_log("right place");

                for ($i = 0; $i < count($groups); $i += 2) {
                    $firstGroupFirstTeam = Team::with('section')->where('group_id', $groups[$i]->id)->first();
                    $secondGroupFirstTeam = Team::with('section')->where('group_id', $groups[$i + 1]->id)->first();
                    $firstGroupSeccodTeam = Team::with('section')->where('group_id', $groups[$i]->id)->skip(1)->first();
                    $secondGroupSecondTeam = Team::with('section')->where('group_id', $groups[$i + 1]->id)->skip(1)->first();
                    [$timeslot, $hall] = $this->getAvailableTimeSlotAndHall();
                    Game::create([
                        'category_id' => $category_id,
                        'team_a_id' => $firstGroupFirstTeam->id,
                        'team_b_id' => $secondGroupSecondTeam->id,
                        'points_team_a' => 0,
                        'points_team_b' => 0,
                        'timeslot_id' => $timeslot->id,
                        'hall_id' => $hall->id,
                        'finale_type' => $finaleType,
                    ]);
                    [$timeslot, $hall] = $this->getAvailableTimeSlotAndHall();
                    Game::create([
                        'category_id' => $category_id,
                        'team_a_id' => $secondGroupFirstTeam->id,
                        'team_b_id' => $firstGroupSeccodTeam->id,
                        'points_team_a' => 0,
                        'points_team_b' => 0,
                        'timeslot_id' => $timeslot->id,
                        'hall_id' => $hall->id,
                        'finale_type' => $finaleType,
                    ]);
                }

            }

        }

    }

    private function getAvailableTimeSlotAndHall()
    {
        $lastTimeSlot = Timeslot::orderBy('end_time', 'desc')->first();
        $halls = Hall::all();
        $playedGamesInTimeSlotCount = Game::where('timeslot_id', $lastTimeSlot->id)->where('played', true)->count();
        if ($playedGamesInTimeSlotCount == 0) {

            foreach ($halls as $hall) {
                $game = Game::where('hall_id', $hall->id)->where('timeslot_id', $lastTimeSlot->id)->first();
                if (!$game) {
                    return [$lastTimeSlot, $hall];
                }
            }
        }
        $newTimeSlot = Timeslot::create([
            'start_time' => $lastTimeSlot->end_time->copy()->addMinutes(30),
            'end_time' => $lastTimeSlot->end_time->copy()->addMinutes(30)->addMinutes(30),
        ]);
        return [$newTimeSlot, $halls[0]];
    }

    public function getRanking()
    {
        $categories = Category::all();
        $ranking = [];
        foreach ($categories as $category) {
            $group = Group::where('category_id', $category->id)->get();
            $currentRanking = ['category_name' => $category->name, 'groups' => []];
            if (count($group) > 0) {
                foreach ($group as $subgroup) {
                    $teams = Team::with('section')->where('group_id', $subgroup->id)->get();
                    $currentGroupRanking = $this->rankTeams($teams);
                    $currentRanking['groups'][] = ['group_name' => $subgroup->name, 'ranking' => $currentGroupRanking];
                }
            } else {
                $teams = Team::with('section')->where('category_id', $category->id)->get();
                $currentGroupRanking = $this->rankTeams($teams);
                $currentRanking['groups'][] = ['group_name' => $category->name, 'ranking' => $currentGroupRanking];
            }
            $ranking[] = $currentRanking;

        }
        return response()->json($ranking);
    }

    private function rankTeams($teams)
    {
        $teamsRanking = [];
        foreach ($teams as $team) {
            $goalsConceded = 0;
            $goalsScored = 0;
            $gamesWon = 0;
            $gamesLost = 0;
            $gamesDrawn = 0;
            $team->points = 0;
            $games = Game::where('played', true)->where(function ($query) use ($team) {
                $query->where('team_a_id', $team->id)->orWhere('team_b_id', $team->id);
            })->get();
            foreach ($games as $game) {
                if ($game->team_a_id == $team->id) {
                    $goalsScored += $game->points_team_a;
                    $goalsConceded += $game->points_team_b;
                    if ($game->points_team_a > $game->points_team_b) {
                        $team->points += 3;
                        $gamesWon++;
                    } elseif ($game->points_team_a == $game->points_team_b) {
                        $team->points += 1;
                        $gamesDrawn++;
                    } else {
                        $team->points += 0;
                        $gamesLost++;
                    }
                } else {
                    $goalsScored += $game->points_team_b;
                    $goalsConceded += $game->points_team_a;
                    if ($game->points_team_b > $game->points_team_a) {
                        $team->points += 3;
                        $gamesWon++;
                    } elseif ($game->points_team_a == $game->points_team_b) {
                        $team->points += 1;
                        $gamesDrawn++;
                    } else {
                        $team->points += 0;
                        $gamesLost++;
                    }
                }
            }
            $goalDifference = $goalsScored - $goalsConceded;
            $teamsRanking[] = [
                'rank' => 0,
                'team' => $team,
                'matches_played' => count($games),
                'wins' => $gamesWon,
                'draws' => $gamesDrawn,
                'losses' => $gamesLost,
                'goals_scored' => $goalsScored,
                'goals_conceded' => $goalsConceded,
                'goals_difference' => $goalDifference,
                'points' => $team->points,
            ];


        }

        usort($teamsRanking, function ($a, $b) {
            if ($a['points'] == $b['points']) {
                if ($a['goals_conceded'] == $b['goals_conceded']) {
                    return $b['goals_scored'] - $a['goals_scored'];
                }
                return $b['goals_conceded'] - $a['goals_conceded'];
            }
            return $b['points'] - $a['points'];
        });

        $rank = 1;
        foreach ($teamsRanking as $key => $team) {
            $teamsRanking[$key]['rank'] = $rank;
            $rank++;
        }

        return $teamsRanking;
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


}
